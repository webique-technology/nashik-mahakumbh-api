<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiLanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         // Default Language
        $locale = 'en';

        // Get Language From Header
        if ($request->hasHeader('Accept-Language')) {

            $locale = $request->header('Accept-Language');
        }

        // Allowed Languages
        $availableLocales = ['en', 'hi'];

        // Invalid Language Check
        if (!in_array($locale, $availableLocales)) {

            $locale = 'en';
        }

        // Set Laravel Locale
        app()->setLocale($locale);

        return $next($request);
    }
}
