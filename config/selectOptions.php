<?php

return [
    'common' => [
        'pagination' => 20,
        'date'       => 'Y-m-d',
        'dateTime'   => 'Y-m-d H:i',
        'dateTimeDB' => 'Y-m-d H:i:s',
        'time'       => 'H:i',
        'status'     => ['active', 'inactive'],
    ],

    'gift_types' => [
        'types' => ['money', 'bonus_points', 'prize'],
    ],

    'prize_types' => [
        'types' => ['чашка', 'ложка', 'вилка', 'нож'],
    ],

    'user_gifts' => [
        'status' => ['put', 'withdrawn', 'exchanged', 'canceled', 'ready_to_send', 'sent_out'],
    ],
];
