<?php

namespace App\Console\Commands;

use App\Enums\EmployeeStatus;
use App\Enums\Gender;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Group;
use App\Models\JobLevel;
use App\Models\JobPosition;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

#[Signature('employees:import-talenta {path : Absolute or relative path to Talenta JSON export} {--dry-run : Preview counts without writing}')]
#[Description('Import employees and organization masters from a Talenta list-employee JSON export')]
class ImportTalentaEmployeesCommand extends Command
{
    public function handle(): int
    {
        $path = $this->argument('path');

        if (! is_file($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        try {
            /** @var array<string, mixed> $payload */
            $payload = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            $this->error('Invalid JSON: '.$exception->getMessage());

            return self::FAILURE;
        }

        $rows = data_get($payload, 'data.data');

        if (! is_array($rows)) {
            $this->error('Unexpected JSON shape: expected data.data to be an array.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $created = ['companies' => 0, 'organizations' => 0, 'positions' => 0, 'levels' => 0, 'employees' => 0];
        $updatedEmployees = 0;

        $run = function () use ($rows, &$created, &$updatedEmployees): void {
            $group = Group::query()->firstOrCreate(
                ['name' => 'Complete Selular'],
                ['code' => 'CS-GROUP'],
            );

            foreach ($rows as $row) {
                if (! is_array($row)) {
                    continue;
                }

                $companyName = trim((string) ($row['branch'] ?? 'Unknown Company'));
                $company = Company::query()->firstOrCreate(
                    ['name' => $companyName],
                    ['group_id' => $group->id, 'is_active' => true],
                );

                if ($company->wasRecentlyCreated) {
                    $created['companies']++;
                }

                $organization = null;
                $organizationName = trim((string) ($row['organization'] ?? ''));

                if ($organizationName !== '') {
                    $organization = Organization::query()->firstOrCreate(
                        ['company_id' => $company->id, 'name' => $organizationName],
                    );

                    if ($organization->wasRecentlyCreated) {
                        $created['organizations']++;
                    }
                }

                $position = null;
                $jobName = trim((string) ($row['job'] ?? ''));

                if ($jobName !== '') {
                    $position = JobPosition::query()->firstOrCreate(
                        ['company_id' => $company->id, 'name' => $jobName],
                    );

                    if ($position->wasRecentlyCreated) {
                        $created['positions']++;
                    }
                }

                $level = null;
                $title = trim((string) ($row['title'] ?? ''));

                if ($title !== '') {
                    $level = JobLevel::query()->firstOrCreate(
                        ['name' => strtoupper($title)],
                        ['code' => null, 'sort_order' => 0],
                    );

                    if ($level->wasRecentlyCreated) {
                        $created['levels']++;
                    }
                }

                $employeeCode = trim((string) ($row['id_employee'] ?? ''));

                if ($employeeCode === '') {
                    continue;
                }

                $attributes = [
                    'external_talenta_id' => isset($row['id']) ? (string) $row['id'] : null,
                    'first_name' => trim((string) ($row['first_name'] ?? 'Unknown')),
                    'last_name' => trim((string) ($row['last_name'] ?? '')) ?: null,
                    'email' => $row['email'] ?? null,
                    'mobile_phone' => $row['mobile_phone'] ?? null,
                    'phone' => $row['phone'] ?? null,
                    'gender' => Gender::fromTalenta(isset($row['gender']) ? (string) $row['gender'] : null),
                    'birth_date' => $this->parseDate($row['birth_date'] ?? null),
                    'marital_status' => $row['marital_status'] ?? null,
                    'religion' => $row['religion'] ?? null,
                    'blood_type' => $row['blood_type'] ?? null,
                    'tax_status' => $row['tax_status'] ?? null,
                    'address' => $row['address'] ?? null,
                    'current_address' => $row['current_address'] ?? null,
                    'avatar_path' => $row['avatar'] ?? null,
                    'company_id' => $company->id,
                    'organization_id' => $organization?->id,
                    'job_position_id' => $position?->id,
                    'job_level_id' => $level?->id,
                    'branch_name' => $companyName,
                    'join_date' => $this->parseDate($row['join_date'] ?? null),
                    'status' => EmployeeStatus::Active,
                ];

                $employee = Employee::query()->where('employee_code', $employeeCode)->first();

                if ($employee === null) {
                    Employee::query()->create([
                        'employee_code' => $employeeCode,
                        ...$attributes,
                    ]);
                    $created['employees']++;
                } else {
                    $employee->update($attributes);
                    $updatedEmployees++;
                }
            }
        };

        if ($dryRun) {
            DB::beginTransaction();

            try {
                $run();
            } finally {
                DB::rollBack();
            }

            $this->components->info('Dry run complete (no changes were saved).');
        } else {
            DB::transaction($run);
            $this->components->info('Import complete.');
        }

        $this->table(
            ['Metric', 'Count'],
            [
                ['Companies created', $created['companies']],
                ['Organizations created', $created['organizations']],
                ['Job positions created', $created['positions']],
                ['Job levels created', $created['levels']],
                ['Employees created', $created['employees']],
                ['Employees updated', $updatedEmployees],
                ['Rows in file', count($rows)],
            ],
        );

        return self::SUCCESS;
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Carbon::parse((string) $value)->toDateString();
        } catch (Throwable) {
            return null;
        }
    }
}
