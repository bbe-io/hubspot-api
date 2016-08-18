<?php


use BBE\HubspotAPI\Factory as Hubspot;

class ContactTest extends PHPUnit_Framework_TestCase
{
    private function hubspot()
    {
        return Hubspot::connect('***REMOVED***');
    }

    /** @test */
    public function can_get_all_contacts()
    {
        $hubspot = $this->hubspot();

        $contacts = $hubspot->contacts()->all();

        $this->assertGreaterThan(1, count($contacts));
    }

    /** @test */
    public function can_get_a_subset_of_contacts()
    {
        $hubspot = $this->hubspot();

        $contacts = $hubspot->contacts()->take(3);

        $this->assertCount(3, $contacts);
    }

    /** @test */
    public function can_find_a_single_contact_by_their_id()
    {
        $hubspot = $this->hubspot();

        $contacts = $hubspot->contacts()->whereId('***REMOVED***');
        $this->assertCount(1, $contacts);

        $contact = $contacts->first();
        $this->assertEquals('***REMOVED***', $contact->vid);

        $contact = $hubspot->contacts()->findId('***REMOVED***');
        $this->assertEquals('***REMOVED***', $contact->vid);
    }

    /** @test */
    public function can_find_a_single_contact_by_their_email()
    {
        $hubspot = $this->hubspot();

        $hubspot->contacts()->whereEmail('***REMOVED***');
    }

    /** @test */
    public function can_find_a_single_contact_by_their_token()
    {
        $hubspot = $this->hubspot();

        $hubspot->contacts()->whereToken('***REMOVED***');
    }

    /** @test */
    public function can_find_multiple_contacts_by_their_ids()
    {
        $hubspot = $this->hubspot();

        $contacts = $hubspot->contacts()->whereId(['***REMOVED***', '***REMOVED***', '***REMOVED***']);
        $this->assertCount(3, $contacts);

        $ids = $contacts->keys();

        $this->assertTrue($ids->contains('***REMOVED***'));
        $this->assertTrue($ids->contains('***REMOVED***'));
        $this->assertTrue($ids->contains('***REMOVED***'));
    }

    /** @test */
    public function can_find_multiple_contacts_by_their_emails()
    {
        $hubspot = $this->hubspot();

        $hubspot->contacts()->whereEmail([
            '***REMOVED***',
            '***REMOVED***',
            '***REMOVED***',
        ]);
    }

    /** @test */
    public function can_find_multiple_contact_by_their_tokens()
    {
        $hubspot = $this->hubspot();

        $hubspot->contacts()->whereToken([
            '***REMOVED***',
            '0f62625e98b0efad5444e5db01051340',
        ]);
    }
}
