<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    

    public function index(Request $request)
    {
        $search = $request->search;
        $date = $request->date;

        $query = ContactUs::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('mobile_number', 'LIKE', "%{$search}%")
                ->orWhere('inquiry_type', 'LIKE', "%{$search}%");
            });
        }

        // Date filter
        if ($date) {
            $query->whereDate('created_at', $date);
        }

        $contacts = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $contacts->getCollection()->transform(function ($contact) {
            return [
                'id' => $contact->id,
                'full_name' => $contact->full_name,
                'email' => $contact->email,
                'mobile_number' => $contact->mobile_number,
                'inquiry_type' => $contact->inquiry_type,
                'your_message' => $contact->your_message,

                // Readable date
                'created_at' => $contact->created_at->format('d M Y, h:i A'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $contacts
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile_number' => 'required|string|max:20',
            'inquiry_type' => 'required|string|max:255',
            'your_message' => 'required|string',
        ]);

        $contact = ContactUs::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Contact inquiry submitted successfully.',
            'data' => $contact
        ], 201);
    }

    
    // public function show($id)
    // {
    //     $contact = ContactUs::find($id);
    //     if (!$contact) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Inquiry not found'
    //         ], 404);
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'data' => $contact
    //     ]);
    // }

   
    // public function destroy($id)
    // {
    //     $contact = ContactUs::find($id);
    //     if (!$contact) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Inquiry not found'
    //         ], 404);
    //     }

    //     $contact->delete();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Inquiry deleted successfully'
    //     ]);
    // }
}