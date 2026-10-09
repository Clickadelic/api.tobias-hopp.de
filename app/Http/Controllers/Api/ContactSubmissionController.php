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
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Contact Submissions", description: "Contact form submissions. Listing and deleting require the admin role.")]
class ContactSubmissionController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	#[OA\Get(
		path: "/api/v1/contact-submissions",
		summary: "List contact submissions (admin)",
		security: [["sanctum" => []]],
		tags: ["Contact Submissions"],
		parameters: [
			new OA\Parameter(name: "per_page", in: "query", description: "Items per page (clamped to 1-50).", schema: new OA\Schema(type: "integer", minimum: 1, maximum: 50, default: 10)),
			new OA\Parameter(name: "page", in: "query", description: "Page number.", schema: new OA\Schema(type: "integer", minimum: 1, default: 1)),
		],
		responses: [
			new OA\Response(
				response: 200,
				description: "Paginated submissions, newest first",
				content: new OA\JsonContent(properties: [
					new OA\Property(property: "current_page", type: "integer"),
					new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/ContactSubmission")),
					new OA\Property(property: "per_page", type: "integer"),
					new OA\Property(property: "total", type: "integer"),
					new OA\Property(property: "last_page", type: "integer"),
				])
			),
			new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/Message")),
			new OA\Response(response: 403, description: "Not an admin or email not verified"),
		]
	)]
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
	#[OA\Post(
		path: "/api/v1/contact-submissions",
		summary: "Submit the contact form",
		description: "Public endpoint. Stores the submission and queues a notification email.",
		tags: ["Contact Submissions"],
		requestBody: new OA\RequestBody(
			required: true,
			content: new OA\JsonContent(
				required: ["name", "email", "subject", "message"],
				properties: [
					new OA\Property(property: "name", type: "string", maxLength: 255),
					new OA\Property(property: "phone", type: "string", maxLength: 255, nullable: true),
					new OA\Property(property: "email", type: "string", format: "email", maxLength: 255),
					new OA\Property(property: "subject", type: "string", maxLength: 255),
					new OA\Property(property: "message", type: "string"),
				]
			)
		),
		responses: [
			new OA\Response(response: 201, description: "Submission stored", content: new OA\JsonContent(type: "string", example: "OK")),
			new OA\Response(response: 422, description: "Validation error", content: new OA\JsonContent(ref: "#/components/schemas/ValidationError")),
		]
	)]
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
	#[OA\Delete(
		path: "/api/v1/contact-submissions/{contactSubmission}",
		summary: "Delete a contact submission (admin)",
		security: [["sanctum" => []]],
		tags: ["Contact Submissions"],
		parameters: [
			new OA\Parameter(name: "contactSubmission", in: "path", required: true, description: "Contact submission ID", schema: new OA\Schema(type: "integer")),
		],
		responses: [
			new OA\Response(response: 200, description: "Deleted", content: new OA\JsonContent(type: "string", example: "Deleted")),
			new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/Message")),
			new OA\Response(response: 403, description: "Not an admin or email not verified"),
			new OA\Response(response: 404, description: "Submission not found"),
		]
	)]
	public function destroy(ContactSubmission $contactSubmission)
	{
		$contactSubmission->delete();
		return response()->json("Deleted", 200);
	}

	public function sendEmail(ContactSubmission $contactSubmission)
	{
		Mail::to(config('mail.from.address'))
			->queue(new ContactSubmissionMail($contactSubmission));

		return response()->json("Email Sent", 200);
	}
}
