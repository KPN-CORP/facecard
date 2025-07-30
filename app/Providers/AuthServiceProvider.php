<?php
namespace App\Providers;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        try {
            if (Schema::hasTable('permissions')) {
                Permission::all()->each(function ($permission) {
                    Gate::define($permission->name, function (User $user) use ($permission) {
                        return $user->hasPermissionTo($permission->name);
                    });
                });
            }
        } catch (\Exception $e) {}
    }
}