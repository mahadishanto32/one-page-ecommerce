<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoriesController extends Controller
{
    /**
     * Relative path (inside /public) where category images are stored.
     */
    protected $uploadPath = 'uploads/product-categories';

    public function index()
    {
        $categories = ProductCategory::orderBy('serial', 'asc')->get();
        return view('admin.product-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.product-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:5120',
            'serial' => 'nullable|integer',
            'status' => 'nullable|boolean',
        ]);

        $data = [
            'name'   => $validated['name'],
            'serial' => $validated['serial'] ?? 0,
            'status' => $request->has('status') ? 1 : 0,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'));
        }

        ProductCategory::create($data);

        return redirect()
            ->route('admin.product-categories.index')
            ->with('success', 'Product category created successfully.');
    }

    public function edit($id)
    {
        $category = ProductCategory::findOrFail($id);
        return view('admin.product-categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = ProductCategory::findOrFail($id);

        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:5120',
            'serial' => 'nullable|integer',
            'status' => 'nullable|boolean',
        ]);

        $data = [
            'name'   => $validated['name'],
            'serial' => $validated['serial'] ?? 0,
            'status' => $request->has('status') ? 1 : 0,
        ];

        if ($request->hasFile('image')) {
            $this->deleteImage($category->image);
            $data['image'] = $this->uploadImage($request->file('image'));
        }

        $category->update($data);

        return redirect()
            ->route('admin.product-categories.index')
            ->with('success', 'Product category updated successfully.');
    }

    public function destroy($id)
    {
        $category = ProductCategory::findOrFail($id);

        $this->deleteImage($category->image);
        $category->delete();

        return redirect()
            ->route('admin.product-categories.index')
            ->with('success', 'Product category deleted successfully.');
    }

    /**
     * Move uploaded file into public/uploads/product-categories and return filename.
     */
    private function uploadImage($file)
    {
        $destination = public_path($this->uploadPath);

        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }

        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($destination, $filename);

        return $filename;
    }

    /**
     * Delete an existing image file from public/uploads/product-categories.
     */
    private function deleteImage($filename)
    {
        if ($filename) {
            $path = public_path($this->uploadPath . '/' . $filename);
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
}