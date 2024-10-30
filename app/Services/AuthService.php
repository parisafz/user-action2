<?php

namespace App\Services;

use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * انجام عملیات لاگین کاربر.
     *
     * @param string $email
     * @param string $password
     * @return User
     * @throws ValidationException
     */
    public function login(string $email, string $password)
    {
        $data = ['email' => $email, 'password' => $password];

        if (Auth::attempt($data)) {
            $user = Auth::user();

            // ایجاد توکن برای کاربر
            $token = $user->createToken('access_token')->plainTextToken;

            return $token;
        }
    }

    /**
     * انجام عملیات خروج کاربر.
     *
     * @return ApiResponse
     */
    public function logout()
    {
        $user = Auth::user();

        if ($user) {
            $user->tokens()->delete();
            return Auth::guard('api')->user(); // برای گرفتن کاربر احراز هویت شده در guard api

        }
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
                'username' => $user->username,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
            ];

            return $userData;
        }
    }

    /**
     * به‌روزرسانی اطلاعات کاربر احراز هویت شده
     *
     * @param int $userId شناسه کاربر
     * @param string|null $username نام کاربری
     * @param string|null $firstName نام کوچک
     * @param string|null $lastName نام خانوادگی
     * @param string|null $email ایمیل
     * @param string|null $password رمز عبور
     * @return \App\Models\User کاربر به‌روز شده
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateUser(
        ?string $username = null,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $email = null,
        ?string $password = null
    ) {
        // دریافت کاربر احراز هویت شده
        if (Auth::user()) {

            $user = Auth::user();

            $data = [
                'username' => $username,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => $password
            ];

            $validatedData = Validator::make($data, [
                'username' => 'nullable|string|unique:users,username,' . $user->id,
                'first_name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'email' => 'nullable|string|email|unique:users,email,' . $user->id . '|max:255',
                'password' => 'nullable|string|min:8'
            ])->validate();

            if (isset($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            }

            $data = array_filter($validatedData);

            $user->update($data);

            return $user->fresh();
        }
    }
}
