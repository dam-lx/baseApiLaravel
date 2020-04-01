<?php
    
    namespace App\Core\Commands\Console;
    
    use Carbon\Carbon;
    use Illuminate\Console\Command;
    
    class ClearLogFolder extends Command
    {
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'log:clear';
        
        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Clear Log Laravel';
        
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
            if(env("APP_DEBUG")==true){
                $days       = (int) $this->ask("How many days do you want to delete?");
                $path       = base_path() . "/storage/logs";
                $arrFolders= array_filter(scandir($path), function ($value) {
                    return preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$value);
                });
                $perform = $this->output->createProgressBar(count($arrFolders));
                $now        = Carbon::now(config('app.timezone'));
                $perform->start();
                foreach ($arrFolders as $obj){
                    $perform->advance();
                    if(((int) $now->diffInDays($obj)) > (($days<1)?1:$days))
                    {
                        $dir = $path."/$obj";
                        $files = glob("$dir/*"); //get all file names
                        foreach($files as $file){
                            if(is_file($file))
                                unlink($file); //delete file
                        }
                        rmdir($dir);
                    }
                }
                $perform->finish();
                $this->info("\n"."Delete log successfully!");
            }else{
                $this->warn("Please enable app debug to clear log!");
            }
            return 1;
        }
    }
