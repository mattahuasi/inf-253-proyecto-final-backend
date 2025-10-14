<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $priority
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Menu> $menus
 * @property-read int|null $menus_count
 * @method static \Database\Factories\CategoryFactory factory($count = null, $state = [])
 * @method static Builder<static>|Category newModelQuery()
 * @method static Builder<static>|Category newQuery()
 * @method static Builder<static>|Category query()
 * @method static Builder<static>|Category whereCreatedAt($value)
 * @method static Builder<static>|Category whereDescription($value)
 * @method static Builder<static>|Category whereId($value)
 * @method static Builder<static>|Category whereName($value)
 * @method static Builder<static>|Category wherePriority($value)
 * @method static Builder<static>|Category whereRouteKey(string $value)
 * @method static Builder<static>|Category whereSlug($value)
 * @method static Builder<static>|Category whereUpdatedAt($value)
 * @method static Builder<static>|Category newModelQuery()
 * @method static Builder<static>|Category newQuery()
 * @method static Builder<static>|Category query()
 * @method static Builder<static>|Category whereCreatedAt($value)
 * @method static Builder<static>|Category whereDescription($value)
 * @method static Builder<static>|Category whereId($value)
 * @method static Builder<static>|Category whereName($value)
 * @method static Builder<static>|Category wherePriority($value)
 * @method static Builder<static>|Category whereRouteKey(string $value)
 * @method static Builder<static>|Category whereSlug($value)
 * @method static Builder<static>|Category whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Category extends Model
{
    use HasFactory;

    // public $resourceType = 'categories';

    protected $fillable = ['name', 'slug', 'description', 'priority'];

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class)->chaperone();
    }

    // public function scopeWhereLike(Builder $query, string $filter, string $value) {
    //     $query->where($filter, 'LIKE', '%' . $value . '%');
    // }

    public function scopeWhereRouteKey(Builder $query, string $value)
    {
        $query->where($this->getRouteKeyName(), $value);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
