<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * 
 *
 * @property int $id
 * @property string|null $paternal_surname
 * @property string|null $maternal_surname
 * @property string $names
 * @property string $gender
 * @property string|null $phone
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereMaternalSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereNames($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person wherePaternalSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereMaternalSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereNames($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person wherePaternalSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address> $addresses
 * @property-read int|null $addresses_count
 * @property-read \App\Models\Customer $customer
 * @property-read \App\Models\Employee $employee
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\PersonFactory factory($count = null, $state = [])
 * @property-read mixed $type
 * @mixin \Eloquent
 */
class Person extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    protected $table = 'people';

    protected $fillable = [
        'paternal_surname',
        'maternal_surname',
        'names',
        'gender',
        'phone'
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class)->chaperone()->withDefault();
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class)->chaperone()->withDefault();
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class)->chaperone()->withDefault();
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class)->chaperone();
    }

    public function getTypeAttribute()
    {
        if ($this->employee()->exists()) {
            return 'employee';
        } elseif ($this->customer()->exists()) {
            return 'customer';
        }
        return 'customer';
    }

    protected function names(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => Str::headline($value),
            set: fn(string $value) => strtolower($value),
        );
    }

    protected function paternalSurname(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => ucfirst($value),
            set: fn(string $value) => strtolower($value),
        );
    }

    protected function maternalSurname(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => ucfirst($value),
            set: fn(string $value) => strtolower($value),
        );
    }
}
