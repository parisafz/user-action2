<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * کلاس AdminMiddleware
 * 
 * این کلاس مسئول بررسی نقش کاربر برای تأیید دسترسی به 
 * مسیرهای خاصی است که فقط برای مدیران مجاز است.
 */
class AdminMiddleware
{
    /**
     * مدیریت یک درخواست ورودی.
     *
     * @param  \Illuminate\Http\Request  $request درخواست ورودی
     * @param  \Closure  $next تابع بعدی در زنجیره میانه‌افزار
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // بررسی نقش کاربر
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized.'], 403);
    }
}
