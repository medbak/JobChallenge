<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;

class testQueue extends Job
{
    public $jobId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($jobId)
    {
        $this->jobId = $jobId;
    }
    /**
     * Execute the job.
     *
     * @param
     * @return bool
     */

    public function handle() {

        Log::info('Test Queue job id '.$this->jobId);

        $result = 'success';

        try {
            echo json_encode(['job_is_executed' => true, 'run_status' => 'success']);
            Log::info('job id '.$this->jobId.' has successfully executed');

        } catch (\Exception $e) {
            echo json_encode(['job_is_executed' => false, 'run_status' => 'failure']);
            Log::info('job id '.$this->jobId. ' has failed');
            $result = 'failure';
        }

        $updated = [
            'run_status' => $result,
        ];

        \App\Models\Job::where('id', $this->jobId)
            ->update($updated);

        return $result;
    }
}