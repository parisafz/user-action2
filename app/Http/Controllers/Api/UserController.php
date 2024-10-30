<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class UserController
 * 
 * این کلاس شامل متدهایی برای مدیریت کاربران است.
 * متدها شامل ایجاد، دریافت، بروزرسانی و حذف کاربران می‌باشند.
 */
class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * نمایش لیست تمامی کاربران.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->input('per_page', 10);
            $users = $this->userService->getAllUsers($perPage);

            return new ApiSuccessResponse($users, 'Users retrieved successfully.');
        } catch (\Exception $e) {
            return new ApiErrorResponse('message', 'Failed to fetch users: ' . $e->getMessage(), 400);
        }
    }

    /**
     * ایجاد یک کاربر جدید.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = $this->userService->createUser(
                $request['username'],
                $request['first_name'],
                $request['last_name'],
                $request['email'],
                $request['password']
            );
            return new ApiSuccessResponse($user, 'User registered successfully.', 201);
        } catch (\Exception $e) {
            return new ApiErrorResponse('message', 'Failed to register user: ' . $e->getMessage(), 400);
        }
    }

    /**
     * نمایش یک کاربر خاص.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($userId)
    {
        try {
            $user = $this->userService->getUserById($userId);
            return new ApiSuccessResponse($user, 'User retrieved successfully.');
        } catch (\Exception $e) {
            return new ApiErrorResponse('failed', 'User not found.', 404);
        }
    }

    /**
     * بروزرسانی اطلاعات یک کاربر خاص.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $userId)
    {
        try {
            $updatedUser = $this->userService->updateUser(
                $userId,
                $request['username'] ?? null,
                $request['first_name'] ?? null,
                $request['last_name'] ?? null,
                $request['email'] ?? null,
                $request['password'] ?? null
            );

            return new ApiSuccessResponse($updatedUser, 'User updated successfully.');
        } catch (ModelNotFoundException $e) {
            return new ApiErrorResponse('message', 'User not found.', 404);
        } catch (\Exception $e) {
            return new ApiErrorResponse('message', 'Update failed: ' . $e->getMessage(), 400);
        }
    }

    /**
     * حذف یک کاربر خاص.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->userService->deleteUser($id);

            return new ApiSuccessResponse(null, 'User deleted successfully.');
        } catch (\Exception $e) {
            return new ApiErrorResponse('message', 'Delete failed.', 404);
        }
    }
}
