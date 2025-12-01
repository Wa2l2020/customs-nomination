<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('questions')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        Category::create($request->all());
        return back()->with('success', 'تم إضافة الفئة بنجاح');
    }

    public function edit(Category $category)
    {
        $category->load('questions');
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate(['name' => 'required']);
        $category->update($request->all());
        return back()->with('success', 'تم تحديث الفئة بنجاح');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'تم حذف الفئة بنجاح');
    }

    // Question Methods
    public function storeQuestion(Request $request, Category $category)
    {
        $request->validate(['text' => 'required']);
        $category->questions()->create($request->all());
        return back()->with('success', 'تم إضافة السؤال بنجاح');
    }

    public function destroyQuestion(Question $question)
    {
        $question->delete();
        return back()->with('success', 'تم حذف السؤال بنجاح');
    }
}
