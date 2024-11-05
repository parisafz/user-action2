<?php

namespace App\Services;

use App\DTOs\UpdateUserDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Class UserService
 * 
 * این کلاس شامل متدهایی برای انجام عملیات مربوط به مدیریت کاربران است.
 * متدها شامل ایجاد، دریافت، بروزرسانی و حذف کاربران می‌باشند.
 */
class UserService
{
    /**
     * دریافت لیست کاربران با صفحه‌بندی.
     *
     * @param int $perPage تعداد کاربران در هر صفحه
     * @param int|null $page شماره صفحه
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllUsers(
        int $perPage = 10,
        int $page = null,
    ) {
        $users = User::select('id', 'role', 'userName', 'firstName', 'lastName', 'email')
            ->paginate($perPage, ['*'], 'page', $page);

        return $users;
    }

    /**
     * ایجاد یک کاربر جدید.
     *
     * @param string $userName نام کاربری
     * @param string $firstName نام کوچک
     * @param string $lastName نام خانوادگی
     * @param string $email ایمیل
     * @param string $password رمز عبور
     * @return User کاربر جدید ایجاد شده
     * @throws ValidationException
     */
    public function createUser(
        string $userName,
        string $firstName,
        string $lastName,
        string $email,
        string $password
    ): User {
        $data = [
            'userName' => $userName,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'password' => $password
        ];

        return User::create($data);
    }

    /**
     * دریافت یک کاربر خاص.
     *
     * @param int $userId شناسه کاربر
     * @return User کاربر
     */
    public function getUserById($userId)
    {
        $user = User::select('id', 'role', 'userName', 'firstName', 'lastName', 'email')->findOrFail($userId);

        return $user;
    }

    /**
     * بروزرسانی اطلاعات یک کاربر خاص.
     *
     * @param UpdateUserDTO $userDTO حاوی اطلاعات جدید کاربر
     * @return User کاربر به‌روز شده
     * @throws ValidationException
     */
    public function updateUser(UpdateUserDTO $userDTO)
    {
        $user = $this->getUserById($userDTO->userId);

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

    /**
     * حذف یک کاربر خاص.
     *
     * @param int $userId شناسه کاربر
     * @return void
     */
    public function deleteUser(int $userId)
    {
        User::where(['id' => $userId])->findOrFail($userId);

        return User::destroy($userId);
    }
}
