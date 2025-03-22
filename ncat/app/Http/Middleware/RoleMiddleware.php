<?php
// app/Http/Middleware/RoleMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        // Check if the user is logged in and has the correct role
        if (!Auth::check() || Auth::user()->role !== $role) {
            // If not, abort with a 403 Unauthorized error
            abort(403, 'Unauthorized action.');
        }

        // Otherwise, allow the request to proceed
        return $next($request);
    }
}