<?php

namespace App\Models;

class UserGift extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'gift_type_id',
        'prize_type_id',
        'value',
        'status',
    ];

    /**
     * Relation with User
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation with GiftType
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function giftType()
    {
        return $this->belongsTo(GiftType::class);
    }

    /**
     * Relation with PrizeType
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function prizeType()
    {
        return $this->belongsTo(PrizeType::class);
    }

    /**
     * Get Type Of Balance
     *
     * @param $name
     *
     * @return null|string
     */
    public function getTypeOfBalance($name)
    {
        $typeOfBalance  =
            $name === 'money' ? 'money_balance'
                : ($name === 'bonus_points' ? 'bonus_balance' : null);

        return $typeOfBalance;
    }
}
