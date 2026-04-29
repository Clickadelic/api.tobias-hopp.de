<?php

namespace App\Http\Controllers\Api;

use App\Models\Domain;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDomainRequest;
use App\Http\Requests\UpdateDomainRequest;

class DomainController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		$domains = Domain::all();
		return response()->json($domains);
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
	public function store(StoreDomainRequest $request)
	{

		$validated = $request->validated();

		$domain = Domain::updateOrCreate(
			['domain' => $validated['domain']],
			$validated
		);

		return response()->json([
			'data' => $domain,
			'created' => $domain->wasRecentlyCreated,
		], $domain->wasRecentlyCreated ? 201 : 200);
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Domain $domain)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Domain $domain)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(UpdateDomainRequest $request, Domain $domain)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Domain $domain)
	{
		//
	}

	public function bulkStore(StoreDomainRequest $request)
	{
		$validated = $request->validated();

		$results = [];
		foreach ($validated as $domainData) {
			// if ($domainData['score'] < 50) {
			// 	return response()->json(['message' => 'Ignored'], 202);
			// }
			$domain = Domain::updateOrCreate(
				['domain' => $domainData['domain']],
				$domainData
			);

			$results[] = [
				'data' => $domain,
				'created' => $domain->wasRecentlyCreated,
			];
		}

		return response()->json($results, 200);
	}
}
