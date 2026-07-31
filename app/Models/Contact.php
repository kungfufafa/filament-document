<?php

namespace App\Models;

use App\Enums\ContactVisibility;
use Database\Factories\ContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contact extends Model
{
    /** @use HasFactory<ContactFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'job_title',
        'company',
        'phone',
        'address',
        'visibility',
    ];

    protected function casts(): array
    {
        return [
            'visibility' => ContactVisibility::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(ContactGroup::class)->withTimestamps();
    }
}
