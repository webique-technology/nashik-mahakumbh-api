<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TourEnquiry;
use Illuminate\Http\Request;

class TourEnquiryController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->search;

        $query = TourEnquiry::with('tour');

        if ($search) {
            $query->where(function ($q) use ($search) {

                $q->where('full_name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('number_of_travelers', 'LIKE', "%{$search}%");

            });
        }

        $enquiries = $query
            ->latest()
            ->paginate(10);

        // transform response
        $enquiries->getCollection()->transform(function ($enquiry) {

            return [
                'id' => $enquiry->id,
                'full_name' => $enquiry->full_name,
                'email' => $enquiry->email,
                'phone_number' => $enquiry->phone_number,
                'number_of_travelers' => $enquiry->number_of_travelers,
                'preferred_dates' => $enquiry->preferred_dates,

                'tour_name' => $enquiry->tour?->title,

                'special_requirements' => $enquiry->special_requirements,
                'created_at' => $enquiry->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $enquiries
        ]);
    }

   
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'number_of_travelers' => 'required',
            'preferred_dates' => 'nullable|string|max:255',
            'tour_id' => 'required|exists:tours,id',
            'special_requirements' => 'nullable|string',
        ]);

        $enquiry = TourEnquiry::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tour enquiry submitted successfully.',
            'data' => $enquiry
        ], 201);
    }

    public function show($id)
    {
        $enquiry = TourEnquiry::with('tour')->find($id);

        if (!$enquiry) {
            return response()->json([
                'success' => false,
                'message' => 'Enquiry not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $enquiry
        ]);
    }

   
    public function destroy($id)
    {
        $enquiry = TourEnquiry::find($id);

        if (!$enquiry) {
            return response()->json([
                'success' => false,
                'message' => 'Enquiry not found'
            ], 404);
        }

        $enquiry->delete();

        return response()->json([
            'success' => true,
            'message' => 'Enquiry deleted successfully'
        ]);
    }
}