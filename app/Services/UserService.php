<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
    public function getAll(
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
    public function store(
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
            'password' =>  Hash::make($password)
        ];

        return User::create($data);
    }

    /**
     * به‌روزرسانی اطلاعات کاربر با استفاده از شناسه کاربر.
     *
     * @param int $userId شناسه کاربر
     * @param string|null $userName نام کاربری
     * @param string|null $firstName نام کوچک
     * @param string|null $lastName نام خانوادگی
     * @param string|null $email ایمیل
     * @param string|null $password رمز عبور جدید (اختیاری)
     * @return User اطلاعات به‌روزرسانی شده کاربر
     * @throws Exception در صورت عدم یافتن کاربر
     */
    public function update(
        int $userId,
        ?string $userName,
        ?string $firstName,
        ?string $lastName,
        ?string $email,
        ?string $password = null
    ) {
        $user = $this->getById($userId);

        if (!$user) {
            throw new Exception('not_found');
        }

        $data = [
            'userName' => $userName,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'password' =>  $password ? Hash::make($password) : null
        ];

        $user->update(array_filter($data));

        return $user->fresh();
    }

    /**
     * به‌روزرسانی اطلاعات کاربر وارد شده.
     *
     * @param int $userId شناسه کاربر
     * @param string|null $userName نام کاربری
     * @param string|null $firstName نام کوچک
     * @param string|null $lastName نام خانوادگی
     * @param string|null $email ایمیل
     * @param string|null $password رمز عبور جدید (اختیاری)
     * @return User اطلاعات به‌روزرسانی شده کاربر
     * @throws Exception در صورت عدم احراز هویت یا عدم مجوز
     */
    public function updateProfile(
        int $userId,
        ?string $userName,
        ?string $firstName,
        ?string $lastName,
        ?string $email,
        ?string $password = null
    ) {
        $user = Auth::user();

        if (!$user) {
            throw new Exception('unauthenticated');
        }

        if ($user->id !== $userId) {
            throw new Exception('unauthorized to update this user');
        }

        $data = [
            'userName' => $userName,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'password' =>  $password ? Hash::make($password) : null
        ];

        $user->update(array_filter($data));

        return $user->fresh();
    }

    /**
     * دریافت اطلاعات یک کاربر خاص با شناسه.
     *
     * @param int $userId شناسه کاربر
     * @return User کاربر
     */
    public function getById(int $userId)
    {
        $user = User::select('id', 'role', 'userName', 'firstName', 'lastName', 'email')->findOrFail($userId);

        return $user;
    }

    /**
     * حذف یک کاربر خاص.
     *
     * @param int $userId شناسه کاربر
     * @return void
     */
    public function delete(int $userId)
    {
        User::where(['id' => $userId])->findOrFail($userId);

        return User::destroy($userId);
    }

    /**
     * نمایش پروفایل کاربر وارد شده.
     *
     * @return array اطلاعات پروفایل کاربر
     * @throws Exception در صورت عدم احراز هویت
     */
    public function showProfile()
    {
        $user = Auth::user();

        if ($user) {
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

        throw new Exception('unauthenticated'); // اگر کاربر وارد نشده باشد
    }
}
