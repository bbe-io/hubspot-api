<?php


use BBE\HubspotAPI\Factory as Hubspot;

class ContactsTest extends PHPUnit_Framework_TestCase
{
    private function hubspot()
    {
        return Hubspot::connect('[api-key]');
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

        $contacts = $hubspot->contacts()->whereId('[contact-id]');
        $this->assertCount(1, $contacts);

        $contact = $contacts->first();
        $this->assertEquals('[contact-id]', $contact->id);

        $contact = $hubspot->contacts()->findWithId('[contact-id]');
        $this->assertEquals('[contact-id]', $contact->id);
    }

    /** @test */
    public function can_find_a_single_contact_by_their_email()
    {
        $hubspot = $this->hubspot();

        $contacts = $hubspot->contacts()->whereEmail('existing@email.com');
        $this->assertCount(1, $contacts);

        $contact = $contacts->first();
        $this->assertEquals('existing@email.com', $contact->email);

        $contact = $hubspot->contacts()->findWithEmail('existing@email.com');
        $this->assertEquals('existing@email.com', $contact->email);
    }

    /** @test */
    public function can_find_a_single_contact_by_their_token()
    {
        $hubspot = $this->hubspot();

        $contacts = $hubspot->contacts()->whereToken('[contact-token]');
        $this->assertCount(1, $contacts);

        $contact = $hubspot->contacts()->findWithToken('[contact-token]');
        $this->assertNotNull($contact);
    }

    /** @test */
    public function can_find_multiple_contacts_by_their_ids()
    {
        $hubspot = $this->hubspot();

        $contacts = $hubspot->contacts()->whereId(['[contact-id]', '[contact-id-2]', '[contact-id-3]']);
        $this->assertCount(3, $contacts);

        $ids = $contacts->pluck('id');

        $this->assertTrue($ids->contains('[contact-id]'));
        $this->assertTrue($ids->contains('[contact-id-2]'));
        $this->assertTrue($ids->contains('[contact-id-3]'));
    }

    /** @test */
    public function can_find_multiple_contacts_by_their_emails()
    {
        $hubspot = $this->hubspot();

        $contacts = $hubspot->contacts()->whereEmail([
            'existing@email.com',
            'existing@email2.com',
            'existing@email3.com',
        ]);

        $this->assertCount(3, $contacts);

        $emails = $contacts->pluck('email');

        $this->assertTrue($emails->contains('existing@email.com'));
        $this->assertTrue($emails->contains('existing@email2.com'));
        $this->assertTrue($emails->contains('existing@email3.com'));
    }

    /** @test */
    public function can_find_multiple_contact_by_their_tokens()
    {
        $hubspot = $this->hubspot();

        $contacts = $hubspot->contacts()->whereToken([
            '[contact-token]',
            '[contact-token-2]',
        ]);

        $this->assertCount(2, $contacts);
    }
}
