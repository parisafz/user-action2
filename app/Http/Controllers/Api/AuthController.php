<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\AuthService;
use Illuminate\Http\Request;

/**
 * کلاس AuthController
 * 
 * این کلاس مسئول مدیریت عملیات احراز هویت کاربران شامل 
 * لاگین، لاگ‌اوت و به‌روزرسانی اطلاعات کاربر می‌باشد.
 */
class AuthController extends Controller
{
    protected $authService;

    /**
     * سازنده کلاس AuthController
     *
     * @param AuthService $authService شی سرویس احراز هویت
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * عملیات لاگین کاربر.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            // اعتبارسنجی ورودی‌ها
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            // ارسال درخواست به سرویس
            $user = $this->authService->login($request['email'], $request['password']);

            // بررسی نتیجه و ارسال پاسخ مناسب
            if ($user) {
                return new ApiSuccessResponse($user, 'Login successfuly.');
            }
        } catch (\Exception $e) {
            return new ApiErrorResponse('failed', 'Failed to login: ' . $e->getmessage());
        }
    }

    /**
     * عملیات خروج از سیستم.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {

        try {
            $logout = $this->authService->logout();
            return new ApiSuccessResponse(null, 'Successfully logged out');
        } catch (\Exception $e) {
            return new ApiErrorResponse('message', 'Failed to logout: ' . $e->getmessage());
        }
    }

    /**
     * به‌روزرسانی اطلاعات کاربر احراز هویت شده
     *
     * @param Request $request
     * @param int $userId شناسه کاربر
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        // if (!Auth::check()) {
        //     return new ApiErrorResponse('failed', 'User not authenticated:');
        // }

        try {
            $updatedUser = $this->authService->updateUser(
                $request['username'] ?? null,
                $request['firstName'] ?? null,
                $request['lastName'] ?? null,
                $request['email'] ?? null,
                $request['password'] ?? null
            );

            return new ApiSuccessResponse($updatedUser, 'User updated successfully.');
        } catch (\Exception $e) {
            return new ApiErrorResponse('message', 'Update failed: ' . $e->getMessage(), 400);
        }
    }

    /**
     * دریافت اطلاعات کاربر احراز هویت شده
     *
     * @return \Illuminate\Http\Response
     */
    public function showUser()
    {
        try {
            $user = $this->authService->showDetails();
            return new ApiSuccessResponse($user, 'User retrieved successfully.');
        } catch (\Exception $e) {
            return new ApiErrorResponse('failed', 'User not found.', 404);
        }
    }
}
