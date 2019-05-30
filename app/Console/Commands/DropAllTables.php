<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use Schema;

class DropAllTables extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'allTables:drop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop All Tables';

    /**
     * Execute the console command.
     *
     * @return void
     */
    protected $counter = 0;

    /**
     * DropAllTables constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        DB::connection()->disableQueryLog();
        Schema::disableForeignKeyConstraints();
        $tables = DB::select('SHOW FULL TABLES');
        foreach ($tables as $table) {
            $tableName = reset($table);
            Schema::dropIfExists($tableName);

            $this->counter++;
            $this->info("{$this->counter}. Drop Table: {$tableName}");
        }
        Schema::enableForeignKeyConstraints();
    }
}
