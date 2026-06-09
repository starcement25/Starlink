<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    // public function handle(Request $request, Closure $next)
    // {
    //     $response = $next($request);

    //     // 1. Content-Security-Policy
    //     $response->headers->set('Content-Security-Policy', 
    //         "default-src 'self'; " .
    //         "script-src 'self' 'unsafe-inline' 'unsafe-eval'; " .
    //         "style-src 'self' 'unsafe-inline'; " .
    //         "img-src 'self' data: https:; " .
    //         "font-src 'self' data:; " .
    //         "connect-src 'self'; " .
    //         "frame-ancestors 'none';"
    //     );

    //     // 2. X-Frame-Options
    //     $response->headers->set('X-Frame-Options', 'DENY');

    //     // 3. X-Content-Type-Options
    //     $response->headers->set('X-Content-Type-Options', 'nosniff');

    //     // 4. Referrer-Policy
    //     $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

    //     // 5. Permissions-Policy
    //     $response->headers->set('Permissions-Policy', 
    //         'camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()'
    //     );

    //     // Bonus: Additional recommended headers
    //     $response->headers->set('X-XSS-Protection', '1; mode=block');
    //     $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

    //     return $response;
    // }

    // public function handle(Request $request, Closure $next)
    // {
    //     $response = $next($request);

    //     // ✅ FIXED: Allows your own CSS, JS, fonts, and images to load
    //     $response->headers->set('Content-Security-Policy', 
    //         "default-src 'self'; " .
    //         "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net cdnjs.cloudflare.com code.jquery.com; " .
    //         "style-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com fonts.googleapis.com; " .
    //         "font-src 'self' data: fonts.gstatic.com cdn.jsdelivr.net cdnjs.cloudflare.com; " .
    //         "img-src 'self' data: https: blob:; " .
    //         "connect-src 'self'; " .
    //         "frame-src 'none'; " .
    //         "frame-ancestors 'none'; " .
    //         "object-src 'none';"
    //     );

    //     $response->headers->set('X-Frame-Options', 'DENY');
    //     $response->headers->set('X-Content-Type-Options', 'nosniff');
    //     $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    //     $response->headers->set('Permissions-Policy', 
    //         'camera=(), microphone=(), geolocation=(), payment=()'
    //     );
    //     $response->headers->set('X-XSS-Protection', '1; mode=block');
    //     $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

    //     return $response;
    // }

    // public function handle(Request $request, Closure $next)
    // {
    //     $response = $next($request);

    //     $response->headers->set('Content-Security-Policy-Report-Only',
    //         "default-src 'self'; " .

    //         // ✅ JS - added more common CDNs used in admin panels
    //         "script-src 'self' 'unsafe-inline' 'unsafe-eval' " .
    //             "cdn.jsdelivr.net " .
    //             "cdnjs.cloudflare.com " .
    //             "code.jquery.com " .
    //             "ajax.googleapis.com " .
    //             "maxcdn.bootstrapcdn.com " .
    //             "stackpath.bootstrapcdn.com " .
    //             "unpkg.com; " .

    //         // ✅ CSS - added more common CDNs
    //         "style-src 'self' 'unsafe-inline' " .
    //             "cdn.jsdelivr.net " .
    //             "cdnjs.cloudflare.com " .
    //             "fonts.googleapis.com " .
    //             "maxcdn.bootstrapcdn.com " .
    //             "stackpath.bootstrapcdn.com " .
    //             "unpkg.com; " .

    //         // ✅ Fonts
    //         "font-src 'self' data: " .
    //             "fonts.gstatic.com " .
    //             "cdn.jsdelivr.net " .
    //             "cdnjs.cloudflare.com " .
    //             "maxcdn.bootstrapcdn.com " .
    //             "stackpath.bootstrapcdn.com; " .

    //         // ✅ Images - allow all https + data URIs + blob (charts/canvas)
    //         "img-src 'self' data: blob: https:; " .

    //         // ✅ Ajax/Fetch calls
    //         "connect-src 'self'; " .

    //         // ✅ Media (audio/video if admin panel has any)
    //         "media-src 'self'; " .

    //         // ✅ Block iframes
    //         "frame-src 'none'; " .
    //         "frame-ancestors 'none'; " .
    //         "object-src 'none';"
    //     );

    //     $response->headers->set('X-Frame-Options', 'DENY');
    //     $response->headers->set('X-Content-Type-Options', 'nosniff');
    //     $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
    //     $response->headers->set('Permissions-Policy',
    //         'camera=(), microphone=(), geolocation=(), payment=()'
    //     );
    //     $response->headers->set('X-XSS-Protection', '1; mode=block');
    //     $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

    //     return $response;
    // }

     public function handle(Request $request, Closure $next)
    {
        // Generate a unique nonce for each request
        $nonce = base64_encode(random_bytes(16));

        // Share nonce with all views
        app()->instance('csp-nonce', $nonce);
        view()->share('cspNonce', $nonce);

        $response = $next($request);

        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'nonce-{$nonce}' " .
                "cdn.jsdelivr.net " .
                "cdnjs.cloudflare.com " .
                "code.jquery.com " .
                "ajax.googleapis.com " .
                "maxcdn.bootstrapcdn.com " .
                "stackpath.bootstrapcdn.com " .
                "unpkg.com; " .
            "style-src 'self' 'nonce-{$nonce}' " .
                "cdn.jsdelivr.net " .
                "cdnjs.cloudflare.com " .
                "fonts.googleapis.com " .
                "maxcdn.bootstrapcdn.com " .
                "stackpath.bootstrapcdn.com " .
                "unpkg.com; " .
            "font-src 'self' data: " .
                "fonts.gstatic.com " .
                "cdn.jsdelivr.net " .
                "cdnjs.cloudflare.com " .
                "maxcdn.bootstrapcdn.com; " .
            "img-src 'self' data: blob: https:; " .
            "connect-src 'self'; " .
            "media-src 'self'; " .
            "frame-src 'none'; " .
            "frame-ancestors 'none'; " .
            "object-src 'none'; " .
            "base-uri 'self'; " .
            "form-action 'self';"
        );

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()'
        );
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        return $response;
    }
}
