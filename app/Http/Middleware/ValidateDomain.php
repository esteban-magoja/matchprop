<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedDomains = explode(',', env('ALLOWED_DOMAINS', ''));
        $currentDomain = $request->getHost();

        if (!empty($allowedDomains) && !in_array($currentDomain, $allowedDomains)) {
            // Redirigir al dominio principal
            $primaryDomain = $allowedDomains[0] ?? env('APP_URL');
            $url = ($request->secure() ? 'https://' : 'http://') . $primaryDomain . $request->getRequestUri();
            
            return redirect($url, 301);
        }

        return $next($request);
    }
}
