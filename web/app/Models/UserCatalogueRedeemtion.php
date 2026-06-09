<?php

namespace App\Models;

use App\Models\User;
use App\Models\Catalogue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserCatalogueRedeemtion extends Model
{
    use HasFactory;
    protected $guarded = [] ;
    protected $primaryKey = 'id';

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
        'order_pending_reason' => 'string',
    ];

    public function getDeliveryConfirmationStatus()
    {
        // if($this->is_delivery_confirmed == self::IS_DELIVERY_CONFIRMED_YES)
        // {
        //     return "Yes";
        // }
        // return "No";

        $status = [ self::IS_DELIVERY_CONFIRMED_YES => 'Yes', self::IS_DELIVERY_CONFIRMED_NO => 'No'] ;

        return $status[$this->is_delivery_confirmed] ?? "" ;
    }
        public function getStatus(): string
    {
        if($this->status == self::STATUS_PENDING)
        {
            return "Pending";
        }
        if($this->status == self::STATUS_DELIVERED)
        {
            return "Delivered";
        }
        if($this->status == self::STATUS_REJECTED)
        {
            return "Rejected";
        }
        if($this->status == self::STATUS_ORDER_PLACED)
        {
            return "Order Placed";
        }
        if($this->status == self::STATUS_UNDELIVERED)
        {
            return "Undelivered";
        }
        if($this->status == self::STATUS_DELIVERY_ACKNOWLEDGEMENT)
        {
            return "Delivery Acknowledgement";
        }
        if($this->status == self::STATUS_COMPLAINT_FEEDBACK)
        {
            return "Complaint / Feedback";
        }
        return "";
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
