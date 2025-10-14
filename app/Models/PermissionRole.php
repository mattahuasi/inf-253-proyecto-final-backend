<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * 
 *
 * @property int $permission_id
 * @property int $role_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionRole wherePermissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionRole whereRoleId($value)
 * @mixin \Eloquent
 */
class PermissionRole extends Pivot
{
    /** @use HasFactory<\Database\Factories\PermissionRoleFactory> */
    use HasFactory;
}
