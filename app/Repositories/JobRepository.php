<?php

namespace App\Repositories;
use App\Exceptions\InsertionException;
use App\Exceptions\ParamsException;
use App\Interfaces\JobInterface;
use App\Models\Job;

class JobRepository implements JobInterface
{
    public function createJob($params)
    {
        if(empty($params))
        {
            throw new ParamsException('Params are empty');
        }
        $job = Job::create($params);
        if (empty($job)) {
            throw new InsertionException("Error in job insertion");
        }
        return $job;
    }

    public function removeJob($param)
    {
        $job = Job::where('reference', $param)
            ->where('status', 'scheduled')
            ->firstOrFail();

        $job->fill(['status' => 'removed'])->save();

        return $job;
    }

    public function retrieveJob($param)
    {
        $job = Job::where('reference', $param)
            ->firstOrFail();

        return $job;
    }

    public function editJob($reference, $params)
    {
        $job = Job::where('reference', $reference)
            ->where('status', 'scheduled')
            ->firstOrFail();

        if(!empty($params))
        {
            $job->fill($params)->save();
        }

        return $job;
    }
}
