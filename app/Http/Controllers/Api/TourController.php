<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\Itinerary;
use App\Models\SeoMeta;
use App\Models\VehicleCategory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TourController extends Controller
{

    public function index(Request $request)
    {
        // $tours = \App\Models\Tour::with(['itineraries', 'seoMeta'])->paginate(10);
        $query = \App\Models\Tour::with(['itineraries', 'seoMeta']);

            // Search by title
            if ($request->filled('title')) {
                $query->where('title', 'like', '%' . $request->title . '%');
            }


            // Filter by category
            // if ($request->filled('category')) {
            //     $query->where('category', $request->category);
            // }

            // Filter by price range
            if ($request->filled('price')) {

                if ($request->price == 'below_5000') {

                    $query->where('base_price', '<', 5000);

                } elseif ($request->price == '5000_15000') {

                    $query->whereBetween('base_price', [5000, 15000]);

                } elseif ($request->price == 'above_15000') {

                    $query->where('base_price', '>', 15000);
                }
            }

            $query->latest();

            if ($request->filled('limit')) {

                $tours = $query->take($request->limit)->get();

                $tours->transform(function ($tour) {

                    $tour->image_url = $tour->main_banner
                        ? asset('uploads/tours/' . $tour->main_banner)
                        : null;

                    if ($tour->start_date && $tour->end_date) {
                        $days = Carbon::parse($tour->start_date)
                            ->diffInDays(Carbon::parse($tour->end_date)) + 1;
                        $nights = max($days - 1, 0);
                        $tour->duration = "{$days} Days / {$nights} Nights";
                    } else {
                        $tour->duration = null;
                    }


                    return $tour;
                });

                return response()->json([
                    'status' => true,
                    'message' => 'Tours fetched successfully',
                    'data' => $tours
                ]);
            }

            $tours = $query->paginate(9);
            

            $tours->getCollection()->transform(function ($tour) {
                // Main banner image
                $tour->image_url = $tour->main_banner ? asset('uploads/tours/' . $tour->main_banner) : null;
                // Itinerary images
                // $tour->itineraries->transform(function ($itinerary) {
                //     $itinerary->itineraries_image_url = $itinerary->image ? asset('uploads/itineraries/' . $itinerary->image) : null;
                //     return $itinerary;
                // });
                $tour->itineraries->transform(function ($itinerary) {
                    $itinerary->itineraries_image_url = $itinerary->image ? '/uploads/itineraries/' . $itinerary->image: null;

                    return $itinerary;
                });

                $tour->vehicle_categories = VehicleCategory::whereIn('id', $tour->vehicle_category_ids ?? [])->get();

                if ($tour->start_date && $tour->end_date) {
                    $days = Carbon::parse($tour->start_date)
                        ->diffInDays(Carbon::parse($tour->end_date)) + 1;
                    $nights = max($days - 1, 0);
                    $tour->duration = "{$days} Days / {$nights} Nights";
                } else {
                    $tour->duration = null;
                }

                return $tour;
            });

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
            // 'category' => 'required|string',
            'status' => 'required',
            // 'location' => 'required',
            'base_price' => 'required|numeric',
            'offer_price' => 'nullable|numeric',
            'taxes' => 'nullable|numeric',
            'total_seats' => 'required|integer',
            'main_banner' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'highlights' => 'nullable|array',
            'inclusions' => 'nullable|array',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
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
            // 'category' => $request->category,
            'status' => $request->status,
            // 'location' => $request->location ?? '',
            'highlights' => $request->highlights ?? [],
            'inclusions' => $request->inclusions ?? [],
            'base_price' => $request->base_price ,
            'offer_price' => $request->offer_price ?? '',
            'taxes' => $request->taxes ?? '',
            'total_seats' => $request->total_seats ?? '',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'main_banner' => $banner ?? '',
            'vehicle_category_ids' => $request->vehicles,
            'routes' => $request->route,
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
        $tour->image_url = $tour->main_banner ? '/uploads/tours/' . $tour->main_banner: null;
        $tour->itineraries->transform(function ($itinerary) {
                    $itinerary->itineraries_image_url = $itinerary->image ? '/uploads/itineraries/' . $itinerary->image: null;

                    return $itinerary;
         });

        if ($tour->start_date && $tour->end_date) {
            $days = Carbon::parse($tour->start_date)
                ->diffInDays(Carbon::parse($tour->end_date)) + 1;
            $nights = max($days - 1, 0);
            $tour->duration = "{$days} Days / {$nights} Nights";
        } else {
            $tour->duration = null;
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
            'status' => 'sometimes',
            // 'location' => 'sometimes',
            'base_price' => 'sometimes|numeric',
            'offer_price' => 'nullable|numeric',
            'taxes' => 'nullable|numeric',
            'total_seats' => 'sometimes|integer',
            'main_banner' => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'highlights' => 'nullable|array',
            'inclusions' => 'nullable|array',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
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
            // 'category',
            'status',
            // 'location',
            'base_price',
            'offer_price',
            'taxes',
            'total_seats',
            'start_date',
            'end_date',
        ]));

        if ($request->has('highlights')) {
            $tour->highlights = $request->highlights;
        }

        if ($request->has('inclusions')) {
            $tour->inclusions = $request->inclusions;
        }
         if ($request->has('vehicles')) {
            $tour->vehicle_category_ids = $request->vehicles;
        }
         if ($request->has('route')) {
            $tour->routes = $request->route;
        }

        $tour->save();

       if ($request->itineraries) {

            $existingIds = [];

            foreach ($request->itineraries as $index => $item) {

                $imageName = $item['existing_image'] ?? null;

                // upload new image
                if ($request->hasFile("itineraries.$index.image")) {

                    $file = $request->file("itineraries.$index.image");

                    $imageName = time().'_'.$file->getClientOriginalName();

                    $file->move(
                        public_path('uploads/itineraries'),
                        $imageName
                    );
                }

                // update existing itinerary
                if (!empty($item['id'])) {

                    $itinerary = Itinerary::find($item['id']);

                    if ($itinerary) {

                        // if new image uploaded remove old image
                        if (
                            $request->hasFile("itineraries.$index.image") &&
                            !empty($itinerary->image)
                        ) {
                            $this->deleteFile(
                                'uploads/itineraries/' . $itinerary->image
                            );
                        }

                        $itinerary->update([
                            'itinerary_title' => $item['itinerary_title'],
                            'description' => $item['description'],
                            'image' => $imageName,
                        ]);

                        $existingIds[] = $itinerary->id;
                    }

                } else {

                    // create new itinerary
                    $new = Itinerary::create([
                        'tour_id' => $tour->id,
                        'itinerary_title' => $item['itinerary_title'],
                        'description' => $item['description'],
                        'image' => $imageName,
                    ]);

                    $existingIds[] = $new->id;
                }
            }

            // delete removed itineraries
            $removed = Itinerary::where('tour_id', $tour->id)
                ->whereNotIn('id', $existingIds)
                ->get();

            foreach ($removed as $row) {

                if (!empty($row->image)) {
                    $this->deleteFile(
                        'uploads/itineraries/' . $row->image
                    );
                }

                $row->delete();
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
        // if ($path && file_exists(public_path($path))) {
        //     unlink(public_path($path));
        // }

        if (!$path) {
            return;
        }

        $fullPath = public_path($path);

        if (file_exists($fullPath) && is_file($fullPath)) {
            unlink($fullPath);
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

    public function getVehicleCategories($id)
    {
        $tour = Tour::find($id);

        if (!$tour) {
            return response()->json([
                'message' => 'Tour not found'
            ], 404);
        }

        $categoryIds =
            $tour->vehicle_category_ids ?? [];

        $categories =
            VehicleCategory::whereIn(
                'id',
                $categoryIds
            )
            ->select('id', 'category')
            ->get();

        return response()->json($categories);
    }

    public function getBySlug($slug)
    {
        $tour = Tour::with(['itineraries', 'seoMeta'])
            ->get()
            ->first(function ($item) use ($slug) {
                return Str::slug($item->title) === $slug;
            });

        if (!$tour) {
            return response()->json([
                'status' => false,
                'message' => 'Tour not found'
            ], 404);
        }

        $tour->image_url = $tour->main_banner
            ? asset('uploads/tours/' . $tour->main_banner)
            : null;

        if ($tour->start_date && $tour->end_date) {
            $days = Carbon::parse($tour->start_date)
                ->diffInDays(Carbon::parse($tour->end_date)) + 1;
            $nights = max($days - 1, 0);
            $tour->duration = "{$days} Days / {$nights} Nights";
        } else {
            $tour->duration = null;
        }

        $tour->itineraries->transform(function ($itinerary) {
            $itinerary->itineraries_image_url = $itinerary->image
                ? asset('uploads/itineraries/' . $itinerary->image)
                : null;

            return $itinerary;
        });

        return response()->json([
            'status' => true,
            'data' => $tour
        ]);
    }
}