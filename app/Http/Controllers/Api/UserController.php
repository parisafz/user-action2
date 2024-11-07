<?php

namespace App\Http\Controllers\Api;

use App\DTOs\CreateUserDTO;
use App\DTOs\UpdateUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiErrorResponse;
use App\Http\Responses\ApiSuccessResponse;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserController
 * 
 * این کلاس شامل متدهایی برای مدیریت کاربران است.
 * متدها شامل ایجاد، دریافت، بروزرسانی و حذف کاربران می‌باشند.
 */
class UserController extends Controller
{
    protected $userService;

    /**
     * سازنده کلاس UserController
     *
     * @param UserService $userService سرویس مدیریت کاربران
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * دریافت لیست کاربران.
     *
     * @param Request $request درخواست حاوی پارامترهای جستجو
     * @return ApiSuccessResponse | ApiErrorResponse
     */
    public function getAll(Request $request)
    {
        try {
            $perPage = (int) $request->input('perPage', 10);
            $users = $this->userService->getAll($perPage);

            return new ApiSuccessResponse($users, 'users_retrieved_successfully.');
        } catch (\Exception $e) {
            return new ApiErrorResponse('failed_to_fetch_users: ' . $e->getMessage(), 400);
        }
    }

    /**
     * ایجاد یک کاربر جدید.
     *
     * @param Request $request درخواست حاوی اطلاعات کاربر جدید
     * @return ApiSuccessResponse | ApiErrorResponse
     */
    public function create(Request $request)
    {
        $userDTO = new CreateUserDTO(
            $request['userName'],
            $request['firstName'],
            $request['lastName'],
            $request['email'],
            $request['password']
        );

        try {
            $user = $this->userService->store(
                $userDTO->userName,
                $userDTO->firstName,
                $userDTO->lastName,
                $userDTO->email,
                $userDTO->password
            );

            return new ApiSuccessResponse($user, 'user_registered_successfully.', 201);
        } catch (\Exception $e) {
            return new ApiErrorResponse('registration_failed' . $e->getMessage(), 400);
        }
    }

    /**
     * حذف یک کاربر.
     *
     * @param int $userId شناسه کاربر
     * @return ApiSuccessResponse | ApiErrorResponse
     */
    public function destroy($userId)
    {
        try {
            $this->userService->delete($userId);

            return new ApiSuccessResponse(null, 'deleted_successfully');
        } catch (\Exception $e) {
            return new ApiErrorResponse('delete_failed', 404);
        }
    }

    /**
     * نمایش پروفایل کاربر احراز هویت شده.
     *
     * @return ApiSuccessResponse | ApiErrorResponse
     */
    public function showProfile()
    {
        try {
            $user = $this->userService->showProfile();
            return new ApiSuccessResponse($user, 'user_retrieved_successfully');
        } catch (\Exception $e) {
            return new ApiErrorResponse('unauthenticated', 404);
        }
    }

    /**
     * نمایش یک کاربر خاص.
     *
     * @param int $userId شناسه کاربر
     * @return \Illuminate\Http\Response
     */
    public function show($userId)
    {
        try {
            $user = $this->userService->getById($userId);
            return new ApiSuccessResponse($user, 'User retrieved successfully.');
        } catch (\Exception $e) {
            return new ApiErrorResponse('not_found.', 404);
        }
    }

    /**
     * به‌روزرسانی اطلاعات کاربر احراز هویت شده.
     *
     * @param Request $request درخواست حاوی اطلاعات جدید کاربر
     * @return ApiSuccessResponse | ApiErrorResponse
     */
    public function updateProfile(Request $request): ApiSuccessResponse | ApiErrorResponse
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

            $updatedUser = $this->userService->updateProfile(
                $userId,
                $userDTO->userName,
                $userDTO->firstName,
                $userDTO->lastName,
                $userDTO->email,
                $userDTO->password
            );

            return new ApiSuccessResponse($updatedUser, 'updated_successfully');
        } catch (\Exception $e) {
            return new ApiErrorResponse('update_failed' . $e->getMessage(), 400);
        }
    }

    /**
     * به‌روزرسانی اطلاعات یک کاربر خاص.
     *
     * @param Request $request درخواست حاوی اطلاعات جدید کاربر
     * @param int $userId شناسه کاربر
     * @return ApiSuccessResponse | ApiErrorResponse
     */
    public function update(Request $request, $userId): ApiSuccessResponse | ApiErrorResponse
    {
        try {
            $userDTO = new UpdateUserDTO(
                $userId,
                $request->input('userName'),
                $request->input('firstName'),
                $request->input('lastName'),
                $request->input('email'),
                $request->input('password')
            );

            $updatedUser = $this->userService->update(
                $userId,
                $userDTO->userName,
                $userDTO->firstName,
                $userDTO->lastName,
                $userDTO->email,
                $userDTO->password
            );

            return new ApiSuccessResponse($updatedUser, 'updated_successfully');
        } catch (ModelNotFoundException $e) {
            return new ApiErrorResponse('not_found_user', 404);
        } catch (\Exception $e) {
            return new ApiErrorResponse('update_failed' . $e->getMessage(), 400);
        }
    }
}
