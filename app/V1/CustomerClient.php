<?php

namespace App\V1;

use Illuminate\Database\Eloquent\Model;

/**
 * App\V1\CustomerClient
 *
 * @property int $id
 * @property int $customer_id
 * @property string $client_id
 * @property string $client
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\V1\CustomerClient whereClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\V1\CustomerClient whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\V1\CustomerClient whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\V1\CustomerClient whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\V1\CustomerClient whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\V1\CustomerClient whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomerClient extends Model
{
    protected $table = 'customer_client';

    protected $fillable = [
        'client_id', 'client'
    ];

    /*---------- RELATION ----------*/
    public function customer()
    {
        return $this->belogsTo('App\V1\Customer');
    }
}
