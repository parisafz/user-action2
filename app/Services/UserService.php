<?php

namespace App\Services;

use App\Http\Responses\ApiErrorResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
     * @param array $data
     * @return User
     * @throws ValidationException
     */
    public function createUser(
        string $username,
        string $firstName,
        string $lastName,
        string $email,
        string $password
    ): User {
        $data = [
            'username' => $username,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $password
        ];

        $this->validateUser($data);

        return User::create($data);
    }

    /**
     * دریافت یک کاربر خاص.
     *
     * @param int $id
     * @return User
     */
    public function getUserById($userId)
    {
        $user = User::select('id', 'role', 'username', 'first_name', 'last_name', 'email')->findOrFail($userId);

        return $user;
    }

    /**
     * بروزرسانی اطلاعات یک کاربر خاص.
     *
     * @param int $id
     * @param array $data
     * @return User
     * @throws ValidationException
     */
    public function updateUser(
        $userId,
        ?string $username,
        ?string $firstName,
        ?string $lastName,
        ?string $email,
        ?string $password
    ) {
        $user = $this->getUserById($userId);

        $data = [
            'username' => $username,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $password
        ];

        $validatedData = Validator::make($data, [
            'username' => 'nullable|string|unique:users,username,' . $userId,
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|unique:users,email,' . $userId . '|max:255',
            'password' => 'nullable|string|min:8'
        ])->validate();

        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $data = array_filter($validatedData);

        $user->update($data);

        return $user->fresh();
    }

    /**
     * حذف یک کاربر خاص.
     *
     * @param int $id
     * @return void
     */
    public function deleteUser(int $userId)
    {
        User::where(['id' => $userId])->findOrFail($userId);

        return User::destroy($userId);
    }

    /**
     * اعتبارسنجی داده‌های کاربر.
     *
     * @param array $data
     * @param int|null $userId
     * @throws ValidationException
     */
    protected function validateUser(array $data)
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|unique:users|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:8',
            'role' => 'in:admin,user',
        ];

        $validatedData = Validator::make($data, $rules);

        return $validatedData;

        if ($validator->fails()) {
            return new ApiErrorResponse('message', 'Failed to validation datas.');
        }
    }
}
