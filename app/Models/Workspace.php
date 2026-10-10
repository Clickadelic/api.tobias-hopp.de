<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Relations\HasMany;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Illuminate\Database\Eloquent\Seeders\WorkspaceSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Workspace extends Model
{
	/** @use HasFactory<\Database\Factories\WorkspaceFactory> */
	use HasFactory;
	use HasUuids;

	protected $table = "workspaces";

	protected $primaryKey = 'uuid';

	protected $fillable = [
		'name',
		'slug',
		'description'
	];
}
