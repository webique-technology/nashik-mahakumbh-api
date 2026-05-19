<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Vehicle::query();

        if ($request->category) {
            $query->where('category', $request->category);
        }
        $query->orderBy('id', 'desc');
        $vehicles = $query->paginate(10);
        // $vehicles = Vehicle::all();
         $vehicles->getCollection()->transform(function ($vehicle) {
            $vehicle->car_image_url  = $vehicle->car_image ? asset('uploads/vehicles/' . $vehicle->car_image) : null;
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
            'location' => 'required|string',
            'status' => 'required',
            'total_seats' => 'required|integer',
            'features' => 'nullable|array',
            'category' => 'required|string',
            'base_price' => 'required|numeric',
            'car_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',

        ]);

        $vehicle = new Vehicle();

        $vehicle->name = $request->name;
        $vehicle->location = $request->location;
        $vehicle->status = $request->status;
        $vehicle->total_seats = $request->total_seats;
        $vehicle->category = $request->category;
        $vehicle->base_price = $request->base_price;

        $vehicle->features = $request->features ?? [];

        if ($request->hasFile('car_image')) {

            $file = $request->file('car_image');
            $imageName = time().'_'.$file->getClientOriginalName();
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
            'location' => 'sometimes|string',
            'status' => 'sometimes',
            'total_seats' => 'sometimes|integer',
            'features' => 'nullable|array',
            'category' => 'sometimes|string',
            'base_price' => 'sometimes|numeric',
            'car_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',

        ]);

        if ($request->has('name')) {
            $vehicle->name = $request->name;
        }
        if ($request->has('location')) {
            $vehicle->location = $request->location;
        }
        if ($request->has('status')) {
            $vehicle->status = $request->status;
        }
        if ($request->has('total_seats')) {
            $vehicle->total_seats = $request->total_seats;
        }
        if ($request->has('category')) {
            $vehicle->category = $request->category;
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
                $oldPath = public_path('uploads/vehicles/'.$vehicle->car_image);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file('car_image');
            $imageName = time().'_'.$file->getClientOriginalName();
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
            $path = public_path('uploads/vehicles/'.$vehicle->car_image);
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
}
