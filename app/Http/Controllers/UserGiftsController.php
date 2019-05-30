<?php

namespace App\Http\Controllers;

use App\Models\GiftType;
use App\Models\PrizeType;
use App\Models\Setting;
use App\Models\UserGift;

class UserGiftsController extends Controller
{
    /**
     * Сoefficient for exchange
     *
     */
    private $coefficient = 2;

    /**
     * Get userGift for current user.
     *
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        $settings = Setting::all();
        $params   = $settings->where('value', '>', 0)->pluck('key')->all();
        array_push($params, 'bonus_points');
        $gift_type  = GiftType::active()->whereIn('name', $params)->inRandomOrder()->first();
        $value      = null;
        $prize_type = null;
        if ($gift_type->name !== 'prize') {
            $value = rand(10, 100);
            $value = $gift_type->name === 'money' && $settings->where('key', 'money')->first()->value < $value
                ? $settings->where('key', 'money')->first()->value : $value;
        } else {
            $prize_type = PrizeType::active()->inRandomOrder()->first();
        }

        \DB::transaction(function () use ($gift_type, $value, $prize_type) {
            $userGift                = new UserGift();
            $userGift->user_id       = auth()->id();
            $userGift->gift_type_id  = $gift_type->id;
            $userGift->prize_type_id = isset($prize_type) ? $prize_type->id : null;
            $userGift->value         = $value ?? null;
            $userGift->status        = $gift_type->name !== 'prize' ? 'put' : 'ready_to_send';
            $userGift->save();

            if ($gift_type->name !== 'prize') {
                $user    = auth()->user();
                $balance = $gift_type->name === 'money' ? 'money_balance' : 'bonus_balance';

                $user->update([$balance => $user->{$balance} + $value]);
            }

            if (in_array($gift_type->name, ['money', 'prize'])) {
                $setting = Setting::where('key', $gift_type->name)->first();
                $setting->update(['value' => $setting->value - ($gift_type->name === 'money' ? $value : 1)]);
            }
        });

        return redirect()->route('dashboard')->with(['success' => "Приз успешно добавлен!"]);
    }

    /**
     * Withdraw money to user bank account.
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     */
    public function withdraw($id)
    {
        $userGift = UserGift::whereId($id)->first();
        $user     = auth()->user();
        $balance  = $userGift->getTypeOfBalance($userGift->giftType->name);

        if ( ! $userGift) {
            return redirect()->route('dashboard')->with(['error' => "Приза с таким id не существует!"]);
        } elseif ($userGift->user_id !== auth()->id()) {
            return redirect()->route('dashboard')->with(['error' => "Приз с таким id принадлежит другому игроку!"]);
        } elseif ($userGift->prize_type_id && $userGift->status === 'sent_out') {
            return redirect()->route('dashboard')->with(['error' => "Приз с таким id уже отправлен игроку!"]);
        } elseif ($userGift->prize_type_id && $userGift->value) {
            return redirect()->route('dashboard')->with(['error' => "Приз с таким id уже отправлен игроку!"]);
        } elseif ( ! ($userGift->prize_type_id) && $user->{$balance} < $userGift->value) {
            return redirect()->route('dashboard')->with(['error' => "Недостаточно средств на счёте!"]);
        } elseif ($userGift->giftType->name !== 'money') {
            return redirect()->route('dashboard')->with(['error' => "Конвертировать можно только деньги!"]);
        }

        \DB::transaction(function () use ($userGift, $user, $balance) {
            $user->update(['money_balance' => $user->money_balance - $userGift->value]);

            $userGift->status = 'withdrawn';
            $userGift->save();

            $userGiftClone          = array_except($userGift->toArray(), ['id']);
            $userGiftClone['value'] = -$userGiftClone['value'];
            UserGift::create($userGiftClone);
        });

        return redirect()->route('dashboard')->with(['success' => "Вы успешно вывели деньги!"]);
    }

    /**
     * Convert money to bonus points.
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     */
    public function exchange($id)
    {
        $userGift = UserGift::whereId($id)->first();
        $user     = auth()->user();
        $balance  = $userGift->getTypeOfBalance($userGift->giftType->name);

        if ( ! $userGift) {
            return redirect()->route('dashboard')->with(['error' => "Приза с таким id не существует!"]);
        } elseif ($userGift->user_id !== auth()->id()) {
            return redirect()->route('dashboard')->with(['error' => "Приз с таким id принадлежит другому игроку!"]);
        } elseif ($userGift->prize_type_id && $userGift->status === 'sent_out') {
            return redirect()->route('dashboard')->with(['error' => "Приз с таким id уже отправлен игроку!"]);
        } elseif ($userGift->prize_type_id && $userGift->value) {
            return redirect()->route('dashboard')->with(['error' => "Приз с таким id уже отправлен игроку!"]);
        } elseif ( ! ($userGift->prize_type_id) && $user->{$balance} < $userGift->value) {
            return redirect()->route('dashboard')->with(['error' => "Недостаточно средств на счёте!"]);
        } elseif ($userGift->giftType->name !== 'money') {
            return redirect()->route('dashboard')->with(['error' => "Конвертировать можно только деньги!"]);
        }

        \DB::transaction(function () use ($userGift, $user, $balance) {
            $user->update(['bonus_balance' => $user->bonus_balance + $userGift->value * $this->coefficient]);
            $user->update(['money_balance' => $user->money_balance - $userGift->value]);

            $userGift->status = 'exchanged';
            $userGift->save();

            $userGiftClone          = array_except($userGift->toArray(), ['id']);
            $userGiftClone['value'] = -$userGiftClone['value'];
            UserGift::create($userGiftClone);

            $userGiftClone                 = array_except($userGift->toArray(), ['id']);
            $userGiftClone['value']        = $userGiftClone['value'] * $this->coefficient;
            $userGiftClone['gift_type_id'] = 2;
            UserGift::create($userGiftClone);
        });

        return redirect()->route('dashboard')->with(['success' => "Вы успешно обменяли деньги!"]);
    }

    /**
     * Destroy userGift.
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        $userGift = UserGift::whereId($id)->first();
        $user     = auth()->user();
        $balance  = $userGift->getTypeOfBalance($userGift->giftType->name);

        if ( ! $userGift) {
            return redirect()->route('dashboard')->with(['error' => "Приза с таким id не существует!"]);
        } elseif ($userGift->user_id !== auth()->id()) {
            return redirect()->route('dashboard')->with(['error' => "Приза с таким id принадлежит другому игроку!"]);
        } elseif ($userGift->prize_type_id && $userGift->status === 'sent_out') {
            return redirect()->route('dashboard')->with(['error' => "Приз с таким id уже отправлен игроку!"]);
        } elseif ($userGift->prize_type_id && $userGift->value) {
            return redirect()->route('dashboard')->with(['error' => "Приз с таким id уже отправлен игроку!"]);
        } elseif ( ! ($userGift->prize_type_id) && $user->{$balance} < $userGift->value) {
            return redirect()->route('dashboard')->with(['error' => "Недостаточно средств на счёте!"]);
        }

        \DB::transaction(function () use ($userGift, $user, $balance) {
            if ( ! ($userGift->prize_type_id)) {
                $user->update([$balance => $user->{$balance} - $userGift->value]);
            }
            $userGift->status = 'canceled';
            $userGift->save();

            if($userGift->giftType->name !== 'prize') {
                $userGiftClone          = array_except($userGift->toArray(), ['id']);
                $userGiftClone['value'] = -$userGiftClone['value'];
                UserGift::create($userGiftClone);
            }

            if (in_array($userGift->giftType->name, ['money', 'prize'])) {
                $setting = Setting::where('key', $userGift->giftType->name)->first();
                $setting->update(['value' => $setting->value + ($userGift->giftType->name === 'money' ? $userGift->value : 1)]);
            }
        });


        return redirect()->route('dashboard')->with(['success' => "Вы успешно отказались от приза!"]);
    }
}
