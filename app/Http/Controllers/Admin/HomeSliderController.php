<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\HomeSlider;
use App\Http\Requests\homeslider\HomeSliderRequest;
use Illuminate\Http\Request;

class HomeSliderController extends Controller
{
    public function index()
    {
        $sliders = HomeSlider::ordered()->get();
        return view('admin.homesliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.homesliders.create');
    }

    public function store(HomeSliderRequest $request)
    {
        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Create directory if it doesn't exist
            $uploadPath = public_path('uploads/homeslider');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $file->move($uploadPath, $filename);

            // Create slider
            HomeSlider::create([
                'name' => $request->name,
                'slider_type' => $request->slider_type,
                'file_path' => 'uploads/homeslider/' . $filename,
                'serial' => $request->serial ?? 0,
                'status' => $request->has('status') ? true : false,
            ]);
        }

        return redirect()->route('admin.homesliders.index')
            ->with('success', 'Home slider created successfully!');
    }

    public function show(HomeSlider $homeSlider)
    {
        return view('admin.homesliders.show', compact('homeslider'));
    }

    public function edit(HomeSlider $homeslider)
    {
        return view('admin.homesliders.edit', compact('homeslider'));
    }

    public function update(HomeSliderRequest $request, HomeSlider $homeslider)
    {
        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file
            $oldFilePath = public_path($homeslider->file_path);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }

            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Create directory if it doesn't exist
            $uploadPath = public_path('uploads/homeslider');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $file->move($uploadPath, $filename);
            $homeslider->file_path = 'uploads/homeslider/' . $filename;
        }

        // Update slider
        $homeslider->update([
            'name' => $request->name,
            'slider_type' => $request->slider_type,
            'serial' => $request->serial ?? 0,
            'status' => $request->has('status') ? true : false,
        ]);

        return redirect()->route('admin.homesliders.index')
            ->with('success', 'Home slider updated successfully!');
    }

    public function destroy(HomeSlider $homeslider)
    {
        // Delete file
        $filePath = public_path($homeslider->file_path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $homeslider->delete();

        return redirect()->route('admin.homesliders.index')
            ->with('success', 'Home slider deleted successfully!');
    }

    public function toggleStatus(HomeSlider $homeslider)
    {
        $homeslider->update([
            'status' => !$homeslider->status
        ]);

        return redirect()->back()
            ->with('success', 'Slider status updated successfully!');
    }
}