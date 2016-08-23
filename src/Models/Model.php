<?php

namespace BBE\HubspotAPI\Models;

use BBE\HubspotAPI\Resources\Resource;
use Illuminate\Support\Collection;

abstract class Model
{
    /**
     * The model ID.
     *
     * @var int
     */
    public $id;

    /**
     * The model resource.
     *
     * @var Resource
     */
    public $resource;

    /**
     * A collection of properties the model has.
     *
     * @var Collection
     */
    public $properties;

    /**
     * A collection of unsaved changes to properties.
     *
     * @var Collection
     */
    public $changes;

    /**
     * Model constructor.
     *
     * @param Resource $resource
     * @param $object
     */
    public function __construct(Resource $resource, $object)
    {
        $this->resource = $resource;

        $this->id = $this->getJsonID($object);
        $this->properties = $this->mapProperties($object);
        $this->changes = Collection::make();
    }

    /**
     * Get the model ID.
     *
     * @param $object
     * @return mixed
     */
    abstract public function getJsonID($object);

    /**
     * Check if the requested property is an ID.
     *
     * @param $property
     * @return bool
     */
    abstract public function wantsId($property);

    /**
     * Map property values to their key.
     *
     * @param $object
     * @return Collection
     */
    abstract public function mapProperties($object);
}
