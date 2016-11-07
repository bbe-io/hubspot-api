<?php


use BBE\HubspotAPI\Factory as Hubspot;
use BBE\HubspotAPI\FormSubmission;

class FormSubmissionTest extends PHPUnit_Framework_TestCase
{
    private function hubspot()
    {
        return Hubspot::connect('[api-key]');
    }

    /** @test */
    public function can_submit_a_form_submission_to_a_form()
    {
        $hubspot = $this->hubspot();
        $form = $hubspot->forms()->find('[form-id]');

        $submission = FormSubmission::create()
            ->page('Unit Test', '//localhost')
            ->data([
                'firstname' => 'James',
                'lastname' => 'Test',
                'email' => 'existing@email.com',
            ])
            ->submitToForm($form);

        $this->assertTrue($submission);
    }

    /** @test */
    public function can_create_a_form_submission_with_a_form()
    {
        $hubspot = $this->hubspot();
        $form = $hubspot->forms()->find('[form-id]');

        $submission = FormSubmission::createForForm($form)
            ->page('Unit Test', '//localhost')
            ->data([
                'firstname' => 'James',
                'lastname' => 'Test',
                'email' => 'existing@email.com',
            ])
            ->submit();

        $this->assertTrue($submission);
    }

    /** @test */
    public function can_create_a_form_submission_manually()
    {
        $hubspot = $this->hubspot();
        $form = $hubspot->forms()->find('[form-id]');

        $submission = FormSubmission::create()
            ->pageName('Unit Test')
            ->pageUrl('//localhost')
            ->data([
                'firstname' => 'James',
                'lastname' => 'Test',
                'email' => 'existing@email.com',
            ])
            ->form($form)
            ->submit();

        $this->assertTrue($submission);
    }

    /** @test */
    public function can_create_a_form_submission_from_a_form()
    {
        $hubspot = $this->hubspot();
        $form = $hubspot->forms()->find('[form-id]');

        $submission = $form->submission()
            ->submit([
                'firstname' => 'James',
                'lastname' => 'Test',
                'email' => 'existing@email.com',
            ], 'Unit Test', '//localhost');

        $this->assertTrue($submission);
    }

    /** @test */
    public function can_submit_a_form_directly()
    {
        $hubspot = $this->hubspot();
        $form = $hubspot->forms()->find('[form-id]');

        $submission = $form->submit([
            'firstname' => 'James',
            'lastname' => 'Test',
            'email' => 'existing@email.com',
        ], 'Unit Test', '//localhost');

        $this->assertTrue($submission);
    }

    /** @test */
    public function submit_a_form_without_a_form_object()
    {
        $submission = FormSubmission::createForEndpoint('[portal-id]', '[form-id]')
            ->page('Unit Test', '//localhost')
            ->submit([
                'firstname' => 'James',
                'lastname' => 'Test',
                'email' => 'existing@email.com',
            ]);

        $this->assertTrue($submission);

        $submission = FormSubmission::create()
            ->portalId('[portal-id]')
            ->formId('[form-id]')
            ->submit([
                'firstname' => 'James',
                'lastname' => 'Test',
                'email' => 'existing@email.com',
            ], 'Unit Test', '//localhost');

        $this->assertTrue($submission);
    }
}
