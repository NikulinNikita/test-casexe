<?php

namespace App\Console\Commands;

use App\Models\UserGift;
use Illuminate\Console\Command;

class SendOutPrizes extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'sendOut:prizes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send out all prizes that has status "ready_to_send"';

    /**
     * Counter.
     *
     * @return void
     */
    protected $counter = 0;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        \DB::connection()->disableQueryLog();

        $prizesQuery = UserGift::with(['user', 'prizeType'])->where('prize_type_id', '!=', null)
                               ->where('status', 'ready_to_send');

        $prizesQuery->chunk(200, function ($prizes) {
            foreach ($prizes as $prize) {
                $prize->update(['status' => 'sent_out']);

                $this->counter++;
                $this->info("{$this->counter}. Prize '{$prize->prizeType->name}' for user '{$prize->user->name}' has been sent out!");
            }
        });
    }
}
