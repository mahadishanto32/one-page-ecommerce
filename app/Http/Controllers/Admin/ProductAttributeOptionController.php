<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductAttributeOptionController extends Controller
{
    /**
     * Display a listing of the attribute options.
     */
    public function index()
    {
        $options = ProductAttributeOption::with('attribute')
                                        ->orderBy('id', 'desc')
                                        ->get();

        return view('admin.attribute-options.index', compact('options'));
    }

    /**
     * Show the form for creating a new attribute option.
     */
    public function create()
    {
        $attributes = ProductAttribute::where('status', true)->get();
        return view('admin.attribute-options.create', compact('attributes'));
    }

    /**
     * Store a newly created attribute option in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'attribute_id' => 'required|exists:product_attributes,id',
            'status' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['status'] = $request->status ?? true;


        ProductAttributeOption::create($data);

        return redirect()->route('admin.attribute-options.index')
                         ->with('success', 'Product attribute option created successfully!');
    }

    /**
     * Show the form for editing the specified attribute option.
     */
    public function edit($id)
    {
        $option = ProductAttributeOption::findOrFail($id);
        $attributes = ProductAttribute::where('status', true)->get();
        return view('admin.attribute-options.edit', compact('option', 'attributes'));
    }

    /**
     * Update the specified attribute option in storage.
     */
    public function update(Request $request, $id)
    {
        $option = ProductAttributeOption::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'attribute_id' => 'required|exists:product_attributes,id',
            'status' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['status'] = $request->status ?? false;

        $option->update($data);

        return redirect()->route('admin.attribute-options.index')
                         ->with('success', 'Product attribute option updated successfully!');
    }

    /**
     * Remove the specified attribute option from storage.
     */
    public function destroy($id)
    {
        $option = ProductAttributeOption::findOrFail($id);

        $option->delete();

        return redirect()->route('admin.attribute-options.index')
                         ->with('success', 'Product attribute option deleted successfully!');
    }
}