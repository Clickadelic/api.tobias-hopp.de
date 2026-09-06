<?php

// Eigenen Namespace deklarieren
namespace App\Http\Controllers\Api;

// Verwendung der benötigten Controller-Klasse als Basis bzw. deren Verwendung
use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Http\Requests\StoreContactSubmissionRequest;
use App\Http\Requests\UpdateContactSubmissionRequest;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactSubmissionMail;
use Illuminate\Http\Request;

class ContactSubmissionController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		$perPage = min(max((int) $request->query('per_page', 10), 1), 50);
		$contacts = ContactSubmission::query()
			->latest()
			->paginate($perPage)
			->withQueryString();

		return response()->json($contacts);
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
	public function store(StoreContactSubmissionRequest $request)
	{
		ContactSubmission::create($request->validated());
		Mail::to(config('mail.from.address'))
			->queue(new ContactSubmissionMail());

		return response()->json("OK", 201);
	}

	/**
	 * Display the specified resource.
	 */
	public function show(ContactSubmission $contactSubmission)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(ContactSubmission $contactSubmission)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(UpdateContactSubmissionRequest $request, ContactSubmission $contactSubmission)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(ContactSubmission $contactSubmission)
	{
		//
	}

	public function sendEmail(ContactSubmission $contactSubmission)
	{
		Mail::to(config('mail.from.address'))
			->queue(new ContactSubmissionMail($contactSubmission));
	}
}
