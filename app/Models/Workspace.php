<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workspace extends Model
{
    protected $fillable = ['council_name', 'province', 'municipality', 'barangay', 'status', 'council_identifier'];

    protected static function booted(): void
    {
        static::creating(function (Workspace $workspace) {
            $workspace->council_identifier ??= self::identifierFor(
                $workspace->province,
                $workspace->municipality,
                $workspace->barangay
            );
        });
    }

    public static function identifierFor(string $province, string $municipality, string $barangay): string
    {
        $parts = array_map(function (string $part) {
            $normalized = mb_strtolower(trim($part));

            return preg_replace('/\s+/u', ' ', $normalized) ?? $normalized;
        }, [$province, $municipality, $barangay]);

        return hash('sha256', implode('|', $parts));
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class);
    }
}
