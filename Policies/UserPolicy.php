<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // يسمح فقط للمشرفين ومديري الموارد البشرية
        return $user->hasAnyRole(['admin', 'hr_manager']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // يسمح للمستخدم برؤية ملفه أو للمخولين
        return $user->id === $model->id || 
                $user->hasAnyRole(['admin', 'hr_manager']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // يسمح فقط للمشرفين ومديري الموارد البشرية
        return $user->hasAnyRole(['admin', 'hr_manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // يسمح بتعديل الملف الشخصي أو للمخولين
        // مع منع تعديل الأدوار إلا للمشرفين
        return $user->id === $model->id || 
            $user->hasAnyRole(['admin', 'hr_manager']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // يمنع حذف النفس ويسمح فقط للمشرفين
        return $user->id !== $model->id && 
            $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        // يسمح فقط للمشرفين
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // يسمح فقط للمشرفين
        return $user->hasRole('admin');
    }
}