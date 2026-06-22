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
        $lang = $request->get('lang', 'en');

        $query = \App\Models\Tour::with([
            'translations',
            'itineraries.translations',
            'seoMeta.translations'
        ]);

        // Search by title
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

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

            $tours->transform(function ($tour) use ($lang) {

                if ($lang !== 'en') {

                    $translation = $tour->translations
                        ->where('language_code', $lang)
                        ->first();

                    if ($translation) {

                        $tour->title = $translation->title;
                        $tour->description = $translation->description;
                        $tour->highlights = $translation->highlights;
                        $tour->inclusions = $translation->inclusions;
                        $tour->routes = $translation->routes;
                    }
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

                return $tour;
            });

            return response()->json([
                'status' => true,
                'message' => 'Tours fetched successfully',
                'data' => $tours
            ]);
        }

        $tours = $query->paginate(9);

        $tours->getCollection()->transform(function ($tour) use ($lang) {

            // Tour Translation
            if ($lang !== 'en') {

                $translation = $tour->translations
                    ->where('language_code', $lang)
                    ->first();

                if ($translation) {

                    $tour->title = $translation->title;
                    $tour->description = $translation->description;
                    $tour->highlights = $translation->highlights;
                    $tour->inclusions = $translation->inclusions;
                    $tour->routes = $translation->routes;
                }
            }

            // Main banner image
            $tour->image_url = $tour->main_banner
                ? asset('uploads/tours/' . $tour->main_banner)
                : null;

            // Itinerary images + translations
            $tour->itineraries->transform(function ($itinerary) use ($lang) {

                if ($lang !== 'en') {

                    $translation = $itinerary->translations
                        ->where('language_code', $lang)
                        ->first();

                    if ($translation) {

                        $itinerary->itinerary_title =
                            $translation->itinerary_title;

                        $itinerary->description =
                            $translation->description;
                    }
                }

                $itinerary->itineraries_image_url =
                    $itinerary->image
                    ? '/uploads/itineraries/' . $itinerary->image
                    : null;

                return $itinerary;
            });

            $tour->vehicle_categories =
                VehicleCategory::whereIn(
                    'id',
                    $tour->vehicle_category_ids ?? []
                )->get();

            if ($tour->start_date && $tour->end_date) {

                $days = Carbon::parse($tour->start_date)
                    ->diffInDays(Carbon::parse($tour->end_date)) + 1;

                $nights = max($days - 1, 0);

                $tour->duration =
                    "{$days} Days / {$nights} Nights";
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

            $banner = time() . '_' . $request->main_banner->getClientOriginalName();

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
            'base_price' => $request->base_price,
            'offer_price' => $request->offer_price ?? '',
            'taxes' => $request->taxes ?? '',
            'total_seats' => $request->total_seats ?? '',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'main_banner' => $banner ?? '',
            'vehicle_category_ids' => $request->vehicles,
            'routes' => $request->route,
            'slug' => Str::slug($request->title),
        ]);

        // // save itineraries
        if ($request->itineraries) {

            foreach ($request->itineraries as $index => $item) {

                $imageName = null;

                if ($request->hasFile("itineraries.$index.image")) {

                    $file = $request->file("itineraries.$index.image");

                    $imageName = time() . '_' . $file->getClientOriginalName();

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
        $tour->image_url = $tour->main_banner ? '/uploads/tours/' . $tour->main_banner : null;
        $tour->itineraries->transform(function ($itinerary) {
            $itinerary->itineraries_image_url = $itinerary->image ? '/uploads/itineraries/' . $itinerary->image : null;

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

            $banner = time() . '_' . $request->main_banner->getClientOriginalName();
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

        if ($request->filled('title')) {
            $tour->slug = Str::slug($request->title);
        }
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

                    $imageName = time() . '_' . $file->getClientOriginalName();

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
            return response()->json(['status' => false, 'message' => 'Tour not found'], 404);
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

    public function getBySlug(Request $request, $slug)
    {
        $lang = $request->get('lang', 'en');

        $tour = Tour::with([
            'translations',
            'itineraries.translations',
            'seoMeta.translations'
        ])
            ->where('slug', $slug)
            ->first();

        if (!$tour) {
            return response()->json([
                'status' => false,
                'message' => 'Tour not found'
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Tour Translation
        |--------------------------------------------------------------------------
        */
        if ($lang !== 'en') {

            $translation = $tour->translations
                ->where('language_code', $lang)
                ->first();

            if ($translation) {

                $tour->title = $translation->title;
                $tour->description = $translation->description;
                $tour->highlights = $translation->highlights;
                $tour->inclusions = $translation->inclusions;
                $tour->routes = $translation->routes;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SEO Translation
        |--------------------------------------------------------------------------
        */
        if ($tour->seoMeta && $lang !== 'en') {

            $seoTranslation = $tour->seoMeta->translations
                ->where('language_code', $lang)
                ->first();

            if ($seoTranslation) {

                $tour->seoMeta->title =
                    $seoTranslation->title;

                $tour->seoMeta->desc =
                    $seoTranslation->desc;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Image URL
        |--------------------------------------------------------------------------
        */
        $tour->image_url = $tour->main_banner
            ? asset('uploads/tours/' . $tour->main_banner)
            : null;

        /*
        |--------------------------------------------------------------------------
        | Duration
        |--------------------------------------------------------------------------
        */
        if ($tour->start_date && $tour->end_date) {

            $days = Carbon::parse($tour->start_date)
                ->diffInDays(Carbon::parse($tour->end_date)) + 1;

            $nights = max($days - 1, 0);

            $tour->duration = "{$days} Days / {$nights} Nights";
        } else {

            $tour->duration = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Itinerary Translation
        |--------------------------------------------------------------------------
        */
        $tour->itineraries->transform(function ($itinerary) use ($lang) {

            if ($lang !== 'en') {

                $translation = $itinerary->translations
                    ->where('language_code', $lang)
                    ->first();

                if ($translation) {

                    $itinerary->itinerary_title =
                        $translation->itinerary_title;

                    $itinerary->description =
                        $translation->description;
                }
            }

            $itinerary->itineraries_image_url =
                $itinerary->image
                ? asset('uploads/itineraries/' . $itinerary->image)
                : null;

            return $itinerary;
        });

        return response()->json([
            'status' => true,
            'data' => $tour
        ]);
    }

    public function chatbotTours()
    {
        $tours = \App\Models\Tour::with(['itineraries'])
            ->get();

        $data = $tours->map(function ($tour) {

            return [
                'id' => $tour->id,
                'title' => $tour->title,
                'description' => strip_tags($tour->description),
                'location' => $tour->location,
                'base_price' => $tour->base_price,
                'offer_price' => $tour->offer_price,
                'taxes' => $tour->taxes,
                'total_seats' => $tour->total_seats,
                'start_date' => $tour->start_date,
                'end_date' => $tour->end_date,
                'highlights' => $tour->highlights,
                'inclusions' => $tour->inclusions,
                'routes' => $tour->routes,
                'slug' => $tour->slug,

                'itinerary' => $tour->itineraries->map(function ($item) {
                    return [
                        'title' => $item->title,
                        'description' => strip_tags($item->description ?? ''),
                    ];
                })->values(),
            ];
        });

        return response()->json([
            'status' => true,
            'count' => $data->count(),
            'data' => $data,
        ]);
    }

    public function translateTour(Request $request, $id)
    {
        $tour = Tour::with(['itineraries', 'seoMeta'])->findOrFail($id);

        $lang = $request->lang;

        app(
            \App\Services\TourTranslationService::class
        )->translate(
            $tour,
            $lang
        );

        return response()->json([
            'status' => true,
            'message' => "{$lang} translated"
        ]);
    }
}
