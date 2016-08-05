<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class Fetch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'socialhub:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch SocialHub items/feeds';
    
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
        (new \App\Http\Controllers\SocialHub\FeedController())->index();
        
        $this->info('Items from SocialHub has been updated.');
    }
}

