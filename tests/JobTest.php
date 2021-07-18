<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Laravel\Lumen\Testing\DatabaseTransactions;

class JobTest extends TestCase
{
    use DatabaseTransactions;

    private function createJob()
    {
        $parameters = [
            'name' => 'testQueue',
            'description' => 'test description',
            'reference' => '12345',
            'schedule_time' => date('H:i'),
        ];

        $this->post('/job/add/', $parameters, []);
        $result = json_decode($this->response->getContent());

        print_r($result);
        return $result;
    }


    public function testRetrieveJob()
    {
        $this->withoutMiddleware();

        $job = $this->createJob();

        $this->get('job/retrieve/'.$job->reference, []);

        $result = json_decode($this->response->getContent(), true);
        print_r($result);

        $this->seeStatusCode(Response::HTTP_OK);
    }

    public function testRemoveJob()
    {
        $this->withoutMiddleware();

        $job = $this->createJob();

        $this->delete('job/remove/'.$job->reference, []);

        $result = json_decode($this->response->getContent(), true);
        print_r($result);

        $this->assertEquals('removed', $result['status']);
        $this->seeStatusCode(Response::HTTP_OK);
    }

    public function testEditJob()
    {
        $this->withoutMiddleware();

        $job = $this->createJob();

        $parameters = [
            'name' => 'updatetest',
        ];

        $this->put('job/edit/'.$job->reference, $parameters, []);

        $result = json_decode($this->response->getContent(), true);
        print_r($result);

        $this->assertEquals('updatetest', $result['name']);
        $this->seeStatusCode(Response::HTTP_RESET_CONTENT);
    }

    public function testCreateJob()
    {
        $this->withoutMiddleware();

        $parameters = [
            'name' => 'testQueue',
            'description' => 'test description',
            'reference' => '12345',
            'schedule_time' => date('H:i'),
        ];

        $this->post('/job/add/', $parameters, []);
        $result = json_decode($this->response->getContent());
        print_r($result);
        $this->seeStatusCode(Response::HTTP_CREATED);

        Artisan::call('get:jobs');
        Artisan::call('queue:work  --sleep=3  --timeout=120 --tries=3 --stop-when-empty');
    }
}
