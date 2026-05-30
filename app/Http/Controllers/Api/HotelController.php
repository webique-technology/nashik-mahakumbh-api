<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;

class HotelController extends Controller
{
    public function index(Request $request)
    {
        $query = Hotel::query();

        if ($request->has('search') && !empty($request->search)) {

            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->has('category') && !empty($request->category)) {

            $query->where('category', $request->category);
        }

        if ($request->has('location') && !empty($request->location)) {

            $query->where('location', $request->location);
        }

        $query->orderBy('id', 'desc');

        $hotels = $query->paginate(10);

        // $hotels->getCollection()->transform(function ($hotel) {

        //     $hotel->image_url = !empty($hotel->images[0])
        //         ? asset('uploads/hotels/' . $hotel->images[0])
        //         : null;

        //     return $hotel;
        // });

        $hotels->getCollection()->transform(function ($hotel) {

            // convert all images into full URLs
            $hotel->images = collect($hotel->images)->map(function ($image) {

                return asset('uploads/hotels/' . $image);

            });

            // first image
            $hotel->image_url = !empty($hotel->images[0])
                ? $hotel->images[0]
                : null;

            return $hotel;
        });

        return response()->json([
            'status' => true,
            'message' => 'Hotels fetched successfully',
            'data' => $hotels
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([

            'title' => 'required|string|max:255',
            // 'description' => 'required',
            'rating' => 'nullable|numeric|min:0|max:5',
            'category' => 'required|string',
            'location' => 'required|string',
            'features' => 'nullable|array',
            'meals' => 'nullable|string',
            'base_price' => 'required|numeric',
            'offer_price' => 'nullable|numeric',

            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp',
            // 'images.*' => 'image',
        ]);

        $hotel = new Hotel();

        $hotel->title = $request->title;
        // $hotel->description = $request->description;
        $hotel->rating = $request->rating;
        $hotel->category = $request->category;
        $hotel->location = $request->location;
        $hotel->meals = $request->meals;
        $hotel->base_price = $request->base_price;
        $hotel->offer_price = $request->offer_price;

        $hotel->features = $request->features ?? [];

        $images = [];

        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $file) {

                $imageName = time().'_'.$file->getClientOriginalName();

                $file->move(public_path('uploads/hotels'), $imageName);

                $images[] = $imageName;
            }
        }

        $hotel->images = $images;

        $hotel->save();

        return response()->json([
            'status' => true,
            'message' => 'Hotel created successfully',
            'data' => $hotel
        ]);
    }

    public function show($id)
    {
        $hotel = Hotel::find($id);

        if (!$hotel) {
            return response()->json([
                'status' => false,
                'message' => 'Hotel not found'
            ], 404);
        }

        $hotel->images = collect($hotel->images)->map(function ($image) {
            return asset('uploads/hotels/' . $image);
        });

        return response()->json([
            'status' => true,
            'data' => $hotel
        ]);
    }

    public function update(Request $request, $id)
    {
        $hotel = Hotel::find($id);

        if (!$hotel) {
            return response()->json([
                'status' => false,
                'message' => 'Hotel not found'
            ], 404);
        }

        $request->validate([

            'title' => 'sometimes|string|max:255',
            // 'description' => 'sometimes',
            'rating' => 'nullable|numeric|min:0|max:5',
            'category' => 'sometimes|string',
            'location' => 'sometimes|string',
            'features' => 'nullable|array',
            'meals' => 'nullable|string',
            'base_price' => 'sometimes|numeric',
            'offer_price' => 'nullable|numeric',

            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp',
        ]);


        if ($request->has('title')) {
            $hotel->title = $request->title;
        }
        // if ($request->has('description')) {
        //     $hotel->description = $request->description;
        // }
        if ($request->has('rating')) {
            $hotel->rating = $request->rating;
        }
        if ($request->has('category')) {
            $hotel->category = $request->category;
        }
        if ($request->has('location')) {
            $hotel->location = $request->location;
        }
        if ($request->has('features')) {
            $hotel->features = $request->features;
        }
        if ($request->has('meals')) {
            $hotel->meals = $request->meals;
        }
        if ($request->has('base_price')) {
            $hotel->base_price = $request->base_price;
        }
        if ($request->has('offer_price')) {
            $hotel->offer_price = $request->offer_price;
        }


        if ($request->hasFile('images')) {

            if (!empty($hotel->images)) {

                foreach ($hotel->images as $oldImage) {

                    $oldPath = public_path('uploads/hotels/'.$oldImage);

                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
            }

            $images = [];

            foreach ($request->file('images') as $file) {

                $imageName = time().'_'.$file->getClientOriginalName();

                $file->move(public_path('uploads/hotels'), $imageName);

                $images[] = $imageName;
            }

            $hotel->images = $images;
        }

        $hotel->save();

        return response()->json([
            'status' => true,
            'message' => 'Hotel updated successfully',
            'data' => $hotel
        ]);
    }

    public function destroy($id)
    {
        $hotel = Hotel::find($id);

        if (!$hotel) {
            return response()->json([
                'status' => false,
                'message' => 'Hotel not found'
            ], 404);
        }

        if (!empty($hotel->images)) {

            foreach ($hotel->images as $image) {

                $path = public_path('uploads/hotels/'.$image);

                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }

        $hotel->delete();

        return response()->json([
            'status' => true,
            'message' => 'Hotel deleted successfully'
        ]);
    }
}
