<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'workspace_id', 'title', 'slug', 'body', 'image_path', 'status', 'is_public', 'published_at',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'is_public' => 'boolean'];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
}
