<?php


use BBE\HubspotAPI\Factory as Hubspot;
use BBE\HubspotAPI\FormSubmission;

class FormSubmissionTest extends PHPUnit_Framework_TestCase
{
    private function hubspot()
    {
        return Hubspot::connect('***REMOVED***');
    }

    /** @test */
    public function can_submit_a_form_submission_to_a_form()
    {
        $hubspot = $this->hubspot();
        $form = $hubspot->forms()->find('***REMOVED***');

        $submission = FormSubmission::create()
            ->page('Unit Test', '//localhost')
            ->data([
                'firstname' => 'James',
                'lastname' => 'Test',
                'email' => '***REMOVED***',
            ])
            ->submitToForm($form);

        $this->assertTrue($submission);
    }

    /** @test */
    public function can_create_a_form_submission_with_a_form()
    {
        $hubspot = $this->hubspot();
        $form = $hubspot->forms()->find('***REMOVED***');

        $submission = FormSubmission::createForForm($form)
            ->page('Unit Test', '//localhost')
            ->data([
                'firstname' => 'James',
                'lastname' => 'Test',
                'email' => '***REMOVED***',
            ])
            ->submit();

        $this->assertTrue($submission);
    }

    /** @test */
    public function can_create_a_form_submission_manually()
    {
        $hubspot = $this->hubspot();
        $form = $hubspot->forms()->find('***REMOVED***');

        $submission = FormSubmission::create()
            ->pageName('Unit Test')
            ->pageUrl('//localhost')
            ->data([
                'firstname' => 'James',
                'lastname' => 'Test',
                'email' => '***REMOVED***',
            ])
            ->form($form)
            ->submit();

        $this->assertTrue($submission);
    }

    /** @test */
    public function can_create_a_form_submission_from_a_form()
    {
        $hubspot = $this->hubspot();
        $form = $hubspot->forms()->find('***REMOVED***');

        $submission = $form->submission()
            ->submit([
                'firstname' => 'James',
                'lastname' => 'Test',
                'email' => '***REMOVED***',
            ], 'Unit Test', '//localhost');

        $this->assertTrue($submission);
    }

    /** @test */
    public function can_submit_a_form_directly()
    {
        $hubspot = $this->hubspot();
        $form = $hubspot->forms()->find('***REMOVED***');

        $submission = $form->submit([
            'firstname' => 'James',
            'lastname' => 'Test',
            'email' => '***REMOVED***',
        ], 'Unit Test', '//localhost');

        $this->assertTrue($submission);
    }

    /** @test */
    public function submit_a_form_without_a_form_object()
    {
        $submission = FormSubmission::createForEndpoint('***REMOVED***', '***REMOVED***')
            ->page('Unit Test', '//localhost')
            ->submit([
                'firstname' => 'James',
                'lastname' => 'Test',
                'email' => '***REMOVED***',
            ]);

        $this->assertTrue($submission);

        $submission = FormSubmission::create()
            ->portalId('***REMOVED***')
            ->formId('***REMOVED***')
            ->submit([
                'firstname' => 'James',
                'lastname' => 'Test',
                'email' => '***REMOVED***',
            ], 'Unit Test', '//localhost');

        $this->assertTrue($submission);
    }
}
