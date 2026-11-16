<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendNFMSMailJob;

class SendNFMSCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iss_nfms:automatic_email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ISS NFMS - Automatic Email';

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
     * @return mixed
     */
    public function handle()
    {
        //
        dispatch(new SendNFMSMailJob());
    }
}
