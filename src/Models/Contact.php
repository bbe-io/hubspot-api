<?php

namespace BBE\HubspotAPI\Models;

use Illuminate\Support\Collection;

class Contact extends Model
{
    /**
     * Get the model ID.
     *
     * @param $object
     * @return mixed
     */
    public function getJsonID($object)
    {
        return $object->vid ?: null;
    }

    /**
     * Check if the requested property is an ID.
     *
     * @param $property
     * @return bool
     */
    public function wantsId($property)
    {
        return $property === 'id' || $property === 'vid';
    }

    /**
     * Map property values to their key.
     *
     * @param $object
     * @return Collection
     */
    public function mapProperties($object)
    {
        $properties = Collection::make($object->properties);

        return $properties->map(function ($property) {
            return $property->value;
        });
    }

    /**
     * Save the contact to HubSpot.
     *
     * @return $this
     * @throws \Exception
     */
    public function save()
    {
        if ($this->id) {
            $this->saveWithId();
        } elseif ($this->email) {
            $this->saveWithEmail();
        } else {
            throw new \Exception('Contact does not have an email or ID for updating');
        }

        $this->properties = $this->properties->merge($this->changes);
        $this->changes = Collection::make();

        return $this;
    }

    /**
     * Get a fresh copy of the Contact from HubSpot.
     *
     * @return $this
     */
    public function fresh()
    {
        $this->discard();

        if ($this->id) {
            $this->properties = $this->resource->findWithId($this->id)->properties;
        } elseif ($this->email) {
            $this->properties = $this->resource->findWithEmail($this->email)->properties;
        }

        return $this;
    }

    /**
     * Update the contact using its ID.
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface|void
     */
    private function saveWithId()
    {
        $endpoint = '/contact/vid/'.$this->id.'/profile';
        $options = ['json' => ['properties' => $this->changesToArray()]];

        return $this->resource->post($endpoint, $options);
    }

    /**
     * Create or update the contact with its email.
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface|void
     */
    private function saveWithEmail()
    {
        $endpoint = '/contact/createOrUpdate/email/'.$this->email;
        $options = ['json' => ['properties' => $this->changesToArray()]];

        return $this->resource->post($endpoint, $options);
    }

    /**
     * Map the changes into a property/value format array for HubSpot.
     *
     * @return array
     */
    private function changesToArray()
    {
        return $this->changes->map(function ($item, $key) {
            return [
                'property' => $key,
                'value' => $item,
            ];
        })->values()->toArray();
    }
}
