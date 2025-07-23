<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('manage categories')) {
            $categories = Category::with(['parent', 'children'])->orderBy('parent_id')->orderBy('name')->get();
            return view('category.index', compact('categories'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        $parentCategories = Category::getParentCategories();
        return view('category.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create categories')) {
            $validator = \Validator::make(
                $request->all(), [
                    'name' => 'required|string|max:255',
                    'parent_id' => 'nullable|integer|exists:categories,id',
                    'slug' => 'required|string|max:255|unique:categories,slug',
                    'description' => 'nullable|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $category = new Category();
            $category->name = $request->name;
            $category->parent_id = $request->parent_id ?? 0;
            $category->slug = $request->slug ?: Category::generateSlug($request->name);
            $category->description = $request->description;
            $category->active = isset($request->active) ? 1 : 0;
            $category->is_deletable = isset($request->is_deletable) ? 1 : 0;
            $category->save();

            return redirect()->route('categories.index')->with('success', __('Category successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Category $category)
    {
        //
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::getParentCategories()->where('id', '!=', $category->id);
        return view('category.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        if (\Auth::user()->can('edit categories')) {
            $validator = \Validator::make(
                $request->all(), [
                    'name' => 'required|string|max:255',
                    'parent_id' => 'nullable|integer|exists:categories,id',
                    'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
                    'description' => 'nullable|string',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            // Prevent setting parent as itself or its child
            if ($request->parent_id && ($request->parent_id == $category->id || $this->isChildOf($category, $request->parent_id))) {
                return redirect()->back()->with('error', __('Cannot set category as parent of itself or its child.'));
            }

            $category->name = $request->name;
            $category->parent_id = $request->parent_id ?? 0;
            $category->slug = $request->slug ?: Category::generateSlug($request->name, $category->id);
            $category->description = $request->description;
            $category->active = isset($request->active) ? 1 : 0;
            $category->is_deletable = isset($request->is_deletable) ? 1 : 0;
            $category->save();

            return redirect()->route('categories.index')->with('success', __('Category successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(Category $category)
    {
        if (\Auth::user()->can('delete categories')) {
            if (!$category->is_deletable) {
                return redirect()->back()->with('error', __('This category cannot be deleted.'));
            }

            if ($category->hasChildren()) {
                return redirect()->back()->with('error', __('Cannot delete category that has subcategories.'));
            }

            $category->delete();
            return redirect()->route('categories.index')->with('success', __('Category successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Check if a category is child of another category
     */
    private function isChildOf($category, $parentId)
    {
        $parent = Category::find($parentId);
        if (!$parent) return false;

        if ($parent->parent_id == $category->id) {
            return true;
        }

        if ($parent->parent_id > 0) {
            return $this->isChildOf($category, $parent->parent_id);
        }

        return false;
    }
} 