<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carousel;
use Illuminate\Http\Request;

class CarouselController extends Controller
{
    /**
     * Display all records
     */
    public function index()
    {
        $carousels = Carousel::latest()->paginate(8);

        $carousels->getCollection()->transform(function ($carousel) {
            $carousel->image_url = $carousel->carousel_image
                ? asset('uploads/carousels/' . $carousel->carousel_image)
                : null;

            return $carousel;
        });

        return response()->json($carousels);
    }

    /**
     * Store new carousel
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'description' => 'nullable',
            'first_button_name' => 'nullable|string|max:255',
            'first_button_link' => 'nullable|string|max:500',
            'second_button_name' => 'nullable|string|max:255',
            'second_button_link' => 'nullable|string|max:500',
            'status' => 'nullable',
            'carousel_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imageName = null;

        if ($request->hasFile('carousel_image')) {
            $image = $request->file('carousel_image');

            $imageName = time() . '_' . $image->getClientOriginalName();

            $image->move(public_path('uploads/carousels'), $imageName);
        }

        $carousel = Carousel::create([
            'title' => $request->title,
            'sub_title' => $request->sub_title,
            'description' => $request->description,
            'first_button_name' => $request->first_button_name,
            'first_button_link' => $request->first_button_link,
            'second_button_name' => $request->second_button_name,
            'second_button_link' => $request->second_button_link,
            'status' => $request->status ?? 1,
            'carousel_image' => $imageName,
        ]);

        return response()->json([
            'message' => 'Carousel created successfully',
            'data' => $carousel
        ], 201);
    }

    /**
     * Show single record
     */
    public function show($id)
    {
        $carousel = Carousel::findOrFail($id);

        $carousel->image_url = $carousel->carousel_image
            ? asset('uploads/carousels/' . $carousel->carousel_image)
            : null;

        return response()->json($carousel);
    }

    /**
     * Update carousel
     */
    public function update(Request $request, $id)
    {
        $carousel = Carousel::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'description' => 'nullable',
            'first_button_name' => 'nullable|string|max:255',
            'first_button_link' => 'nullable|string|max:500',
            'second_button_name' => 'nullable|string|max:255',
            'second_button_link' => 'nullable|string|max:500',
            'status' => 'nullable',
            'carousel_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('carousel_image')) {

            if (
                $carousel->carousel_image &&
                file_exists(public_path('uploads/carousels/' . $carousel->carousel_image))
            ) {
                unlink(public_path('uploads/carousels/' . $carousel->carousel_image));
            }

            $image = $request->file('carousel_image');

            $imageName = time() . '_' . $image->getClientOriginalName();

            $image->move(public_path('uploads/carousels'), $imageName);

            $carousel->carousel_image = $imageName;
        }

        $carousel->update([
            'title' => $request->title,
            'sub_title' => $request->sub_title,
            'description' => $request->description,
            'first_button_name' => $request->first_button_name,
            'first_button_link' => $request->first_button_link,
            'second_button_name' => $request->second_button_name,
            'second_button_link' => $request->second_button_link,
            'status' => $request->status ?? $carousel->status,
        ]);

        return response()->json([
            'message' => 'Carousel updated successfully',
            'data' => $carousel
        ]);
    }

    /**
     * Delete carousel
     */
    public function destroy($id)
    {
        $carousel = Carousel::findOrFail($id);

        if (
            $carousel->carousel_image &&
            file_exists(public_path('uploads/carousels/' . $carousel->carousel_image))
        ) {
            unlink(public_path('uploads/carousels/' . $carousel->carousel_image));
        }

        $carousel->delete();

        return response()->json([
            'message' => 'Carousel deleted successfully'
        ]);
    }
}