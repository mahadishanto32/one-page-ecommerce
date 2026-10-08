<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ReviewsController extends Controller
{
    /**
     * Photos are stored directly under the public folder (not the storage disk).
     */
    protected string $uploadPath = 'uploads/reviews';

    /**
     * Display a listing of the reviews.
     */
    public function index()
    {
        $reviews = Review::latest()->get();

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new review.
     */
    public function create()
    {
        return view('admin.reviews.create');
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'designation'   => 'nullable|string|max:255',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'message'       => 'required|string',
            'rating_point'  => 'required|integer|min:1|max:5',
            'active'        => 'nullable|boolean',
        ]);

        $validated['active'] = $request->boolean('active');

        if ($request->hasFile('photo')) {
            $validated['photo'] = $this->uploadPhoto($request->file('photo'));
        }

        Review::create($validated);

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', 'Review created successfully.');
    }

    /**
     * Show the form for editing the specified review.
     */
    public function edit(Review $review)
    {
        return view('admin.reviews.edit', compact('review'));
    }

    /**
     * Update the specified review in storage.
     */
    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'designation'   => 'nullable|string|max:255',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'message'       => 'required|string',
            'rating_point'  => 'required|integer|min:1|max:5',
            'active'        => 'nullable|boolean',
        ]);

        $validated['active'] = $request->boolean('active');

        if ($request->hasFile('photo')) {
            // Remove the old photo before saving the new one.
            $this->deletePhoto($review->photo);
            $validated['photo'] = $this->uploadPhoto($request->file('photo'));
        }

        $review->update($validated);

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', 'Review updated successfully.');
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(Review $review)
    {
        $this->deletePhoto($review->photo);
        $review->delete();

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }

    /**
     * Move the uploaded photo into public/uploads/reviews and return its filename.
     */
    protected function uploadPhoto($file): string
    {
        $destination = public_path($this->uploadPath);

        if (! File::isDirectory($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $file->move($destination, $filename);

        return $filename;
    }

    /**
     * Delete a photo file from public/uploads/reviews, if it exists.
     */
    protected function deletePhoto(?string $filename): void
    {
        if (! $filename) {
            return;
        }

        $path = public_path($this->uploadPath . '/' . $filename);

        if (File::exists($path)) {
            File::delete($path);
        }
    }
}