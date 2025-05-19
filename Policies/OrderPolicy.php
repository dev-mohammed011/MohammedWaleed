<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // يسمح للمشرفين، مديري المبيعات، وموظفي الشحن
        return $user->hasAnyRole(['admin', 'sales_manager', 'shipping_staff']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        // يسمح للعميل برؤية طلبه الخاص أو للمخولين
        return $user->id === $order->user_id || 
               $user->hasAnyRole(['admin', 'sales_manager', 'shipping_staff']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // يسمح للعملاء إنشاء طلبات أو للمخولين
        return $user->hasRole('customer') || 
               $user->hasAnyRole(['admin', 'sales_manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        // يسمح بالتعديل فقط إذا كانت حالة الطلب قابلة للتعديل
        $editableStatuses = ['pending', 'processing'];
        
        return in_array($order->status, $editableStatuses) && (
            $user->hasAnyRole(['admin', 'sales_manager']) ||
            ($user->hasRole('shipping_staff') && $order->status === 'processing')
        );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        // يسمح بالحذف فقط إذا كانت حالة الطلب "قيد الانتظار"
        return $order->status === 'pending' && 
               $user->hasAnyRole(['admin', 'sales_manager']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        // يسمح فقط للمشرفين
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        // يسمح فقط للمشرفين مع التحقق من عدم وجود مدفوعات
        return $user->hasRole('admin') && 
               $order->payments()->doesntExist();
    }
}