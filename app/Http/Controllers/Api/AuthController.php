<?php

namespace App\Http\Controllers\Api;

use App\DTOs\LoginDTO;
use App\DTOs\LogoutDTO;
use App\DTOs\UpdateUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * @param Request $request درخواست حاوی اطلاعات کاربر
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            $authDTO = new LoginDTO($request->all());

            // ارسال درخواست به سرویس
            $user = $this->authService->login($authDTO);

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
            $userId = Auth::id();

            $logoutDTO = new LogoutDTO($userId);

            $logout = $this->authService->logout($logoutDTO);

            return new ApiSuccessResponse(null, 'Successfully logged out');
        } catch (\Exception $e) {
            return new ApiErrorResponse('message', 'Failed to logout: ' . $e->getmessage());
        }
    }

    /**
     * به‌روزرسانی اطلاعات کاربر احراز هویت شده.
     *
     * @param Request $request درخواست حاوی اطلاعات جدید کاربر
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $userId = Auth::id();

        try {
            $userDTO = new UpdateUserDTO(
                $userId,
                $request->input('userName'),
                $request->input('firstName'),
                $request->input('lastName'),
                $request->input('email'),
                $request->input('password')
            );

            $updatedUser = $this->authService->updateUser($userDTO);

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
            return new ApiErrorResponse('failed', 'User not found, first login.', 404);
        }
    }
}
