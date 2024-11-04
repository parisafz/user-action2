<?php

namespace App\DTOs;

use WendellAdriel\ValidatedDTO\Attributes\Cast;
use WendellAdriel\ValidatedDTO\Attributes\Rules;
use WendellAdriel\ValidatedDTO\Casting\IntegerCast;
use WendellAdriel\ValidatedDTO\Casting\StringCast;

/**
 * کلاس UpdateUserDTO برای مدیریت اطلاعات به‌روزرسانی کاربر.
 * 
 * این کلاس شامل فیلدهای لازم برای به‌روزرسانی اطلاعات کاربر است.
 */
class UpdateUserDTO
{
    /**
     * @var int شناسه کاربر که اطلاعات آن باید به‌روزرسانی شود.
     */
    #[Rules(['nullable', 'integer', 'unique:users', 'max:255'])]
    #[Cast(IntegerCast::class)]
    public int $user_id;

    /**
     * @var string|null نام کاربری جدید کاربر که باید حداکثر 255 کاراکتر باشد.
     */
    #[Rules(['nullable', 'string', 'unique:users', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $username;

    /**
     * @var string|null نام کوچک جدید کاربر که حداکثر باید 255 کاراکتر باشد.
     */
    #[Rules(['nullable', 'string', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $first_name;

    /**
     * @var string|null نام خانوادگی جدید کاربر که حداکثر باید 255 کاراکتر باشد.
     */
    #[Rules(['nullable', 'string', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $last_name;

    /**
     * @var string|null ایمیل جدید کاربر که باید معتبر و حداکثر 255 کاراکتر باشد.
     */
    #[Rules(['nullable', 'string', 'unique:users', 'email', 'max:255'])]
    #[Cast(StringCast::class)]
    public ?string $email;

    /**
     * @var string|null رمز عبور جدید کاربر که حداقل باید 8 کاراکتر باشد.
     */
    #[Rules(['nullable', 'string', 'min:8'])]
    #[Cast(StringCast::class)]
    public ?string $password;

    /**
     * سازنده کلاس UpdateUserDTO.
     *
     * @param int $user_id شناسه کاربر.
     * @param string|null $username نام کاربری جدید.
     * @param string|null $first_name نام کوچک جدید.
     * @param string|null $last_name نام خانوادگی جدید.
     * @param string|null $email ایمیل جدید.
     * @param string|null $password رمز عبور جدید.
     */
    public function __construct(
        int $user_id,
        ?string $username,
        ?string $first_name,
        ?string $last_name,
        ?string $email,
        ?string $password
    ) {
        $this->user_id = $user_id;
        $this->username = $username;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->password = $password;
    }
}