<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Policy;

class PolicyController extends Controller
{
    // Add or update privacy policy
    public function savePrivacyPolicy(Request $request)
    {
        $request->validate([
            'content' => 'required'
        ]);

        $policy = Policy::updateOrCreate(
            ['type' => 'privacy'],
            ['content' => $request->content]
        );

        return response()->json([
            'message' => 'Privacy Policy saved successfully',
            'data' => $policy
        ]);
    }

    // Get privacy policy
    public function getPrivacyPolicy()
    {
        $policy = Policy::where('type', 'privacy')->first();

        return response()->json($policy);
    }

    // Add or update payment policy
    public function savePaymentPolicy(Request $request)
    {
        $request->validate([
            'content' => 'required'
        ]);

        $policy = Policy::updateOrCreate(
            ['type' => 'payment'],
            ['content' => $request->content]
        );

        return response()->json([
            'message' => 'Payment Policy saved successfully',
            'data' => $policy
        ]);
    }

    // Get payment policy
    public function getPaymentPolicy()
    {
        $policy = Policy::where('type', 'payment')->first();

        return response()->json($policy);
    }
}
