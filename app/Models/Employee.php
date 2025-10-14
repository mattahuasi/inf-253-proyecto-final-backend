<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 
 *
 * @property int $person_id
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Person $person
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Employee whereUpdatedAt($value)
 * @method static \Database\Factories\EmployeeFactory factory($count = null, $state = [])
 * @property-read \App\Models\User $user
 * @method static Builder<static>|Employee whereLike(string $filter, string $value)
 * @mixin \Eloquent
 */
class Employee extends Model
{
    use HasFactory;

    // public $nameAllowedFilters = [
    //     'names',
    //     'maternal_surname',
    //     'paternal_surname',
    //     'gender',
    //     'phone',
    //     'type'
    // ];


    protected $fillable = [
        'person_id',
        'type'
    ];


    protected $primaryKey = 'person_id';
    public $incrementing = false;

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id')->withDefault();
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'person_id', 'person_id')->withDefault();
    }

    public function scopeWhereLike(Builder $query, string $filter, string $value)
    {
        $personAttributes = ['names', 'paternal_surname', 'maternal_surname', 'gender', 'phone'];
        if (in_array($filter, $personAttributes)) {
            return $query->orWhereHas('person', function ($q) use ($filter, $value) {
                $q->orWhere($filter, 'like', '%' . $value . '%');
            });
        }

        return $query->where($filter, 'like', '%' . $value . '%');
    }

    public function getRouteKeyName(): string
    {
        return 'person_id';
    }
}
