<?php

namespace Medibank\Index\Engines;

use Illuminate\Support\Collection;

abstract class Engine
{
    /**
     * Update the given model in the index.
     *
     * @param  Collection $models
     * @return void
     */
    abstract public function update(Collection $models);

    /**
     * Remove the given model from the index.
     *
     * @param  Collection $models
     * @return void
     */
    abstract public function delete(Collection $models);
}