<?php

namespace Modules\Contact\Tests;

use Illuminate\Support\Facades\Event;
use Modules\Contact\Events\ContactRequestWasCreated;

class EloquentContactRequestRepositoryTest extends BaseContactRequestTest
{
    /** @test */
    public function it_creates_a_contact_request()
    {
        $this->contactRequest->create([
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'company' => 'John Doe LLC',
            'phone' => '123 456',
            'message' => 'Hello there!',
        ]);

        $contactRequest = $this->contactRequest->find(1);
        $this->assertCount(1, $this->contactRequest->all());
        $this->assertEquals('John Doe', $contactRequest->name);
        $this->assertEquals('john@doe.com', $contactRequest->email);
        $this->assertEquals('John Doe LLC', $contactRequest->company);
        $this->assertEquals('123 456', $contactRequest->phone);
        $this->assertEquals('Hello there!', $contactRequest->message);
    }

    /** @test */
    public function it_triggers_event_when_contact_request_was_created()
    {
        Event::fake();

        $contactRequest = $this->createContactRequest();

        Event::assertDispatched(ContactRequestWasCreated::class, function ($e) use ($contactRequest) {
            return $e->contactRequest->id === $contactRequest->id;
        });
    }

    private function createContactRequest()
    {
        $faker = \Faker\Factory::create();

        return $this->contactRequest->create([
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            'company' => $faker->company,
            'phone' => $faker->phoneNumber,
            'message' => $faker->paragraph(10),
        ]);
    }
}