<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HotelEnquiry;
use Illuminate\Http\Request;

class HotelEnquiryController extends Controller
{
    /**
     * Listing + Pagination + Date Search
     */
    public function index(Request $request)
    {
        $date = $request->date;
        $query = HotelEnquiry::with('hotel');

        // Search by Name
        if ($request->filled('full_name')) {
            $query->where('full_name', 'like', '%' . $request->full_name . '%');
        }
        if ($request->filled('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }

        // Search by Check-In Date Range
        if ($request->filled('from_date') && $request->filled('to_date')) {

            $query->whereBetween(
                'check_in_date',
                [$request->from_date, $request->to_date]
            );
        }
        if ($date) {
            $query->whereDate('created_at', $date);
        }
        $perPage = $request->get('per_page', 10);

        $enquiries = $query
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // return response()->json($enquiries);
         $enquiries->getCollection()->transform(function ($enquiry) {
            return [
                'id' => $enquiry->id,
                'full_name' => $enquiry->full_name,
                'email' => $enquiry->email,
                'mobile_number' => $enquiry->mobile_number,
                'room_type' => $enquiry->room_type,
                'inquiry_type' => $enquiry->inquiry_type,
                'check_in_date' => $enquiry->check_in_date,
                'check_out_date' => $enquiry->check_out_date,
                'adults' => $enquiry->adults,
                'children' => $enquiry->children,
                'hotel_id' => $enquiry->hotel_id,

                // Readable date
                'created_at' => $enquiry->created_at->format('d M Y, h:i A'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $enquiries
        ]);
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'mobile_number' => 'required|string|max:20',
            'room_type' => 'required|string|max:255',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after_or_equal:check_in_date',
            'adults' => 'required|string|min:1',
            'children' => 'nullable|string|min:0',
        ]);

        $enquiry = HotelEnquiry::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Hotel enquiry submitted successfully.',
            'data' => $enquiry
        ], 201);
    }

    /**
     * Show Single
     */
    public function show($id)
    {
        $enquiry = HotelEnquiry::with('hotel')->findOrFail($id);

        return response()->json($enquiry);
    }

    /**
     * Update
     */
    public function update(Request $request, $id)
    {
        $enquiry = HotelEnquiry::findOrFail($id);

        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'mobile_number' => 'required|string|max:20',
            'room_type' => 'required|string|max:255',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after_or_equal:check_in_date',
            'adults' => 'required|string|min:1',
            'children' => 'nullable|string|min:0',
        ]);

        $enquiry->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Hotel enquiry updated successfully.',
            'data' => $enquiry
        ]);
    }

    /**
     * Delete
     */
    public function destroy($id)
    {
        $enquiry = HotelEnquiry::findOrFail($id);

        $enquiry->delete();

        return response()->json([
            'success' => true,
            'message' => 'Hotel enquiry deleted successfully.'
        ]);
    }
}