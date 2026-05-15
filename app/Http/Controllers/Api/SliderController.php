<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class SliderController extends Controller
{

    public function index()
    {
        $sliders = Slider::orderBy('display_order', 'ASC')
                    ->paginate(10);
                    // foreach ($sliders as $slider) {
                    //     $slider->banner_image = asset('uploads/sliders/'.$slider->banner_image);
                    // }

        return response()->json([
            'success' => true,
            'message' => 'Slider list fetched successfully',
            'data' => $sliders
        ], 200);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'banner_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'visibility' => 'required|boolean',
            'display_order' => 'required|integer',
            'status' => 'required',

        ]);

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        
        // Upload Image
        $imageName = null;

        if ($request->hasFile('banner_image')) {

            $file = $request->file('banner_image');

            $imageName = time().'_'.$file->getClientOriginalName();

            $file->move(public_path('uploads/sliders'), $imageName);
        }

        
        $slider = Slider::create([

            'banner_image' => $imageName,
            'title' => $request->title,
            'description' => $request->description,
            'visibility' => $request->visibility,
            'display_order' => $request->display_order,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Slider created successfully',
            'data' => $slider
        ]);
    }


    public function show($id)
    {
        $slider = Slider::find($id);

        if (!$slider) {

            return response()->json([
                'success' => false,
                'message' => 'Slider not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $slider
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $slider = Slider::find($id);

        if (!$slider) {

            return response()->json([
                'success' => false,
                'message' => 'Slider not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [

            'banner_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'visibility' => 'required|boolean',
            'display_order' => 'required|integer',
            'status' => 'required',

        ]);

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('banner_image')) {

            $oldImagePath = public_path('uploads/sliders/'.$slider->banner_image);

            if (File::exists($oldImagePath)) {

                File::delete($oldImagePath);
            }

            $file = $request->file('banner_image');

            $imageName = time().'_'.$file->getClientOriginalName();

            $file->move(public_path('uploads/sliders'), $imageName);

            $slider->banner_image = $imageName;
        }
        $slider->title = $request->title;
        $slider->description = $request->description;
        $slider->visibility = $request->visibility;
        $slider->display_order = $request->display_order;
        $slider->status = $request->status;

        $slider->save();

        return response()->json([
            'success' => true,
            'message' => 'Slider updated successfully',
            'data' => $slider
        ], 200);
    }

    public function destroy($id)
    {
        $slider = Slider::find($id);

        if (!$slider) {
            return response()->json([
                'success' => false,
                'message' => 'Slider not found'
            ], 404);
        }

        $imagePath = public_path('uploads/sliders/'.$slider->banner_image);
        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        $slider->delete();

        return response()->json([
            'success' => true,
            'message' => 'Slider deleted successfully'
        ], 200);
    }
   
}
