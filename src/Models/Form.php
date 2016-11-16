<?php
namespace BBE\HubspotAPI\Models;

use BBE\HubspotAPI\FormSubmission;
use BBE\HubspotAPI\Resources\Contracts\CanRetrieveData;
use Illuminate\Support\Collection;

class Form extends Model
{
    /**
     * HubSpot portal ID.
     *
     * @var
     */
    public $portal_id;

    /**
     * Form constructor.
     *
     * @param CanRetrieveData $resource
     * @param $object
     */
    public function __construct(CanRetrieveData $resource, $object)
    {
        parent::__construct($resource, $object);

        $this->portal_id = $object->portalId;
    }

    /**
     * Create a Form Submission with this form.
     *
     * @return FormSubmission
     */
    public function submission()
    {
        return FormSubmission::createForForm($this);
    }

    /**
     * Create a Form Submission with this form and submit it.
     *
     * @param array $data
     * @param String $page_name
     * @param String $page_url
     * @return FormSubmission
     */
    public function submit(array $data = [], $page_name = null, $page_url = null)
    {
        $submission = FormSubmission::createForForm($this);

        if (count($data) > 0) {
            $submission->data($data);
        }

        if (! is_null($page_name)) {
            $submission->pageName($page_name);
        }

        if (! is_null($page_url)) {
            $submission->pageUrl($page_url);
        }

        return $submission->submit();
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
