<?php

namespace BBE\HubspotAPI\Models;

use BBE\HubspotAPI\Resources\Contracts\CanRetrieveData;
use Illuminate\Support\Collection;

class Form extends Model
{
    /**
     * HubSpot portal ID.
     *
     * @var $portalId
     */
    public $portalId;

    /**
     * Form constructor.
     *
     * @param CanRetrieveData $resource
     * @param $object
     */
    public function __construct(CanRetrieveData $resource, $object)
    {
        parent::__construct($resource, $object);

        $this->portalId = $object->portalId;
    }

    /**
     * Get the model ID.
     *
     * @param $object
     * @return mixed
     */
    public function getJsonID($object)
    {
        return $object->guid ?: null;
    }

    /**
     * Check if the requested property is an ID.
     *
     * @param $property
     * @return bool
     */
    public function wantsId($property)
    {
        return $property === 'id' || $property === 'guid';
    }

    /**
     * Map property values to their key.
     *
     * @param $object
     * @return Collection
     */
    public function mapProperties($object)
    {
        if (isset($object->fields)) {
            return Collection::make($object->fields);
        }

        if (isset($object->formFieldGroups)) {
            return Collection::make($object->formFieldGroups)
                ->pluck('fields')
                ->flatten(1)
                ->keyBy('name');
        }

        return Collection::make();
    }
}
