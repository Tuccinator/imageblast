<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GroupTest extends TestCase
{
    protected static $user;

    protected static $secondUser;

    public function setUp()
    {
        parent::setUp();

        if(is_null(self::$user)) {
            self::$user = factory(\App\User::class)->create();
            self::$secondUser = factory(\App\User::class)->create();
        }
    }

    public function testUserMustBeLoggedInToCreateGroup()
    {
        $response = $this->json('POST', "/graphql?query=mutation+groups{ createGroup(name: \"\", description: \"\"){id}}");
        $result = $response->json();

        $this->assertEquals(null, $result['data']['createGroup']);
    }

    public function testUserMustProvideNameToCreateGroup()
    {
        $response = $this->actingAs(self::$user)->json('POST', "/graphql?query=mutation+groups{ createGroup(name: \"\", description: \"\"){id} }");
        $result = $response->json();

        $this->assertArrayHasKey('name', $result['errors'][0]['validation']);
    }

    public function testUserMustProvidePrivacyToCreateGroup()
    {
        $response = $this->actingAs(self::$user)->json('POST', "/graphql?query=mutation+groups{ createGroup(name: \"first group\", description: \"\", privacy: 5){id}}");
        $result = $response->json();

        $this->assertArrayHasKey('privacy', $result['errors'][0]['validation']);
    }

    public function testUserHasSuccessfullyCreatedGroup()
    {
        $response = $this->actingAs(self::$user)->json('POST', "/graphql?query=mutation+groups{ createGroup(name: \"first group\", description: \"\", privacy: 1){id}}");
        $result = $response->json();

        $this->assertArrayHasKey('id', $result['data']['createGroup']);
    }

    public function testUserCannotChangeGroupPrivacy()
    {
        $group = factory(\App\Group::class)->create(['creator_id' => self::$user->id]);
        $response = $this->actingAs(self::$secondUser)->json('POST', "/graphql?query=mutation+groups{ groupPrivacy(id: {$group->id}, privacy: 0){id, public}}");

        $result = $response->json();

        $this->assertEquals(null, $result['data']['groupPrivacy']);
    }

    public function testAdminCanChangeGroupPrivacy()
    {
        $group = factory(\App\Group::class)->create(['creator_id' => self::$user->id]);
        $groupUser = factory(\App\GroupUser::class)->create(['user_id' => self::$user->id, 'group_id' => $group->id, 'role' => 2]);
        $response = $this->actingAs(self::$user)->json('POST', "/graphql?query=mutation+groups{ groupPrivacy(id: {$group->id}, privacy: 0){id, public}}");

        $result = $response->json();

        $this->assertArrayHasKey('public', $result['data']['groupPrivacy']);
        $this->assertEquals(0, $result['data']['groupPrivacy']['public']);
    }
}
