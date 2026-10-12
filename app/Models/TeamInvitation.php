<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamInvitation extends Model
{
	use HasUuids;

	protected $fillable = ['team_id', 'email', 'role', 'token', 'invited_by', 'expires_at'];

	protected $hidden = ['token'];

	protected function casts(): array
	{
		return ['expires_at' => 'datetime'];
	}

	public function team(): BelongsTo
	{
		return $this->belongsTo(Team::class);
	}

	public function inviter(): BelongsTo
	{
		return $this->belongsTo(User::class, 'invited_by');
	}

	public function isExpired(): bool
	{
		return $this->expires_at->isPast();
	}
}
