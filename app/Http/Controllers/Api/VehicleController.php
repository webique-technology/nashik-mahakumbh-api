<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;

class VehicleController extends Controller
{

    public function index(Request $request)
    {
        $query = Vehicle::with('category');

        // if ($request->category) {
        //     $query->where('category', $request->category);
        // }
        // Search by name

        if ($request->filled('name')) {
            $query->where(
                'name',
                'like',
                '%' . $request->name . '%'
            );
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where(
                    'category',
                    'like',
                    '%' . $request->category . '%'
                );
            });
        }

        // Filter by price range
        if ($request->filled('price')) {
            $priceRange = explode('-', $request->price);

            if (count($priceRange) === 2) {
                $min = (int) $priceRange[0];
                $max = (int) $priceRange[1];

                $query->whereBetween('base_price', [$min, $max]);
            }
        }

        $query->orderBy('id', 'desc');

        if ($request->filled('limit')) {

            $vehicles = $query->take($request->limit)->get();

            $vehicles->transform(function ($vehicle) {

                $vehicle->image_url = $vehicle->car_image
                    ? asset('uploads/vehicles/' . $vehicle->car_image)
                    : null;

                return $vehicle;
            });

            return response()->json([
                'status' => true,
                'message' => 'vehicles fetched successfully',
                'data' => $vehicles
            ]);
        }

        $vehicles = $query->paginate(9);

        $vehicles->getCollection()->transform(function ($vehicle) {
            // $vehicle->car_image_url  = $vehicle->car_image ? asset('uploads/vehicles/' . $vehicle->car_image) : null;
            $vehicle->car_image_url = $vehicle->car_image ? '/uploads/vehicles/' . $vehicle->car_image : null;
            return $vehicle;
        });
        return response()->json([
            'status' => true,
            'data' => $vehicles
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([

            'name' => 'required|string|max:255',
            // 'location' => 'required|string',
            'status' => 'required',
            'total_seats' => 'required|integer',
            'features' => 'nullable|array',
            'category_id' => 'required',
            'base_price' => 'required|numeric',
            'car_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',

        ]);

        $vehicle = new Vehicle();

        $vehicle->name = $request->name;
        // $vehicle->location = $request->location;
        $vehicle->status = $request->status;
        $vehicle->total_seats = $request->total_seats;
        $vehicle->category_id = $request->category_id;
        $vehicle->base_price = $request->base_price;

        $vehicle->features = $request->features ?? [];

        if ($request->hasFile('car_image')) {

            $file = $request->file('car_image');
            $imageName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/vehicles'), $imageName);
            $vehicle->car_image = $imageName;
        }

        $vehicle->save();

        return response()->json([
            'status' => true,
            'message' => 'Vehicle created successfully',
            'data' => $vehicle
        ]);
    }

    public function show($id)
    {
        $vehicle = Vehicle::find($id);

        if (!$vehicle) {
            return response()->json([
                'status' => false,
                'message' => 'Vehicle not found'
            ], 404);
        }
        $vehicle->car_image_url = $vehicle->car_image
            ? asset('uploads/vehicles/' . $vehicle->car_image)
            : null;

        return response()->json([
            'status' => true,
            'data' => $vehicle
        ]);
    }

    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::find($id);

        if (!$vehicle) {
            return response()->json([
                'status' => false,
                'message' => 'Vehicle not found'
            ], 404);
        }

        $request->validate([

            'name' => 'sometimes|string|max:255',
            // 'location' => 'sometimes|string',
            'status' => 'sometimes',
            'total_seats' => 'sometimes|integer',
            'features' => 'nullable|array',
            'category_id' => 'sometimes',
            'base_price' => 'sometimes|numeric',
            'car_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',

        ]);

        if ($request->has('name')) {
            $vehicle->name = $request->name;
        }
        // if ($request->has('location')) {
        //     $vehicle->location = $request->location;
        // }
        if ($request->has('status')) {
            $vehicle->status = $request->status;
        }
        if ($request->has('total_seats')) {
            $vehicle->total_seats = $request->total_seats;
        }
        if ($request->has('category_id')) {
            $vehicle->category_id = $request->category_id;
        }
        if ($request->has('base_price')) {
            $vehicle->base_price = $request->base_price;
        }
        if ($request->has('features')) {
            $vehicle->features = $request->features;
        }
        if ($request->hasFile('car_image')) {

            // delete old image
            if (!empty($vehicle->car_image)) {
                $oldPath = public_path('uploads/vehicles/' . $vehicle->car_image);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file('car_image');
            $imageName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/vehicles'), $imageName);

            $vehicle->car_image = $imageName;
        }

        $vehicle->save();

        return response()->json([
            'status' => true,
            'message' => 'Vehicle updated successfully',
            'data' => $vehicle
        ]);
    }


    public function destroy($id)
    {
        $vehicle = Vehicle::find($id);

        if (!$vehicle) {
            return response()->json([
                'status' => false,
                'message' => 'Vehicle not found'
            ], 404);
        }

        // delete image
        if (!empty($vehicle->car_image)) {
            $path = public_path('uploads/vehicles/' . $vehicle->car_image);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $vehicle->delete();

        return response()->json([
            'status' => true,
            'message' => 'Vehicle deleted successfully'
        ]);
    }

    public function chatbotVehicles()
    {
        $vehicles = \App\Models\Vehicle::with('category')
            ->where('status', 1)
            ->get();

        $data = $vehicles->map(function ($vehicle) {

            return [
                'id' => $vehicle->id,
                'name' => $vehicle->name,
                'location' => $vehicle->location,
                'category' => $vehicle->category?->name,
                'total_seats' => $vehicle->total_seats,
                'base_price' => $vehicle->base_price,
                'features' => $vehicle->features,
            ];
        });

        return response()->json([
            'status' => true,
            'count' => $data->count(),
            'data' => $data,
        ]);
    }
}
