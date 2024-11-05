<?php

namespace App\Services;

use App\DTOs\LoginDTO;
use App\DTOs\LogoutDTO;
use App\DTOs\UpdateUserDTO;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * کلاس AuthService
 * 
 * این کلاس مسئول انجام عملیات احراز هویت کاربران شامل 
 * لاگین، لاگ‌اوت و به‌روزرسانی اطلاعات کاربر می‌باشد.
 */
class AuthService
{
    /**
     * انجام عملیات لاگین کاربر.
     *
     * @param LoginDTO $authDTO حاوی اطلاعات ورود کاربر
     * @return string|null توکن دسترسی کاربر یا null در صورت عدم موفقیت
     * @throws ValidationException
     */
    public function login(LoginDTO $authDTO)
    {
        $data = ['email' => $authDTO->email, 'password' => $authDTO->password];

        if (Auth::attempt($data)) {
            $user = Auth::user();

            // ایجاد توکن برای کاربر
            $token = $user->createToken('access_token')->plainTextToken;

            return $token;
        }

        return null; // در صورت عدم موفقیت
    }

    /**
     * انجام عملیات خروج کاربر.
     *
     * @param LogoutDTO $logoutDTO حاوی شناسه کاربر
     * @return bool
     */
    public function logout(LogoutDTO $logoutDTO)
    {
        $user = Auth::user();

        if ($user && $user->id === $logoutDTO->userId) {
            $user->tokens()->delete();

            return true;
        }

        return false; // در صورت عدم موفقیت
    }

    /**
     * نمایش جزئیات کاربر احراز هویت شده.
     *
     * @return array|null اطلاعات کاربر به صورت آرایه یا null اگر کاربر وارد نشده باشد.
     */
    public function showDetails()
    {
        if (Auth::user()) {

            $user = Auth::user();

            $userData = [
                'id' => $user->id,
                'role' => $user->role,
                'userName' => $user->userName,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'email' => $user->email,
            ];

            return $userData;
        }

        return null; // اگر کاربر وارد نشده باشد
    }

    /**
     * به‌روزرسانی اطلاعات کاربر احراز هویت شده.
     *
     * @param UpdateUserDTO $userDTO حاوی اطلاعات جدید کاربر
     * @return User کاربر به‌روز شده
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateUser(UpdateUserDTO $userDTO)
    {
        // دریافت کاربر احراز هویت شده
        if (Auth::user()) {

            $user = Auth::user();

            $data = [
                'userName' => $userDTO->userName,
                'firstName' => $userDTO->firstName,
                'lastName' => $userDTO->lastName,
                'email' => $userDTO->email,
                'password' =>  $userDTO->password ? Hash::make($userDTO->password) : null
            ];

            $user->update(array_filter($data));

            return $user->fresh();
        }

        return null; // اگر کاربر وارد نشده باشد
    }
}
