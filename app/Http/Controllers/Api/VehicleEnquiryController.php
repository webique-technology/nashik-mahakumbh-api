<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleEnquiry;
use Illuminate\Http\Request;

class VehicleEnquiryController extends Controller
{
    /**
     * List with Pagination + Search
     */
    public function index(Request $request)
    {
        $date = $request->date;
        $query = VehicleEnquiry::with('vehicle');

        // Search by name
        if ($request->filled('full_name')) {
            $query->where(
                'full_name',
                'like',
                '%' . $request->full_name . '%'
            );
        }

        // Search by vehicle
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Search by pickup date range
        if (
            $request->filled('from_date') &&
            $request->filled('to_date')
        ) {
            $query->whereBetween(
                'pickup_date',
                [
                    $request->from_date,
                    $request->to_date
                ]
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
                'mobile_number' => $enquiry->mobile_number,
                'pickup_date' => $enquiry->pickup_date,
                'return_date' => $enquiry->return_date,
                'passengers' => $enquiry->passengers,
                'vehicle_id' => $enquiry->vehicle_id,

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
            'full_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',

            'pickup_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:pickup_date',

            'passengers' => 'required|integer|min:1',

            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        $enquiry = VehicleEnquiry::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Vehicle enquiry submitted successfully.',
            'data' => $enquiry
        ], 201);
    }

    /**
     * Show Single
     */
    public function show($id)
    {
        $enquiry = VehicleEnquiry::with('vehicle')
            ->findOrFail($id);

        return response()->json($enquiry);
    }

    /**
     * Update
     */
    public function update(Request $request, $id)
    {
        $enquiry = VehicleEnquiry::findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:20',

            'pickup_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:pickup_date',

            'passengers' => 'required|integer|min:1',

            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        $enquiry->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Vehicle enquiry updated successfully.',
            'data' => $enquiry
        ]);
    }

    /**
     * Delete
     */
    public function destroy($id)
    {
        $enquiry = VehicleEnquiry::findOrFail($id);

        $enquiry->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle enquiry deleted successfully.'
        ]);
    }
}