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
        $tours = \App\Models\Tour::with(['itineraries', 'seoMeta'])->get();

        return response()->json([
            'status' => true,
            'data' => $tours
        ]);
    }

    public function store(Request $request)
    {
        
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
            'location' => $request->location,
            'highlights' => $request->highlights,
            'inclusions' => $request->inclusions,
            'base_price' => $request->base_price,
            'offer_price' => $request->offer_price,
            'taxes' => $request->taxes,
            'total_seats' => $request->total_seats,
            'main_banner' => $banner,
        ]);

        // save itineraries
        if ($request->itineraries) {

            foreach ($request->itineraries as $item) {

                Itinerary::create([
                    'tour_id' => $tour->id,
                    'itinerary_title' => $item['itinerary_title'],
                    'description' => $item['description'],
                    'image' => $item['image'] ?? null,
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
        $tour = Tour::find($id);

        if (!$tour) {
            return response()->json([
                'status' => false,
                'message' => 'Tour not found'
            ], 404);
        }

        // upload new banner if exists
        if ($request->hasFile('main_banner')) {

            $banner = time().'_'.$request->main_banner->getClientOriginalName();

            $request->main_banner->move(public_path('uploads/tours'), $banner);

            $tour->main_banner = $banner;
        }

        // update tour
        $tour->title = $request->title;
        $tour->description = $request->description;
        $tour->category = $request->category;
        $tour->status = $request->status;
        $tour->location = $request->location;

        $tour->highlights = $request->highlights;

        $tour->inclusions = $request->inclusions;

        $tour->base_price = $request->base_price;
        $tour->offer_price = $request->offer_price;
        $tour->taxes = $request->taxes;
        $tour->total_seats = $request->total_seats;

        $tour->save();

        // update itineraries
        if ($request->itineraries) {

            // delete old itineraries
            Itinerary::where('tour_id', $tour->id)->delete();

            foreach ($request->itineraries as $item) {

                Itinerary::create([
                    'tour_id' => $tour->id,
                    'itinerary_title' => $item['itinerary_title'],
                    'description' => $item['description'],
                    'image' => $item['image'] ?? null,
                ]);
            }
        }

        // update seo meta
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
}