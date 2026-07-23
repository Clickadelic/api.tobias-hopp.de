<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

use App\Models\Ingredient;
use App\Models\Category;
use App\Models\Media;
use App\Models\RecipeRating;
use App\Models\Comment;
use App\Models\User;


class Recipe extends Model
{
	// 1. Verbindung zur Rezeptbuch-Datenbank wechseln
	protected $connection = 'rezeptbuch_db';

	protected $table = 'recipes';

	// Da UUIDs als Strings genutzt werden
	public $incrementing = false;
	protected $primaryKey = 'id';
	protected $keyType = 'string';

	// Für reine Lese-Operationen eigentlich optional, aber gut fürs Eager Loading
	protected $fillable = [
		'id',
		'name',
		'status',
		'slug',
		'punchline',
		'description',
		'preparation_time',
		'preparation_instructions',
		'difficulty',
		'is_veggy',
		'community_rating',
		'community_votes',
		'user_id',
		'category_id'
	];

	protected $casts = [
		'preparation_time' => 'integer',
		'status' => 'string',
		'community_rating' => 'float', // Auf float geändert, da round(..., 2) Nachkommastellen haben kann
		'community_votes' => 'integer',
		'category_id' => 'integer',
		'is_veggy' => 'boolean',
	];

	/**
     Map-Routing über den Slug bleibt für die API nützlich, 
     falls du Routen wie /api/v1/recipes/{recipe:slug} nutzen willst.
	 */
	public function getRouteKeyName(): string
	{
		return 'slug';
	}

	/*
    |--------------------------------------------------------------------------
    | Beziehungen (Relations)
    |--------------------------------------------------------------------------
    | ACHTUNG: Beziehungen wie 'user', 'category' oder 'ingredients' funktionieren
    | über Eloquent nur dann reibungslos, wenn die zugehörigen Models (Ingredient, Category, etc.)
    | in der API AUCH existieren und EBENFALLS auf 'protected $connection = "rezeptbuch_db";' gesetzt sind.
    */

	public function ingredients(): BelongsToMany
	{
		return $this->belongsToMany(Ingredient::class, 'recipe_ingredient')
			->withPivot('quantity', 'unit')
			->withTimestamps();
	}

	public function category(): BelongsTo
	{
		return $this->belongsTo(Category::class);
	}

	public function media(): BelongsToMany
	{
		return $this->belongsToMany(Media::class, 'recipe_media')
			->withPivot('collection', 'is_primary', 'position')
			->withTimestamps()
			->orderBy('recipe_media.position');
	}

	public function ratings(): HasMany
	{
		return $this->hasMany(RecipeRating::class);
	}

	public function comments(): HasMany
	{
		return $this->hasMany(Comment::class)->whereNull('parent_id');
	}

    /*
    |--------------------------------------------------------------------------
    | Lokale API-Features (z.B. Favoriten steuern)
    |--------------------------------------------------------------------------
    */

	/**
	 * Falls die "Favorites"-Tabelle in der API-Datenbank liegt (und nicht im Rezeptbuch),
	 * wechselt dieses Model für diese eine Beziehung elegant zurück auf deine API-Standard-DB!
	 */
	public function favoritedBy(): BelongsToMany
	{
		// 'mysql' entspricht deiner Standard-API-Verbindung
		return $this->belongsToMany(User::class, 'favorites')
			->from('mysql.recipes') // Zwingt den Join über die richtige DB, falls nötig
			->withTimestamps();
	}

	/**
	 * Prüft, ob der über Sanctum authentifizierte User dieses Rezept favorisiert hat.
	 */
	public function getIsFavoriteAttribute(): bool
	{
		$userId = Auth::id();
		if (!$userId) {
			return false;
		}

		return $this->favoritedBy()->where('user_id', $userId)->exists();
	}
}
