<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Workspace;
use Illuminate\Support\Facades\Gate;
use App\Models\Team;
use App\Http\Requests\StoreWorkspaceRequest;
use App\Http\Requests\UpdateWorkspaceRequest;

class WorkspaceController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		$perPage = min(max((int) $request->query('per_page', 10), 1), 50);
		$workspaces = Workspace::query()
			->whereIn('team_id', $request->user()->teams()->select('teams.id'))
			->when($request->query('team_id'), fn ($q, $teamId) => $q->where('team_id', $teamId))
			->latest()
			->paginate($perPage)
			->withQueryString();

		return response()->json($workspaces);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreWorkspaceRequest $request)
	{
		$team = Team::findOrFail($request->validated('team_id'));
		Gate::authorize('createWorkspace', $team);

		$workspace = Workspace::create($request->validated());

		return response()->json($workspace, 201);
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Workspace $workspace)
	{
		Gate::authorize('view', $workspace);

		return response()->json($workspace);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Workspace $workspace)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(UpdateWorkspaceRequest $request, Workspace $workspace)
	{
		Gate::authorize('update', $workspace);

		$workspace->update($request->validated());

		return response()->json($workspace);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Workspace $workspace)
	{
		Gate::authorize('delete', $workspace);

		$workspace->delete();

		return response()->json(null, 204);
	}
}
