<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GetJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $listJobs =  DB::connection('database1')->table('job')
            ->where('status', 'scheduled')
            ->where('schedule_time', date("H:i"))
            ->get();

        $class = '\App\Jobs';

        if(!empty($listJobs))
        {
            foreach ($listJobs as $elem)
            {
                $class .= "\ ".$elem->name;
                $class = str_replace(' ', '', $class);

                if (class_exists($class)) {

                    $job = (new $class($elem->id));
                    dispatch($job);

                    $updated = [
                        'status' => 'run',
                    ];

                    DB::connection('database1')->table('job')->where('id', $elem->id)
                        ->update($updated);

                }else{
                    Log::info('Job '.$class.' does not exist');
                }
            }
        }

        return true;
    }
}
