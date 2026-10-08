<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductAttributeController extends Controller
{
    /**
     * Display a listing of the attributes.
     */
    public function index()
    {
        $attributes = ProductAttribute::orderBy('id', 'desc')->get();
        return view('admin.attributes.index', compact('attributes'));
    }

    /**
     * Show the form for creating a new attribute.
     */
    public function create()
    {
        return view('admin.attributes.create');
    }

    /**
     * Store a newly created attribute in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:product_attributes,name',
            'status' => 'nullable|boolean',
        ]);

        ProductAttribute::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status ?? true,
        ]);

        return redirect()->route('admin.attributes.index')
                         ->with('success', 'Product attribute created successfully!');
    }

    /**
     * Show the form for editing the specified attribute.
     */
    public function edit($id)
    {
        $attribute = ProductAttribute::findOrFail($id);
        return view('admin.attributes.edit', compact('attribute'));
    }

    /**
     * Update the specified attribute in storage.
     */
    public function update(Request $request, $id)
    {
        $attribute = ProductAttribute::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:product_attributes,name,' . $id,
            'status' => 'nullable|boolean',
        ]);

        $attribute->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => $request->status ?? false,
        ]);

        return redirect()->route('admin.attributes.index')
                         ->with('success', 'Product attribute updated successfully!');
    }

    /**
     * Remove the specified attribute from storage.
     */
    public function destroy($id)
    {
        $attribute = ProductAttribute::findOrFail($id);
        
        // Check if attribute has options
        if ($attribute->options()->count() > 0) {
            return redirect()->route('admin.attributes.index')
                             ->with('error', 'Cannot delete attribute with existing options. Please delete options first.');
        }

        $attribute->delete();

        return redirect()->route('admin.attributes.index')
                         ->with('success', 'Product attribute deleted successfully!');
    }
}