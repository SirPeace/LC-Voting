<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use GuzzleHttp\Client as GuzzleClient;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->client = new GuzzleClient(['verify' => false]);
});


test("user_can_generate_gravatar_default_image_when_no_email_found_first_character_a", function () {
    $user = User::factory()->create([
        'name' => 'Andre',
        'email' => 'afakeemail@fakeemail.com',
    ]);

    $gravatarUrl = $user->getAvatar();

    $this->assertEquals(
        'https://www.gravatar.com/avatar/' . md5($user->email) . '?s=200&d=https://s3.amazonaws.com/laracasts/images/forum/avatars/default-avatar-1.png',
        $gravatarUrl
    );

    $response = $this->client->get($user->getAvatar());

    $this->assertTrue($response->getStatusCode() === 200);
});


test("user_can_generate_gravatar_default_image_when_no_email_found_first_character_z", function () {
    $user = User::factory()->create([
        'name' => 'Andre',
        'email' => 'zfakeemail@fakeemail.com',
    ]);

    $gravatarUrl = $user->getAvatar();

    $this->assertEquals(
        'https://www.gravatar.com/avatar/' . md5($user->email) . '?s=200&d=https://s3.amazonaws.com/laracasts/images/forum/avatars/default-avatar-26.png',
        $gravatarUrl
    );

    $response = $this->client->get($user->getAvatar());

    $this->assertTrue($response->getStatusCode() === 200);
});


test("user_can_generate_gravatar_default_image_when_no_email_found_first_character_0", function () {
    $user = User::factory()->create([
        'name' => 'Andre',
        'email' => '0fakeemail@fakeemail.com',
    ]);

    $gravatarUrl = $user->getAvatar();

    $this->assertEquals(
        'https://www.gravatar.com/avatar/' . md5($user->email) . '?s=200&d=https://s3.amazonaws.com/laracasts/images/forum/avatars/default-avatar-27.png',
        $gravatarUrl
    );

    $response = $this->client->get($user->getAvatar());

    $this->assertTrue($response->getStatusCode() === 200);
});


test("user_can_generate_gravatar_default_image_when_no_email_found_first_character_9", function () {
    $user = User::factory()->create([
        'name' => 'Andre',
        'email' => '9fakeemail@fakeemail.com',
    ]);

    $gravatarUrl = $user->getAvatar();

    $this->assertEquals(
        'https://www.gravatar.com/avatar/' . md5($user->email) . '?s=200&d=https://s3.amazonaws.com/laracasts/images/forum/avatars/default-avatar-36.png',
        $gravatarUrl
    );

    $response = $this->client->get($user->getAvatar());

    $this->assertTrue($response->getStatusCode() === 200);
});
