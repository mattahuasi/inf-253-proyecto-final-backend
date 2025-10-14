<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $price
 * @property string|null $photo
 * @property int $stock
 * @property string $priority
 * @property int $enable
 * @property int $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category $category
 * @method static \Database\Factories\MenuFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereEnable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Menu whereUpdatedAt($value)
 * @method static Builder<static>|Menu newModelQuery()
 * @method static Builder<static>|Menu newQuery()
 * @method static Builder<static>|Menu orderByPriority($sortDirection = 'asc')
 * @method static Builder<static>|Menu query()
 * @method static Builder<static>|Menu whereCategoryId($value)
 * @method static Builder<static>|Menu whereCreatedAt($value)
 * @method static Builder<static>|Menu whereDescription($value)
 * @method static Builder<static>|Menu whereEnable($value)
 * @method static Builder<static>|Menu whereHasCategories($categories)
 * @method static Builder<static>|Menu whereId($value)
 * @method static Builder<static>|Menu whereName($value)
 * @method static Builder<static>|Menu wherePhoto($value)
 * @method static Builder<static>|Menu wherePrice($value)
 * @method static Builder<static>|Menu wherePriority($value)
 * @method static Builder<static>|Menu whereSlug($value)
 * @method static Builder<static>|Menu whereStock($value)
 * @method static Builder<static>|Menu whereUpdatedAt($value)
 * @method static Builder<static>|Menu whereInHasCategories($categories)
 * @property int $enabled
 * @property-read mixed $photo_url
 * @method static Builder<static>|Menu whereEnabled($value)
 * @mixin \Eloquent
 */
class Menu extends Model
{
    use HasFactory;

    // public $resourceType = 'menus';

    protected $fillable = ['name', 'slug', 'description', 'price', 'photo', 'stock', 'priority', 'enabled', 'category_id'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->withDefault();
    }

    public function scopeWhereInHasCategories(Builder $query, $categories)
    {
        $category_slugs = explode(',', $categories);
        $query->whereHas('category', function ($q) use ($category_slugs) {
            $q->whereIn('slug', $category_slugs);
        });
    }

    public function scopeOrderByPriority(Builder $query, $sortDirection = 'asc')
    {
        return $query->orderByRaw("
            CASE priority
                WHEN 'H' THEN 1
                WHEN 'M' THEN 2
                WHEN 'L' THEN 3
                ELSE 4
            END $sortDirection
        ");
    }

    public function getPhotoUrlAttribute()
    {
        $path = 'menus/photos/' . $this->photo;
        if (Storage::disk('public')->exists($path))
            return Storage::disk('public')->url($path);
        return null;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
