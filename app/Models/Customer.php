<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * 
 *
 * @property int $person_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Person $person
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereUpdatedAt($value)
 * @method static \Database\Factories\CustomerFactory factory($count = null, $state = [])
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 */
class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_id'
    ];


    protected $primaryKey = 'person_id';
    public $incrementing = false;
    // protected $keyType = 'unsignedBigInteger';


    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id')->withDefault();
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'person_id', 'person_id')->withDefault();
    }

    public function getRouteKeyName(): string
    {
        return 'person_id';
    }
}
