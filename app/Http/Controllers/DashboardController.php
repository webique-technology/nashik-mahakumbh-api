<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\Vehicle;
use App\Models\Hotel;
use App\Models\Blog;
use App\Models\TourEnquiry;

class DashboardController extends Controller
{
    public function index()
    {
        $tourCount = Tour::count();
        $vehicleCount = Vehicle::count();
        $hotelCount = Hotel::count();
        $blogCount = Blog::count();

        // latest enquiries
        $recentEnquiries = TourEnquiry::latest()
            ->take(5)
            ->get();

        // latest blogs/activity
        $recentBlogs = Blog::latest()
            ->take(4)
            ->get();

        return response()->json([
            'success' => true,

            'counts' => [
                'tours' => $tourCount,
                'vehicles' => $vehicleCount,
                'hotels' => $hotelCount,
                'blogs' => $blogCount,
                'visitors' => 0, // add later
            ],

            'recent_enquiries' => $recentEnquiries,

            'recent_activities' => $recentBlogs
        ]);
    }
}