<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class AvatarTest extends TestCase
{
    private $user;

    /**
     * Create a user to use with consecutive requests
     */
    public function setUp()
    {
        parent::setUp();

        $this->user = factory(\App\User::class)->make();
    }

    /**
     * Make sure user is logged in to gain authority to upload avatar
     */
    public function testUserMustBeLoggedIn()
    {
        $response = $this->json('POST', '/avatar', [
            'avatar' => ''
        ]);

        $response->assertStatus(401);
    }

    /**
     * Make sure you provide a filename for avatar
     */
    public function testMustProvideFilename()
    {
        $response = $this->actingAs($this->user)->json('POST', '/avatar', [
            'avatar' => UploadedFile::fake()->image('')
        ]);

        $response->assertStatus(422);
    }

    /**
     * Make sure the file extension is allowed
     */
    public function testMustProvideValidExtension()
    {
        $response = $this->actingAs($this->user)->json('POST', '/avatar', [
            'avatar' => UploadedFile::fake()->image('avatar.random')
        ]);

        $response->assertStatus(422);
    }

    /**
     * Check if avatar was successfully uploaded
     */
    public function testAvatarSuccessfullyUploaded()
    {
        Storage::fake('public');
        
        $avatarCount = count(Storage::disk('public')->files('avatars'));

        $response = $this->actingAs($this->user)->json('POST', '/avatar', [
            'avatar' => UploadedFile::fake()->image('avatar.jpg')
        ]);

        $this->assertEquals($avatarCount + 1, count(Storage::disk('public')->files('avatars')));
    }
}
