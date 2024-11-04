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
        $users = User::select('id', 'role', 'username', 'first_name', 'last_name', 'email')
            ->paginate($perPage, ['*'], 'page', $page);

        return $users;
    }

    /**
     * ایجاد یک کاربر جدید.
     *
     * @param string $username نام کاربری
     * @param string $first_name نام کوچک
     * @param string $last_name نام خانوادگی
     * @param string $email ایمیل
     * @param string $password رمز عبور
     * @return User کاربر جدید ایجاد شده
     * @throws ValidationException
     */
    public function createUser(
        string $username,
        string $first_name,
        string $last_name,
        string $email,
        string $password
    ): User {
        $data = [
            'username' => $username,
            'first_name' => $first_name,
            'last_name' => $last_name,
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
        $user = User::select('id', 'role', 'username', 'first_name', 'last_name', 'email')->findOrFail($userId);

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
        $user = $this->getUserById($userDTO->user_id);

        $data = [
            'username' => $userDTO->username,
            'first_name' => $userDTO->first_name,
            'last_name' => $userDTO->last_name,
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
