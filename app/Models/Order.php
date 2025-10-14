<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property string $tracking_code
 * @property string $type
 * @property string $ordered_at
 * @property string|null $delivered_at
 * @property string $customer_name
 * @property string $customer_phone
 * @property string|null $comment
 * @property int $payment_made
 * @property int $state_id
 * @property int|null $customer_id
 * @property int|null $address_id
 * @property int $employee_id
 * @property int|null $table_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereOrderedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentMade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTableNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTrackingCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereAddressId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCustomerPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereEmployeeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereOrderedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order wherePaymentMade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTableNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTrackingCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 * @property-read \App\Models\State $state
 * @mixin \Eloquent
 */
class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'tracking_code',
        'type',
        'ordered_at',
        'delivered_at',
        'customer_name',
        'customer_phone',
        'comment',
        'payment_made',
        'state_id',
        'customer_id',
        'address_id',
        'employee_id',
        'table_number'
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class)->withDefault();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'person_id')->withDefault();
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'person_id')->withDefault();
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id', 'id')->withDefault();
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class, 'table_number', 'number')->withDefault();
    }
}
