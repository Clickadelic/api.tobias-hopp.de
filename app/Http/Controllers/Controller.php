<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
	version: "1.0.0",
	title: "Toby's REST-API",
	description: "<a href=\"/\" style=\"display: inline-block; padding: 8px 16px; background: #3f51b5; color: white; border-radius: 4px; text-decoration: none; font-weight: bold;\">← Back to homepage</a>"
)]
#[OA\SecurityScheme(
	securityScheme: "sanctum",
	type: "http",
	scheme: "bearer",
	description: "Sanctum API token returned by /api/auth/login or /api/auth/register."
)]
#[OA\Schema(
	schema: "User",
	properties: [
		new OA\Property(property: "id", type: "integer", example: 1),
		new OA\Property(property: "name", type: "string", example: "Jane Doe"),
		new OA\Property(property: "email", type: "string", format: "email", example: "jane@example.com"),
		new OA\Property(property: "email_verified_at", type: "string", format: "date-time", nullable: true),
		new OA\Property(property: "roles", type: "array", items: new OA\Items(type: "string"), example: ["user"]),
	]
)]
#[OA\Schema(
	schema: "Message",
	properties: [new OA\Property(property: "message", type: "string")]
)]
#[OA\Schema(
	schema: "ValidationError",
	properties: [
		new OA\Property(property: "message", type: "string", example: "The email field is required."),
		new OA\Property(
			property: "errors",
			type: "object",
			additionalProperties: new OA\AdditionalProperties(type: "array", items: new OA\Items(type: "string"))
		),
	]
)]
#[OA\Schema(
	schema: "ContactSubmission",
	properties: [
		new OA\Property(property: "id", type: "integer", example: 1),
		new OA\Property(property: "name", type: "string"),
		new OA\Property(property: "phone", type: "string", nullable: true),
		new OA\Property(property: "email", type: "string", format: "email"),
		new OA\Property(property: "subject", type: "string"),
		new OA\Property(property: "message", type: "string"),
		new OA\Property(property: "created_at", type: "string", format: "date-time"),
		new OA\Property(property: "updated_at", type: "string", format: "date-time"),
	]
)]
#[OA\Schema(
	schema: "UnsplashImageResponse",
	properties: [
		new OA\Property(property: "url", type: "string", format: "uri"),
		new OA\Property(property: "photo", type: "object", description: "Unsplash photo payload (includes user for attribution)."),
		new OA\Property(property: "season", type: "string", enum: ["spring", "summer", "autumn", "winter"], description: "Only on the seasonal endpoint."),
	]
)]
#[OA\Get(
	path: "/api/v1/me",
	summary: "Get the authenticated user",
	security: [["sanctum" => []]],
	tags: ["User"],
	responses: [
		new OA\Response(response: 200, description: "The current user", content: new OA\JsonContent(ref: "#/components/schemas/User")),
		new OA\Response(response: 401, description: "Unauthenticated", content: new OA\JsonContent(ref: "#/components/schemas/Message")),
		new OA\Response(response: 403, description: "Email address not verified"),
	]
)]
abstract class Controller
{
	//
}
