<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleManager
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $authUserRole = Auth::user()->role;

        switch ($role) {
            case 'admin':
                if ($authUserRole == 0) {
                    return $next($request);
                }
                break;
            case 'vendor':
                if ($authUserRole == 1) {
                    return $next($request);
                }
                break;
            case 'customer':
                if ($authUserRole == 2) {
                    return $next($request);
                }
                break;
        }

        // Nếu không đúng role, chuyển hướng về đúng dashboard
        switch ($authUserRole) {
            case 0:
                return redirect()->route('admin');
            case 1:
                return redirect()->route('vendor');
            case 2:
                return redirect()->route('dashboard');
        }

        return redirect()->route('login');
    }
}
