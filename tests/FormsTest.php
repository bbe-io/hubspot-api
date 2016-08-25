<?php

use BBE\HubspotAPI\Factory as Hubspot;

class FormsTest extends PHPUnit_Framework_TestCase
{
    private function hubspot()
    {
        return Hubspot::connect('***REMOVED***');
    }

    /** @test */
    public function can_get_all_forms()
    {
        $hubspot = $this->hubspot();

        $forms = $hubspot->forms()->all();

        $this->assertGreaterThan(1, count($forms));
    }

    /** @test */
    public function can_get_a_subset_of_forms()
    {
        $hubspot = $this->hubspot();

        $forms = $hubspot->forms()->take(3);

        $this->assertCount(3, $forms);
    }

    /** @test */
    public function can_find_a_single_form_by_its_id()
    {
        $hubspot = $this->hubspot();

        $forms = $hubspot->forms()->whereId('***REMOVED***');
        $this->assertCount(1, $forms);

        $form = $forms->first();
        $this->assertEquals('***REMOVED***', $form->id);

        $form = $hubspot->forms()->findWithId('***REMOVED***');
        $this->assertEquals('***REMOVED***', $form->id);
    }

    /** @test */
    public function can_find_multiple_forms_by_their_ids()
    {
        $hubspot = $this->hubspot();

        $forms = $hubspot->forms()->whereId([
            '***REMOVED***',
            '***REMOVED***',
        ]);
        $this->assertCount(2, $forms);

        $ids = $forms->pluck('id');

        $this->assertTrue($ids->contains('***REMOVED***'));
        $this->assertTrue($ids->contains('***REMOVED***'));
    }
}
