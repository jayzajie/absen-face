<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HrBasicAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = (string) config('services.hr.username');
        $password = (string) config('services.hr.password');
        $valid = $user !== '' && $password !== ''
            && hash_equals($user, (string) $request->getUser())
            && hash_equals($password, (string) $request->getPassword());

        return $valid
            ? $next($request)
            : response('Unauthorized', 401, ['WWW-Authenticate' => 'Basic realm="AbsenKu HR"']);
    }
}
