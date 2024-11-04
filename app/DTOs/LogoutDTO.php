<?php

namespace App\DTOs;

use WendellAdriel\ValidatedDTO\Attributes\Cast;
use WendellAdriel\ValidatedDTO\Attributes\Rules;
use WendellAdriel\ValidatedDTO\Casting\IntegerCast;

/**
 * کلاس LogoutDTO برای مدیریت اطلاعات خروج کاربر.
 * 
 * این کلاس شامل شناسه کاربر است که می‌خواهد از سیستم خارج شود.
 */
class LogoutDTO
{
    /**
     * @var int شناسه کاربر که برای خروج استفاده می‌شود.
     */
    #[Rules(['nullable', 'integer', 'unique:users', 'max:255'])]
    #[Cast(IntegerCast::class)]
    public int $user_id;

    /**
     * سازنده کلاس LogoutDTO.
     *
     * @param int $user_id شناسه کاربر.
     */
    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;
    }
}
