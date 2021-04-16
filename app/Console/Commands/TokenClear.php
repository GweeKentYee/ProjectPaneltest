<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TokenClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove tokens expired for more than a day.';

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
        DB::table('oauth_access_tokens')->whereDate('expires_at', '<', now())->delete();
    }
}
