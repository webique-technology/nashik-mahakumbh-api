<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\Itinerary;
use App\Models\SeoMeta;

class TourController extends Controller
{

    public function index()
    {
        // $tours = \App\Models\Tour::with(['itineraries', 'seoMeta'])->get();
        $tours = \App\Models\Tour::with(['itineraries', 'seoMeta'])->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $tours
        ]);
    }

    public function store(Request $request)
    {
        
        $request->validate([

            'title' => 'required|string|max:255',
            'description' => 'required',
            'category' => 'required|string',
            'status' => 'required',
            'location' => 'required',
            'base_price' => 'required|numeric',
            'offer_price' => 'nullable|numeric',
            'taxes' => 'nullable|numeric',
            'total_seats' => 'required|integer',
            'main_banner' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'highlights' => 'nullable|array',
            'inclusions' => 'nullable|array',
        ]);

        // upload banner
        $banner = null;

        if ($request->hasFile('main_banner')) {

            $banner = time().'_'.$request->main_banner->getClientOriginalName();

            $request->main_banner->move(public_path('uploads/tours'), $banner);
        }

        // create tour
        $tour = Tour::create([

            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'status' => $request->status,
            'location' => $request->location ?? '',
            'highlights' => $request->highlights ?? [],
            'inclusions' => $request->inclusions ?? [],
            'base_price' => $request->base_price ,
            'offer_price' => $request->offer_price ?? '',
            'taxes' => $request->taxes ?? '',
            'total_seats' => $request->total_seats ?? '',
            'main_banner' => $banner ?? '',
        ]);

        // // save itineraries
        if ($request->itineraries) {

            foreach ($request->itineraries as $index => $item) {

                $imageName = null;

                if ($request->hasFile("itineraries.$index.image")) {

                    $file = $request->file("itineraries.$index.image");

                    $imageName = time().'_'.$file->getClientOriginalName();

                    $file->move(public_path('uploads/itineraries'), $imageName);
                }

                Itinerary::create([
                    'tour_id' => $tour->id,
                    'itinerary_title' => $item['itinerary_title'],
                    'description' => $item['description'],
                    'image' => $imageName,
                ]);
            }
        }

        // save seo meta
        if ($request->seo_meta) {

            SeoMeta::create([
                'tour_id' => $tour->id,
                'title' => $request->seo_meta['title'],
                'desc' => $request->seo_meta['desc'],
            ]);
        }

        return response()->json([
            'message' => 'Tour created successfully',
            'data' => $tour
        ]);
    }

    public function show($id)
    {
        $tour = \App\Models\Tour::with(['itineraries', 'seoMeta'])->find($id);

        if (!$tour) {
            return response()->json([
                'status' => false,
                'message' => 'Tour not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $tour
        ]);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes',
            'category' => 'sometimes|string',
            'status' => 'sometimes',
            'location' => 'sometimes',
            'base_price' => 'sometimes|numeric',
            'offer_price' => 'nullable|numeric',
            'taxes' => 'nullable|numeric',
            'total_seats' => 'sometimes|integer',
            'main_banner' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'highlights' => 'nullable|array',
            'inclusions' => 'nullable|array',
        ]);

        $tour = Tour::find($id);

        if (!$tour) {
            return response()->json([
                'status' => false,
                'message' => 'Tour not found'
            ], 404);
        }

        if ($request->hasFile('main_banner')) {

            $banner = time().'_'.$request->main_banner->getClientOriginalName();
            $request->main_banner->move(public_path('uploads/tours'), $banner);
            $tour->main_banner = $banner;
        }

        $tour->update($request->only([
            'title',
            'description',
            'category',
            'status',
            'location',
            'base_price',
            'offer_price',
            'taxes',
            'total_seats',
        ]));

        if ($request->has('highlights')) {
            $tour->highlights = $request->highlights;
        }

        if ($request->has('inclusions')) {
            $tour->inclusions = $request->inclusions;
        }


        $tour->save();

        if ($request->itineraries) {

            // Get old itineraries
            $oldItineraries = Itinerary::where('tour_id', $tour->id)->get();

            // Delete old images safely
            foreach ($oldItineraries as $old) {
                if (!empty($old->image)) {
                    $this->deleteFile('uploads/itineraries/' . $old->image);
                }
            }

            // Delete old DB records
            Itinerary::where('tour_id', $tour->id)->delete();

            // Insert new itineraries
            foreach ($request->itineraries as $index => $item) {

                $imageName = null;

                // Check if new image uploaded
                if ($request->hasFile("itineraries.$index.image")) {

                    $file = $request->file("itineraries.$index.image");

                    $imageName = time().'_'.$file->getClientOriginalName();

                    $file->move(public_path('uploads/itineraries'), $imageName);
                }

                Itinerary::create([
                    'tour_id' => $tour->id,
                    'itinerary_title' => $item['itinerary_title'],
                    'description' => $item['description'],
                    'image' => $imageName, // null if not uploaded
                ]);
            }
        }

        if ($request->seo_meta) {

            SeoMeta::updateOrCreate(
                ['tour_id' => $tour->id],
                [
                    'title' => $request->seo_meta['title'],
                    'desc' => $request->seo_meta['desc'],
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Tour updated successfully',
            'data' => $tour
        ]);
    }

    // public function destroy($id)
    // {
    //     $tour = Tour::find($id);

    //     if (!$tour) {

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Tour not found'
    //         ], 404);
    //     }

    //     if ($tour->main_banner &&
    //         file_exists(public_path('uploads/tours/' . $tour->main_banner))) {

    //         unlink(public_path('uploads/tours/' . $tour->main_banner));
    //     }

    //     $tour->delete();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Tour deleted successfully'
    //     ]);
    // }
    private function deleteFile($path)
    {
        if ($path && file_exists(public_path($path))) {
            unlink(public_path($path));
        }
    }

    public function destroy($id)
    {
        $tour = Tour::with('itineraries')->find($id);

        if (!$tour) {
            return response()->json(['status' => false,'message' => 'Tour not found'], 404);
        }

        $this->deleteFile('uploads/tours/' . $tour->main_banner);

        foreach ($tour->itineraries as $itinerary) {
            $this->deleteFile('uploads/itineraries/' . $itinerary->image);
        }

        $tour->delete();

        return response()->json([
            'message' => 'Tour deleted successfully'
        ]);
    }
}