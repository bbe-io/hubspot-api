<?php


use BBE\HubspotAPI\Factory as Hubspot;

class FormTest extends PHPUnit_Framework_TestCase
{
    private function hubspot()
    {
        return Hubspot::connect('[api-key]');
    }

    /** @test */
    public function can_get_form_properties_directly()
    {
        $hubspot = $this->hubspot();
        $form = $hubspot->forms()->find('[form-id]');

        $this->assertEquals('[form-id]', $form->id);
        $this->assertEquals('[portal-id]', $form->portal_id);

        $this->assertTrue($form->properties->has('firstname'));
        $this->assertTrue($form->properties->has('email'));
        $this->assertTrue($form->properties->has('lifecyclestage'));
    }
}
