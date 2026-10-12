<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;

class WorkspacePolicy
{
	public function viewAny(User $user): bool
	{
		return true;
	}

	public function view(User $user, Workspace $workspace): bool
	{
		return $workspace->team->hasMember($user);
	}

	public function update(User $user, Workspace $workspace): bool
	{
		return $workspace->team->hasMember($user);
	}

	public function delete(User $user, Workspace $workspace): bool
	{
		return $workspace->team->isOwnedBy($user);
	}
}
