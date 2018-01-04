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

    public function testUserIsLoggedInToJoinGroup()
    {
        $group = factory(\App\Group::class)->create(['creator_id' => self::$user->id]);

        $response = $this->json('POST', "/graphql?query=mutation+groups{ joinGroup(id: {$group->id}){id, members{id}}}");

        $result = $response->json();

        $this->assertEquals(null, $result['data']['joinGroup']);
    }

    public function testUserHasToProvideValidGroupId()
    {
        $response = $this->actingAs(self::$user)->json('POST', "/graphql?query=mutation+groups{ joinGroup(id: 500000){id, members{id}}}");

        $result = $response->json();

        $this->assertArrayHasKey('id', $result['errors'][0]['validation']);
    }

    public function testUserCanSuccessfullyJoinGroup()
    {
        $userId = self::$secondUser->id;
        $group = factory(\App\Group::class)->create(['creator_id' => self::$user->id, 'public' => 1]);

        $response = $this->actingAs(self::$secondUser)->json('POST', "/graphql?query=mutation+groups{ joinGroup(id: {$group->id}){id, members{id}}}");

        $result = $response->json();

        $this->assertCount(1, $result['data']['joinGroup']['members']);
    }

    public function testUserWillLeaveGroupWhenTryingToJoin()
    {
        $userId = self::$secondUser->id;
        $group = factory(\App\Group::class)->create(['creator_id' => self::$user->id, 'public' => 1]);
        $groupUser = factory(\App\GroupUser::class)->create(['group_id' => $group->id, 'user_id' => $userId]);

        $response = $this->actingAs(self::$secondUser)->json('POST', "/graphql?query=mutation+groups{ joinGroup(id: {$group->id}){id, members{id}}}");

        $result = $response->json();

        $this->assertCount(0, $result['data']['joinGroup']['members']);
    }

    public function testUserUnableToLeaveGroupIfCreator()
    {
        $group = factory(\App\Group::class)->create(['creator_id' => self::$user->id]);
        $groupUser = factory(\App\GroupUser::class)->create(['group_id' => $group->id, 'user_id' => self::$user->id]);

        $response = $this->actingAs(self::$user)->json('POST', "/graphql?query=mutation+groups{ joinGroup(id: {$group->id}){id, members{id}}}");

        $result = $response->json();

        $this->assertEquals(null, $result['data']['joinGroup']);
    }

    public function testAdminCannotChangeInviteCodeWithMoreThan32Chars()
    {
        $group = factory(\App\Group::class)->create(['creator_id' => self::$user->id]);
        $groupUser = factory(\App\GroupUser::class)->create(['group_id' => $group->id, 'user_id' => self::$user->id, 'role' => 2]);

        $inviteCode = '12easd2e12easd2e12easd2e12easd2e1';

        $response = $this->actingAs(self::$user)->json('POST', "/graphql?query=mutation+groups{ changeGroupCode(id: {$group->id}, code: \"{$inviteCode}\"){id, invite_code}}");

        $result = $response->json();

        $this->assertArrayHasKey('code', $result['errors'][0]['validation']);
    }

    public function testAdminCannotAddDuplicateInviteCode()
    {
        $inviteCode = '12easd2e';

        $group1 = factory(\App\Group::class)->create(['creator_id' => self::$user->id, 'invite_code' => $inviteCode, 'public' => 0]);
        $group = factory(\App\Group::class)->create(['creator_id' => self::$user->id]);
        $groupUser = factory(\App\GroupUser::class)->create(['group_id' => $group->id, 'user_id' => self::$user->id, 'role' => 2]);

        $response = $this->actingAs(self::$user)->json('POST', "/graphql?query=mutation+groups{ changeGroupCode(id: {$group->id}, code: \"{$inviteCode}\"){id, invite_code}}");

        $result = $response->json();

        $this->assertEquals(null, $result['data']['changeGroupCode']);
    }

    public function testAdminCanChangeInviteCode()
    {
        $group = factory(\App\Group::class)->create(['creator_id' => self::$user->id, 'public' => 0]);
        $groupUser = factory(\App\GroupUser::class)->create(['group_id' => $group->id, 'user_id' => self::$user->id, 'role' => 2]);

        $inviteCode = str_random(16);

        $response = $this->actingAs(self::$user)->json('POST', "/graphql?query=mutation+groups{ changeGroupCode(id: {$group->id}, code: \"{$inviteCode}\"){id, invite_code}}");

        $result = $response->json();

        $this->assertArrayHasKey('invite_code', $result['data']['changeGroupCode']);
    }

    public function testUserMustProvideInviteCodeToJoinPrivateGroup()
    {
        $inviteCode = str_random(16);
        $group = factory(\App\Group::class)->create(['creator_id' => self::$user->id, 'public' => 0, 'invite_code' => $inviteCode]);
        $groupUser = factory(\App\GroupUser::class)->create(['group_id' => $group->id, 'user_id' => self::$user->id, 'role' => 2]);

        $response = $this->actingAs(self::$secondUser)->json('POST', "/graphql?query=mutation+groups{ joinGroup(id: {$group->id}){id}}");

        $result = $response->json();

        $this->assertEquals(null, $result['data']['joinGroup']);
    }

    public function testUserMustProvideValidInviteCodeToJoinPrivateGroup()
    {
        $inviteCode = str_random(16);
        $group = factory(\App\Group::class)->create(['creator_id' => self::$user->id, 'public' => 0, 'invite_code' => $inviteCode]);
        $groupUser = factory(\App\GroupUser::class)->create(['group_id' => $group->id, 'user_id' => self::$user->id, 'role' => 2]);

        $response = $this->actingAs(self::$secondUser)->json('POST', "/graphql?query=mutation+groups{ joinGroup(id: {$group->id}, code: \"invite123\"){id}}");

        $result = $response->json();

        $this->assertEquals(null, $result['data']['joinGroup']);
    }

    public function testUserCanJoinPrivateGroupWithInviteCode()
    {
        $inviteCode = str_random(16);
        $group = factory(\App\Group::class)->create(['creator_id' => self::$user->id, 'public' => 0, 'invite_code' => $inviteCode]);
        $groupUser = factory(\App\GroupUser::class)->create(['group_id' => $group->id, 'user_id' => self::$user->id, 'role' => 2]);

        $response = $this->actingAs(self::$secondUser)->json('POST', "/graphql?query=mutation+groups{ joinGroup(id: {$group->id}, code: \"{$inviteCode}\"){id}}");

        $result = $response->json();

        $this->assertEquals($group->id, $result['data']['joinGroup']['id']);
    }
}
