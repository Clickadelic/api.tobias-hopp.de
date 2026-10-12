<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSlug;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
	use HasFactory, HasUuids, GeneratesSlug;

	public const ROLE_OWNER = 'owner';
	public const ROLE_MEMBER = 'member';

	protected $fillable = ['name', 'slug'];

	public function users(): BelongsToMany
	{
		return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
	}

	public function workspaces(): HasMany
	{
		return $this->hasMany(Workspace::class);
	}

	public function invitations(): HasMany
	{
		return $this->hasMany(TeamInvitation::class);
	}

	public function hasMember(User $user): bool
	{
		return $this->users()->whereKey($user->getKey())->exists();
	}

	public function isOwnedBy(User $user): bool
	{
		return $this->users()
			->whereKey($user->getKey())
			->wherePivot('role', self::ROLE_OWNER)
			->exists();
	}
}
