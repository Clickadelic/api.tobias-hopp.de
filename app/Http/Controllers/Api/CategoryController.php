<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Concerns\GeneratesSlug;

class CategoryController extends Controller
{
	use HasFactory, HasUuids, GeneratesSlug;
	public function index(Request $request)
	{

		$perPage = min(max((int) $request->query('per_page', 10), 1), 50);
		$categories = Category::query()
			->latest()
			->paginate($perPage)
			->withQueryString();

		return response()->json($categories);
	}

	public function show(Category $category)
	{
		return response()->json($category);
	}

	public function store(StoreCategoryRequest $request)
	{
		$category = Category::create($request->validated());

		return response()->json($category, 201);
	}

	public function update(UpdateCategoryRequest $request, Category $category)
	{
		$category->update($request->validated());

		return response()->json($category);
	}

	public function destroy(Category $category)
	{
		$category->delete();

		return response()->json(null, 204);
	}
}

