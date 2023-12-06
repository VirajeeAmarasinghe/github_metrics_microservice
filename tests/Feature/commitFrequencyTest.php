<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class commitFrequencyTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {


        Http::fake([
            'https://api.github.com/repos/octocat/Hello-World/commits' => Http::response(['data' => 'your_mocked_data'], 200)
        ]);

        $response = Http::get('https://api.github.com/repos/octocat/Hello-World/commits');


        $this->assertTrue($response->status() == 200);;
    }
}
