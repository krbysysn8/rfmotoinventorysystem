<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        return view('categories');
    }

    // ── GET /api/categories ───────────────────────────────────
    public function list(): JsonResponse
    {
        $categories = Category::withCount(['subcategories as subcat_count'])
            ->orderBy('category_name')
            ->get()
            ->map(fn($c) => [
                'category_id'   => $c->category_id,
                'category_name' => $c->category_name,
                'description'   => $c->description,
                'icon'          => $c->icon,
                'color_hex'     => $c->color_hex,
                'subcat_count'  => $c->subcat_count,
                'subcategories' => $c->subcategories()->get()->map(fn($s) => [
                    'subcategory_id'   => $s->subcategory_id,
                    'subcategory_name' => $s->subcategory_name,
                ]),
            ]);

        return response()->json(['status' => 'success', 'categories' => $categories]);
    }

    // ── POST /api/categories ──────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:100|unique:categories,category_name',
            'description'   => 'nullable|string|max:500',
            'icon'          => 'nullable|string|max:60',
            'color_hex'     => 'nullable|string|max:7',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $category = Category::create([
            'category_name' => $request->category_name,
            'description'   => $request->description,
            'icon'          => $request->icon,
            'color_hex'     => $request->color_hex,
        ]);

        ActivityLog::record(
            action:      'category',
            subject:     $category->category_name,
            description: "New category added.",
            user:        $request->user(),
        );

        return response()->json([
            'status'   => 'success',
            'message'  => 'Category created.',
            'category' => [
                'category_id'   => $category->category_id,
                'category_name' => $category->category_name,
                'icon'          => $category->icon,
                'color_hex'     => $category->color_hex,
                'description'   => $category->description,
                'subcat_count'  => 0,
                'subcategories' => [],
            ],
        ], 201);
    }

    // ── PUT /api/categories/{id} ──────────────────────────────
    public function update(Request $request, int $id): JsonResponse
    {
        $category = Category::where('category_id', $id)->first();
        if (!$category) {
            return response()->json(['status' => 'error', 'message' => 'Category not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'category_name' => "required|string|max:100|unique:categories,category_name,{$id},category_id",
            'description'   => 'nullable|string|max:500',
            'icon'          => 'nullable|string|max:60',
            'color_hex'     => 'nullable|string|max:7',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $category->update([
            'category_name' => $request->category_name,
            'description'   => $request->description,
            'icon'          => $request->icon,
            'color_hex'     => $request->color_hex,
        ]);

        ActivityLog::record(
            action:      'item_updated',
            subject:     $request->category_name,
            description: "Category updated.",
            user:        $request->user(),
        );

        return response()->json(['status' => 'success', 'message' => 'Category updated.']);
    }

    // ── DELETE /api/categories/{id} ───────────────────────────
    public function destroy(Request $request, int $id): JsonResponse
    {
        $category = Category::where('category_id', $id)->first();
        if (!$category) {
            return response()->json(['status' => 'error', 'message' => 'Category not found.'], 404);
        }

        $productCount = DB::table('products')->where('category_id', $id)->count();
        if ($productCount > 0) {
            return response()->json([
                'status'  => 'error',
                'message' => "Cannot delete: {$productCount} active product(s) are using this category.",
            ], 422);
        }

        $name = $category->category_name;
        $category->delete();
        Subcategory::where('category_id', $id)->delete();

        ActivityLog::record(
            action:      'deleted',
            subject:     $name,
            description: "Category and its subcategories deleted.",
            user:        $request->user(),
        );

        return response()->json(['status' => 'success', 'message' => 'Category deleted.']);
    }

    // ── POST /api/categories/{id}/subcategories ───────────────
    public function storeSubcategory(Request $request, int $id): JsonResponse
    {
        $category = Category::where('category_id', $id)->first();
        if (!$category) {
            return response()->json(['status' => 'error', 'message' => 'Category not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'subcategory_name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        if (Subcategory::where('category_id', $id)->where('subcategory_name', $request->subcategory_name)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'Subcategory already exists in this category.'], 422);
        }

        $sub = Subcategory::create([
            'category_id'      => $id,
            'subcategory_name' => $request->subcategory_name,
        ]);

        ActivityLog::record(
            action:      'category',
            subject:     $request->subcategory_name,
            description: "Subcategory added under '{$category->category_name}'.",
            user:        $request->user(),
        );

        return response()->json([
            'status'      => 'success',
            'message'     => 'Subcategory added.',
            'subcategory' => [
                'subcategory_id'   => $sub->subcategory_id,
                'subcategory_name' => $sub->subcategory_name,
            ],
        ], 201);
    }

    // ── PUT /api/subcategories/{id} ───────────────────────────
    public function updateSubcategory(Request $request, int $id): JsonResponse
    {
        $sub = Subcategory::where('subcategory_id', $id)->first();
        if (!$sub) {
            return response()->json(['status' => 'error', 'message' => 'Subcategory not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'subcategory_name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $old = $sub->subcategory_name;
        $sub->update(['subcategory_name' => $request->subcategory_name]);

        ActivityLog::record(
            action:      'item_updated',
            subject:     $request->subcategory_name,
            description: "Subcategory renamed: '{$old}' → '{$request->subcategory_name}'.",
            user:        $request->user(),
        );

        return response()->json(['status' => 'success', 'message' => 'Subcategory updated.']);
    }

    // ── DELETE /api/subcategories/{id} ────────────────────────
    public function destroySubcategory(Request $request, int $id): JsonResponse
    {
        $sub = Subcategory::where('subcategory_id', $id)->first();
        if (!$sub) {
            return response()->json(['status' => 'error', 'message' => 'Subcategory not found.'], 404);
        }

        $name = $sub->subcategory_name;
        $sub->delete();

        ActivityLog::record(
            action:      'deleted',
            subject:     $name,
            description: "Subcategory deleted.",
            user:        $request->user(),
        );

        return response()->json(['status' => 'success', 'message' => 'Subcategory deleted.']);
    }
}