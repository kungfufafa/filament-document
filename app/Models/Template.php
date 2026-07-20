<?php

namespace App\Models;

use App\Enums\TemplateStatus;
use Database\Factories\TemplateFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    /** @use HasFactory<TemplateFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'folder_id',
        'title',
        'file_path',
        'original_filename',
        'mime_type',
        'page_count',
        'sequential_signing',
        'recipient_roles',
        'fields',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'sequential_signing' => 'boolean',
            'recipient_roles' => 'array',
            'fields' => 'array',
            'page_count' => 'integer',
            'status' => TemplateStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }
}
