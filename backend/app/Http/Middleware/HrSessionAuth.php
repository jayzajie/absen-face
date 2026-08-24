<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HrSessionAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('hr_authenticated')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
