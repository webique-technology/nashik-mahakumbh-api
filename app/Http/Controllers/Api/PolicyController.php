<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Policy;
use App\Services\PolicyTranslationService;

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
    // public function getPrivacyPolicy()
    // {
    //     $policy = Policy::where('type', 'privacy')->first();

    //     return response()->json($policy);
    // }
    public function getPrivacyPolicy(
        Request $request
    ) {
        $lang = $request->get(
            'lang',
            'en'
        );

        $policy = Policy::with(
            'translations'
        )
            ->where(
                'type',
                'privacy'
            )
            ->first();

        if (!$policy) {
            return response()->json(null);
        }

        if ($lang !== 'en') {

            $translation = $policy
                ->translations
                ->where(
                    'language_code',
                    $lang
                )
                ->first();

            if ($translation) {
                $policy->content =
                    $translation->content;
            }
        }

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

    public function translatePolicy(
        $id,
        Request $request,
        PolicyTranslationService $service
    ) {
        $lang = $request->lang;

        $policy = Policy::findOrFail($id);

        $service->translate(
            $policy,
            $lang
        );

        return response()->json([
            'status' => true,
            'message' => "Translated to {$lang}"
        ]);
    }
}
