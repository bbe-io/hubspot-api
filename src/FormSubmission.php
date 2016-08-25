<?php

namespace BBE\HubspotAPI;

use BBE\HubspotAPI\Models\Form;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use GuzzleHttp\Client as GuzzleClient;

class FormSubmission
{
    /**
     * Form data.
     *
     * @var
     */
    private $data;

    /**
     * HubSpot contextual information.
     *
     * @var
     */
    private $context;

    /**
     * Portal ID to submit to.
     *
     * @var
     */
    private $portal_id;

    /**
     * Form ID to submit to.
     *
     * @var
     */
    private $form_id;

    /**
     * FormSubmission constructor.
     */
    public function __construct()
    {
        $this->data = Collection::make();
        $this->context = Collection::make();

        $this->setContext();
    }

    /**
     * Static constructor.
     *
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Create a FormSubmission for a specific form.
     *
     * @param Form $form
     * @return mixed
     */
    public static function createForForm(Form $form)
    {
        return (new static())->form($form);
    }

    /**
     * Create a FormSubmission with endpoint details.
     *
     * @param String $portal_id
     * @param String $form_id
     * @return static
     */
    public static function createForEndpoint(String $portal_id, String $form_id)
    {
        $submission = new static();
        $submission->portalId($portal_id);
        $submission->formId($form_id);

        return $submission;
    }

    /**
     * Set the HubSpot context with whatever data we can find.
     */
    private function setContext()
    {
        if (isset($_COOKIE['hubspotutk'])) {
            $this->context->put('hutk', $_COOKIE['hubspotutk']);
        }

        if (isset($_SERVER['REMOTE_ADDR'])) {
            $this->context->put('ipAddress', $_SERVER['REMOTE_ADDR']);
        }
    }

    /**
     * Set the portal ID.
     *
     * @param String $portal_id
     * @return $this
     */
    public function portalId(String $portal_id)
    {
        $this->portal_id = $portal_id;

        return $this;
    }

    /**
     * Set the form ID.
     *
     * @param String $form_id
     * @return $this
     */
    public function formId(String $form_id)
    {
        $this->form_id = $form_id;

        return $this;
    }

    /**
     * Set the HubSpot token context.
     *
     * @param String $token
     * @return $this
     */
    public function token(String $token)
    {
        $this->context->put('hutk', $token);

        return $this;
    }

    /**
     * Set the IP address context.
     *
     * @param String $ip
     * @return $this
     */
    public function ip(String $ip)
    {
        $this->context->put('ipAddress', $ip);

        return $this;
    }

    /**
     * Set the page name and url context.
     *
     * @param String $name
     * @param String $url
     * @return $this
     */
    public function page(String $name, String $url)
    {
        $this->context->put('pageName', $name);
        $this->context->put('pageUrl', $url);

        return $this;
    }

    /**
     * Set the page url context.
     *
     * @param String $url
     * @return $this
     */
    public function pageUrl(String $url)
    {
        $this->context->put('pageUrl', $url);

        return $this;
    }

    /**
     * Set the page name context.
     *
     * @param String $name
     * @return $this
     */
    public function pageName(String $name)
    {
        $this->context->put('pageName', $name);

        return $this;
    }

    /**
     * Set the form field data.
     *
     * @param array $data
     * @return $this
     */
    public function data(array $data)
    {
        $this->data = $this->data->merge($data);

        return $this;
    }

    /**
     * Set the portal id and form id from a Form model.
     *
     * @param Form $form
     * @return $this
     */
    public function form(Form $form)
    {
        $this->form_id = $form->id;
        $this->portal_id = $form->portal_id;

        return $this;
    }

    /**
     * Get the form data for the post request.
     *
     * @return array
     */
    public function formData()
    {
        $form_data = $this->data;
        $form_data->put('hs_context', $this->context->toJson());

        return $form_data->toArray();
    }

    /**
     * Get the form submission URL.
     *
     * @return string
     * @throws \Exception
     */
    private function postUrl()
    {
        if (! $this->canSubmit()) {
            throw new \Exception('Form data not provided');
        }

        return "https://forms.hubspot.com/uploads/form/v2/{$this->portal_id}/{$this->form_id}";
    }

    /**
     * Check if form can be submitted.
     *
     * @return bool
     * @throws \Exception
     */
    private function canSubmit()
    {
        if (! isset($this->portal_id)) {
            throw new \Exception('Portal ID not provided or found on the form');
        }

        if (! isset($this->form_id)) {
            throw new \Exception('Form ID not provided or found on the form');
        }

        if ($this->data->count() == 0) {
            throw new \Exception('No form data provided');
        }

        return true;
    }

    /**
     * Set the form details and submit.
     *
     * @param Form $form
     * @return bool
     */
    public function submitToForm(Form $form)
    {
        $this->form($form);

        return $this->submit();
    }

    /**
     * Submit the form and optionally set data/context.
     *
     * @param array $data
     * @param String $page_name
     * @param String $page_url
     * @return bool
     */
    public function submit(array $data = [], $page_name = null, $page_url = null)
    {
        if (count($data) > 0) {
            $this->data($data);
        }

        if (! is_null($page_name)) {
            $this->pageName($page_name);
        }

        if (! is_null($page_url)) {
            $this->pageUrl($page_url);
        }

        $client = new GuzzleClient([
            'connect_timeout' => 3,
            'timeout' => 5,
        ]);

        $response = $client->request('POST', $this->postUrl(), [
            'form_params' => $this->formData(),
        ]);

        return $this->response($response);
    }

    /**
     * Parse the Guzzle response.
     *
     * @param Response $response
     * @return bool
     * @throws \Exception
     */
    private function response(Response $response)
    {
        switch ($response->getStatusCode()) {
            case 204:
            case 302:
                // 302 = Redirect
                return true;

            case 404:
                throw new \Exception('Form GUID or Portal ID not found');

            default:
                throw new \Exception('Internal server error');
        }
    }
}
