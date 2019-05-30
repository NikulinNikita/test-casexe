<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\UserGift;

class PagesController extends Controller
{
    /**
     * Return dashboard page.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $settings  = Setting::all();
        $userGifts = UserGift::with(['giftType', 'prizeType'])->where('user_id', auth()->id())
                             ->where(function ($q1) {
                                 $q1->where('value', '>', 0)->orWhere('gift_type_id', 3);
                             })
                             ->orderBy('created_at', 'desc')->paginate(5);


        return view('dashboard', compact('settings', 'userGifts'));
    }
}
