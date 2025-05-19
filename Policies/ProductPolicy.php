<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // يسمح للمشرفين، مديري المنتجات، وموظفي المبيعات
        return $user->hasAnyRole(['admin', 'product_manager', 'sales']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        // يسمح للجميع برؤية المنتجات إلا إذا كانت محذوفة أو غير نشطة
        return $product->is_active || $user->hasAnyRole(['admin', 'product_manager']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // يسمح فقط للمشرفين ومديري المنتجات
        return $user->hasAnyRole(['admin', 'product_manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        // يسمح بالتعديل إذا كان المنتج غير محذوف
        // مع صلاحيات خاصة للمشرفين ومديري المنتجات
        return !$product->trashed() && 
               $user->hasAnyRole(['admin', 'product_manager']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        // يمنع حذف المنتجات المرفوعة في طلبات نشطة
        $hasActiveOrders = $product->orders()->where('status', '!=', 'cancelled')->exists();
        
        return !$hasActiveOrders && 
               $user->hasAnyRole(['admin', 'product_manager']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Product $product): bool
    {
        // يسمح فقط للمشرفين
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        // يسمح فقط للمشرفين مع التحقق من عدم وجود طلبات مرتبطة
        return $user->hasRole('admin') && 
               $product->orders()->doesntExist();
    }
}