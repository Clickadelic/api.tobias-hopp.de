<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TeamMemberController extends Controller
{
	public function transferOwnership(Request $request, Team $team)
	{
		Gate::authorize('manageMembers', $team);

		$data = $request->validate(['user_id' => ['required', 'uuid']]);
		$target = $team->users()->whereKey($data['user_id'])->first();

		if (!$target || $target->is($request->user())) {
			throw ValidationException::withMessages(['user_id' => 'Choose another member of this team.']);
		}

		DB::transaction(function () use ($team, $target, $request) {
			$team->users()->updateExistingPivot($target->getKey(), ['role' => Team::ROLE_OWNER]);
			$team->users()->updateExistingPivot($request->user()->getKey(), ['role' => Team::ROLE_MEMBER]);
		});

		return response()->json($team->load('users:id,name'));
	}

	public function update(Request $request, Team $team, User $user)
	{
		Gate::authorize('manageMembers', $team);

		$data = $request->validate([
			'role' => ['required', Rule::in([Team::ROLE_OWNER, Team::ROLE_MEMBER])],
		]);

		abort_unless($team->hasMember($user), 404);
		$this->guardLastOwner($team, $user, $data['role']);

		$team->users()->updateExistingPivot($user->getKey(), ['role' => $data['role']]);

		return response()->json($team->load('users:id,name'));
	}

	public function destroy(Request $request, Team $team, User $user)
	{
		$isSelf = $request->user()->is($user);

		if (!$isSelf) {
			Gate::authorize('manageMembers', $team);
		}

		abort_unless($team->hasMember($user), 404);
		$this->guardLastOwner($team, $user, Team::ROLE_MEMBER);

		$team->users()->detach($user->getKey());

		return response()->json(null, 204);
	}

	private function guardLastOwner(Team $team, User $user, string $newRole): void
	{
		if ($newRole === Team::ROLE_OWNER || !$team->isOwnedBy($user)) {
			return;
		}

		$ownerCount = $team->users()->wherePivot('role', Team::ROLE_OWNER)->count();

		if ($ownerCount <= 1) {
			throw ValidationException::withMessages(['role' => 'A team must keep at least one owner.']);
		}
	}
}
