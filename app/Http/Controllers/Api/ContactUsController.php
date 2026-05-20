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
        $query = ContactUs::query();
       
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('mobile_number', 'LIKE', "%{$search}%")
                  ->orWhere('inquiry_type', 'LIKE', "%{$search}%");
            });
        }

        $contacts = $query
            ->latest()
            ->paginate(10);

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