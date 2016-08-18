<?php

use Illuminate\Support\Collection;
use Medibank\Index\Engines\AlgoliaEngine;

class EngineTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function testPostData()
    {
        return [
            [
                'ID' => 1
            ]
        ];
    }

    /** @test */
    public function import_adds_objects_to_index()
    {
        $index_name = 'posts';

        $client = Mockery::mock('AlgoliaSearch\Client');
        $client->shouldReceive('initIndex')->with($index_name)->andReturn($index = Mockery::mock('StdClass'));
        $index->shouldReceive('addObjects')->with([[
            'ID' => 1,
            'objectID' => 1,
        ]]);

        $engine = new AlgoliaEngine($client, $index_name);
        $engine->import(Collection::make($this->testPostData()));
    }

    /** @test */
    public function update_adds_objects_to_index()
    {
        $index_name = 'posts';

        $client = Mockery::mock('AlgoliaSearch\Client');
        $client->shouldReceive('initIndex')->with($index_name)->andReturn($index = Mockery::mock('StdClass'));
        $index->shouldReceive('addObjects')->with([[
            'ID' => 1,
            'objectID' => 1,
        ]]);

        $engine = new AlgoliaEngine($client, $index_name);
        $engine->update(Collection::make($this->testPostData()));
    }

    /** @test */
    public function delete_removes_objects_to_index()
    {
        $index_name = 'posts';

        $client = Mockery::mock('AlgoliaSearch\Client');
        $client->shouldReceive('initIndex')->with($index_name)->andReturn($index = Mockery::mock('StdClass'));
        $index->shouldReceive('deleteObjects')->with([1]);

        $engine = new AlgoliaEngine($client, $index_name);
        $engine->delete(Collection::make($this->testPostData()));
    }
}