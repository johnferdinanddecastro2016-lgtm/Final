<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramRegistration extends Model
{
    protected $fillable = ['program_id', 'workspace_id', 'user_id', 'name', 'email', 'status'];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
