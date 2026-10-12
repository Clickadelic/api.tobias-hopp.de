<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSlug;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Workspace extends Model
{
	/** @use HasFactory<\Database\Factories\WorkspaceFactory> */
	use HasFactory;
	use HasUuids;
	use GeneratesSlug;

	protected $table = "workspaces";

	protected $primaryKey = 'uuid';

	protected $fillable = [
		'team_id',
		'name',
		'slug',
		'description'
	];

	public function team(): BelongsTo
	{
		return $this->belongsTo(Team::class);
	}
}
