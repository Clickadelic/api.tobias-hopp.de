<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TeamController extends Controller
{
	public function index(Request $request)
	{
		return response()->json(
			$request->user()->teams()->latest('teams.created_at')->get()
		);
	}

	public function store(Request $request)
	{
		Gate::authorize('create', Team::class);

		$data = $request->validate(['name' => ['required', 'string', 'max:255']]);

		$team = DB::transaction(function () use ($data, $request) {
			$team = Team::create($data);
			$team->users()->attach($request->user()->getKey(), ['role' => Team::ROLE_OWNER]);

			return $team;
		});

		return response()->json($team, 201);
	}

	public function show(Team $team)
	{
		Gate::authorize('view', $team);

		return response()->json($team->load('users:id,name'));
	}

	public function update(Request $request, Team $team)
	{
		Gate::authorize('update', $team);

		$team->update($request->validate(['name' => ['required', 'string', 'max:255']]));

		return response()->json($team);
	}

	public function destroy(Team $team)
	{
		Gate::authorize('delete', $team);

		$team->delete();

		return response()->json(null, 204);
	}
}
