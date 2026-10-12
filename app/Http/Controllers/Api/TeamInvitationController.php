<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\TeamInvitationMail;
use App\Models\Team;
use App\Models\TeamInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TeamInvitationController extends Controller
{
	private const EXPIRES_IN_DAYS = 7;

	public function index(Team $team)
	{
		Gate::authorize('manageMembers', $team);

		return response()->json(
			$team->invitations()->where('expires_at', '>', now())->latest()->get()
		);
	}

	public function store(Request $request, Team $team)
	{
		Gate::authorize('manageMembers', $team);

		$data = $request->validate([
			'email' => ['required', 'email', 'max:255'],
			'role' => ['sometimes', Rule::in([Team::ROLE_OWNER, Team::ROLE_MEMBER])],
		]);
		$email = Str::lower($data['email']);

		if ($team->users()->whereRaw('lower(email) = ?', [$email])->exists()) {
			throw ValidationException::withMessages(['email' => 'This person is already a member of the team.']);
		}

		$plainToken = Str::random(48);

		$invitation = DB::transaction(function () use ($team, $email, $data, $request, $plainToken) {
			$team->invitations()->where('email', $email)->delete();

			return $team->invitations()->create([
				'email' => $email,
				'role' => $data['role'] ?? Team::ROLE_MEMBER,
				'token' => hash('sha256', $plainToken),
				'invited_by' => $request->user()->getKey(),
				'expires_at' => now()->addDays(self::EXPIRES_IN_DAYS),
			]);
		});

		Mail::to($email)->send(new TeamInvitationMail($team, $request->user()->name, $plainToken));

		return response()->json($invitation, 201);
	}

	public function destroy(Team $team, TeamInvitation $invitation)
	{
		Gate::authorize('manageMembers', $team);
		abort_unless($invitation->team_id === $team->id, 404);

		$invitation->delete();

		return response()->json(null, 204);
	}

	public function accept(Request $request)
	{
		$data = $request->validate(['token' => ['required', 'string']]);
		$user = $request->user();

		$invitation = TeamInvitation::where('token', hash('sha256', $data['token']))->first();

		if (!$invitation || $invitation->isExpired() || Str::lower($user->email) !== $invitation->email) {
			throw ValidationException::withMessages(['token' => 'This invitation is invalid or has expired.']);
		}

		DB::transaction(function () use ($invitation, $user) {
			$team = $invitation->team;

			if (!$team->hasMember($user)) {
				$team->users()->attach($user->getKey(), ['role' => $invitation->role]);
			}

			$invitation->delete();
		});

		return response()->json($invitation->team->load('users:id,name'));
	}
}
