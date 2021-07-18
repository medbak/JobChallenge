<?php

namespace App\Http\Controllers;

use App\Interfaces\JobInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class JobController extends Controller
{
    private $job;

    public function __construct(JobInterface $job)
    {
        $this->job = $job;
    }

    public function createJob(Request $request)
    {
        $params = [
            'name' =>$request->input('name'),
            'description' => $request->input('description'),
            'reference' => $request->input('reference'),
            'schedule_time' => $request->input('schedule_time'),
        ];

        $params= array_filter($params, function($value) { return !is_null($value) && $value !== ''; });

        $result = $this->job->createJob($params);

        return response()->json($result, Response::HTTP_CREATED);
    }

    public function removeJob($reference)
    {
        $result = $this->job->removeJob($reference);
        return response()->json($result);
    }

    public function retrieveJob($reference)
    {
        $result = $this->job->retrieveJob($reference);
        return response()->json($result);
    }

    public function editJob($reference, Request $request)
    {
        $params = [
            'name' =>$request->input('name'),
            'description' => $request->input('description'),
            'schedule_time' => $request->input('schedule_time'),
        ];

        $params= array_filter($params, function($value) { return !is_null($value) && $value !== ''; });

        $result = $this->job->editJob($reference, $params);
        return response()->json($result, Response::HTTP_RESET_CONTENT);
    }
}
