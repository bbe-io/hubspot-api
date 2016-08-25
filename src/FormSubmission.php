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

    private $form;

    /**
     * FormSubmission constructor.
     */
    public function __construct()
    {
        $this->data = Collection::make();
        $this->context = Collection::make();

        $this->setContext();
    }

    public static function create()
    {
        return new static();
    }

    public static function createForForm(Form $form)
    {
        return (new static())->form($form);
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

    public function data(array $data)
    {
        $this->data = $this->data->merge($data);

        return $this;
    }

    public function form(Form $form)
    {
        $this->form = $form;

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

    private function postUrl()
    {
        if (is_null($this->form) || !$this->canSubmit()) {
            throw new \Exception('Form data not provided');
        }

        return "https://forms.hubspot.com/uploads/form/v2/{$this->form->portal_id}/{$this->form->id}";
    }


    private function canSubmit()
    {
        if (!isset($this->form->portal_id)) {
            throw new \Exception('Portal ID not provided or found on the form');
        }

        if (!isset($this->form->id)) {
            throw new \Exception('Form ID not provided or found on the form');
        }

        return true;
    }

    public function submitToForm(Form $form)
    {
        $this->form($form);

        return $this->submit();
    }

    public function submit()
    {
        $client = new GuzzleClient([
            'connect_timeout' => 3,
            'timeout' => 5,
        ]);

        $response = $client->request('POST', $this->postUrl(), [
            'form_params' => $this->formData()
        ]);

        return $this->response($response);
    }

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