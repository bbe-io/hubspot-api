<?php


use BBE\HubspotAPI\Factory as Hubspot;

class FormTest extends PHPUnit_Framework_TestCase
{
    private function hubspot()
    {
        return Hubspot::connect('***REMOVED***');
    }

    /** @test */
    public function can_get_form_properties_directly()
    {
        $hubspot = $this->hubspot();
        $form = $hubspot->forms()->find('***REMOVED***');

        $this->assertEquals('***REMOVED***', $form->id);
        $this->assertEquals(***REMOVED***, $form->portalId);

        $this->assertTrue($form->properties->has('firstname'));
        $this->assertTrue($form->properties->has('email'));
        $this->assertTrue($form->properties->has('lifecyclestage'));
    }
}
