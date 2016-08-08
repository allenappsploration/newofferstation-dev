<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Post extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newos:post';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get post from SocialHub items/feeds table';
    
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
        (new \App\Http\Controllers\PostController())->creation();
        
        $this->info('New posts has been created in table.');
    }
}

