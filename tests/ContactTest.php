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

        $hubspot->contacts()->all();
    }

    /** @test */
    public function can_get_a_subset_of_contacts()
    {
        $hubspot = $this->hubspot();

        $hubspot->contacts()->take(5);
    }

    /** @test */
    public function can_find_a_single_contact_by_their_id()
    {
        $hubspot = $this->hubspot();

        $hubspot->contacts()->whereId('***REMOVED***');
        $hubspot->contacts()->whereId('***REMOVED***')->first();
    }

    /** @test */
    public function can_find_a_single_contact_by_their_email()
    {
        $hubspot = $this->hubspot();

        $hubspot->contacts()->whereEmail('***REMOVED***');
        $hubspot->contacts()->whereEmail('***REMOVED***')->first();
    }

    /** @test */
    public function can_find_a_single_contact_by_their_token()
    {
        $hubspot = $this->hubspot();

        $hubspot->contacts()->whereToken('***REMOVED***');
        $hubspot->contacts()->whereToken('***REMOVED***')->first();
    }

    /** @test */
    public function can_find_multiple_contacts_by_their_ids()
    {
        $hubspot = $this->hubspot();

        $hubspot->contacts()->whereId(['***REMOVED***', '***REMOVED***', '***REMOVED***']);
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
