<?php

namespace App\Core\Console\Commands;

use Illuminate\Console\Command;

class SayHello extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'say:hello {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Say hello to custom user';

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
        $name = $this->ask("What's your name?");
        dd("Hello ".$name);
        return 1;
    }
}
