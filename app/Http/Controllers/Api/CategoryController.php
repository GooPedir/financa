<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\TenantContext;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::with('children')->where('is_active', true)->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id' => 'nullable|exists:categories,id',
            'name' => 'required|string',
            'type' => 'required|in:INCOME,EXPENSE',
            'color' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);
        $category = Category::create(array_merge($data, ['tenant_id' => TenantContext::id()]));
        return response()->json($category, 201);
    }

    public function show($id)
    {
        return response()->json(Category::with('children')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->fill($request->only(['name','color','icon','is_active']))->save();
        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(['deleted' => true]);
    }
}

