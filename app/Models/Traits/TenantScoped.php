<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait TenantScoped
{
    /**
     * Boot the tenant scoping trait.
     */
    public static function bootTenantScoped(): void
    {
        // Automatically set the admin_id when creating a new record
        static::creating(function (Model $model) {
            if (!$model->admin_id) {
                if (Auth::check() && Auth::user()->isAdmin()) {
                    $model->admin_id = Auth::id();
                } elseif (app()->runningUnitTests()) {
                    $firstAdmin = \App\Models\User::where('role', 'admin')->first();
                    if ($firstAdmin) {
                        $model->admin_id = $firstAdmin->id;
                    }
                }
            }
        });

        // Automatically scope all queries if the logged-in user is an admin
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->isAdmin()) {
                    $builder->where($builder->getQuery()->from . '.admin_id', $user->id);
                }
            }
        });
    }
}
