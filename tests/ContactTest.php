<?php


use BBE\HubspotAPI\Factory as Hubspot;

class ContactTest extends PHPUnit_Framework_TestCase
{
    private function hubspot()
    {
        return Hubspot::connect('***REMOVED***');
    }

    /** @test */
    public function can_get_contact_properties_directly()
    {
        $hubspot = $this->hubspot();
        $contact = $hubspot->contacts()->findEmail('***REMOVED***');

        $this->assertEquals('***REMOVED***', $contact->email);
        $this->assertEquals(***REMOVED***, $contact->id);
        $this->assertEquals(1454541276071, $contact->createdate);
        $this->assertEquals(1444348690000, $contact->hs_email_first_send_date);
    }

    /** @test */
    public function can_set_contact_properties_directly_and_changes_are_tracked()
    {
        $hubspot = $this->hubspot();
        $contact = $hubspot->contacts()->findEmail('***REMOVED***');

        $contact->email = 'new@email.com';
        $contact->phone = '03 9500 0000';

        $this->assertTrue($contact->changes->has('email'));
        $this->assertTrue($contact->changes->has('phone'));

        $this->assertEquals('new@email.com', $contact->email);
        $this->assertEquals('03 9500 0000', $contact->phone);
    }

    /** @test */
    public function setting_a_contact_property_to_an_old_value_forgets_the_change()
    {
        $hubspot = $this->hubspot();
        $contact = $hubspot->contacts()->findEmail('***REMOVED***');

        $old_phone = $contact->phone;
        $contact->phone = '03 9500 0000';

        $this->assertTrue($contact->changes->has('phone'));
        $this->assertEquals('03 9500 0000', $contact->phone);

        $contact->phone = $old_phone;

        $this->assertCount(0, $contact->changes);
        $this->assertEquals($old_phone, $contact->phone);
    }

    /** @test */
    public function changes_are_cleared_when_a_contact_is_saved()
    {
        $hubspot = $this->hubspot();
        $contact = $hubspot->contacts()->findEmail('***REMOVED***');

        $old_phone = $contact->phone;
        $contact->phone = '03 9500 0000';
        $contact->save();

        $this->assertEquals('03 9500 0000', $contact->phone);
        $this->assertCount(0, $contact->changes);

        $contact->phone = $old_phone;
        $contact->save();

        $this->assertEquals($old_phone, $contact->phone);
        $this->assertCount(0, $contact->changes);
    }

    /** @test */
    public function changes_are_successfully_synced_with_hubspot()
    {
        $hubspot = $this->hubspot();
        $contact = $hubspot->contacts()->findEmail('***REMOVED***');

        $old_phone = $contact->phone;
        $contact->phone = '03 9500 1111';
        $contact->save();

        // Re-fetch contact
        $contact = $hubspot->contacts()->findEmail('***REMOVED***');
        $this->assertEquals('03 9500 1111', $contact->phone);

        $contact->phone = $old_phone;
        $contact->save();

        $contact = $hubspot->contacts()->findEmail('***REMOVED***');
        $this->assertEquals($old_phone, $contact->phone);
    }
}
