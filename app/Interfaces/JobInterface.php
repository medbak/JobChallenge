<?php

namespace App\Interfaces;

interface JobInterface
{
    public function createJob($params);
    public function removeJob($param);
    public function retrieveJob($param);
    public function editJob($reference, $params);
}
