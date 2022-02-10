<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CronTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to search and import tokens';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("Token Scrap Cron is started.");
    }
}
