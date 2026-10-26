<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $fillable = [
        'workspace_id', 'title', 'slug', 'description', 'category', 'objectives', 'venue',
        'starts_at', 'ends_at', 'registration_deadline', 'capacity', 'image_path',
        'requirements', 'status', 'is_public',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'registration_deadline' => 'datetime',
            'is_public' => 'boolean',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(ProgramRegistration::class);
    }

    public function activeRegistrations(): HasMany
    {
        return $this->registrations()->whereIn('status', ['pending', 'approved']);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
