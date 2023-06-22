<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Checkrole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) // I included this check because you have it, but it really should be part of your 'auth' middleware, most likely added as part of a route group.
            return redirect('login');

        $user = auth()->user();

        foreach ($roles as $role) {
            // Check if user has the role This check will depend on how your roles are set up
            if ($user->roleuser_id == $role) {
                return $next($request);
            } else {
                abort(403, 'Anda tidak memiliki hak mengakses laman tersebut!');
            }
        }

        return redirect('login');
    }
}
