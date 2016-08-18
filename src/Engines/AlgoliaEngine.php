<?php

namespace Medibank\Index\Engines;

use AlgoliaSearch\Client as Algolia;
use Illuminate\Support\Collection;

class AlgoliaEngine extends Engine
{
    /**
     * The Algolia client instance.
     *
     * @var Algolia
     */
    protected $algolia;

    /**
     * The name of the index to search.
     *
     * @var String
     */
    protected $index_name;

    /**
     * The name of the ID key on each model.
     *
     * @var String
     */
    protected $model_key = 'ID';

    /**
     * Create a new engine instance.
     *
     * @param  \AlgoliaSearch\Client $algolia
     * @param  String $index_name
     */
    public function __construct(Algolia $algolia, String $index_name)
    {
        $this->algolia = $algolia;
        $this->index_name = $index_name;
    }

    /**
     * Import a collection of models into the index.
     *
     * @param  Collection $models
     * @param int $upload_chunks
     */
    public function import(Collection $models, $upload_chunks = 20)
    {
        $models->chunk($upload_chunks)->each(function ($chunk, $key) {
            $this->update($chunk);
        });
    }

    /**
     * Update the given model in the index.
     *
     * @param  Collection $models
     * @return void
     */
    public function update(Collection $models)
    {
        $index = $this->index();

        $index->addObjects($models->map(function ($model) {
            return array_merge(['objectID' => $this->modelKey($model)], $model);
        })->values()->all());
    }

    /**
     * Remove the given model from the index.
     *
     * @param  Collection $models
     * @return void
     */
    public function delete(Collection $models)
    {
        $index = $this->index();

        $index->deleteObjects(
            $models->map(function ($model) {
                return $this->modelKey($model);
            })->values()->all()
        );
    }

    /**
     * Change the model ID key.
     *
     * @param $model_key
     * @return $this
     */
    public function withKey($model_key)
    {
        $this->model_key = $model_key;

        return $this;
    }

    /**
     * Get the ID of a model.
     *
     * @param $model
     * @return mixed
     * @throws \Exception
     */
    private function modelKey($model)
    {
        if (isset($model[$this->model_key])) {
            return $model[$this->model_key];
        }

        throw new \Exception('Model key not found');
    }

    /**
     * Get the Algolia search index.
     *
     * @return \AlgoliaSearch\Index
     */
    private function index()
    {
        return $this->algolia->initIndex($this->index_name);
    }
}