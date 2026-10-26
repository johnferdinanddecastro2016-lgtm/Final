<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\User;

class ProgramPolicy
{
    public function view(User $user, Program $program): bool
    {
        return $program->is_public && $program->status === 'published'
            || $this->manage($user, $program);
    }

    public function create(User $user): bool
    {
        return $user->isWorkspaceMember()
            && $user->hasVerifiedEmail()
            && $user->account_status === 'active'
            && $user->workspace?->status === 'active';
    }

    public function update(User $user, Program $program): bool
    {
        return $this->manage($user, $program);
    }

    public function delete(User $user, Program $program): bool
    {
        return $this->manage($user, $program);
    }

    private function manage(User $user, Program $program): bool
    {
        return $user->isWorkspaceMember()
            && $user->hasVerifiedEmail()
            && $user->account_status === 'active'
            && $user->workspace?->status === 'active'
            && $user->workspace_id === $program->workspace_id;
    }
}
