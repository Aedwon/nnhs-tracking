<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLoggingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log login/logout/failed login
        if ($request->routeIs('login') && $request->isMethod('POST')) {
            if ($response->isRedirect() && !session()->has('errors')) {
                \App\Services\AuditLogger::log('Login Success');
            }
        }

        if ($request->routeIs('logout') && $request->isMethod('POST')) {
            \App\Services\AuditLogger::log('Logout');
        }

        return $response;
    }
}
