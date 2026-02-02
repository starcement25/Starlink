<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCatalogueRedeemtion extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $guarded = [];

    public const STATUS_PENDING = 0;
    public const STATUS_DELIVERED = 1;
    public const STATUS_REJECTED = 2;
    public const STATUS_ORDER_PLACED = 3;
    public const STATUS_UNDELIVERED = 4;
    public const STATUS_DELIVERY_ACKNOWLEDGEMENT = 5;
    public const STATUS_COMPLAINT_FEEDBACK = 6;

    public const IS_DELIVERY_CONFIRMED_YES = 1;
    public const IS_DELIVERY_CONFIRMED_NO = 0;

    protected $casts = [
        'delivery_confirmation_datetime' => 'datetime',
    ];

    public function getDeliveryConfirmationStatus()
    {
        if($this->is_delivery_confirmed == self::IS_DELIVERY_CONFIRMED_YES)
        {
            return "Yes";
        }
        return "No";
    }

    public function deliveryConfirmedBy()
    {
        return $this->belongsTo(User::class, 'delivery_confirmed_by', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function catalogue()
    {
        return $this->belongsTo(Catalogue::class, 'catalogue_id', 'id');
    }
}
