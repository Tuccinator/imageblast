<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageTest extends TestCase
{
    protected static $user;

    protected static $secondUser;

    protected static $image;

    public function setUp()
    {
        parent::setUp();

        // Only setup the users and image once and then re-use them
        if(is_null(self::$user)) {
            self::$user = factory(\App\User::class)->create();
            self::$secondUser = factory(\App\User::class)->create();
            self::$image = factory(\App\Image::class)->create(['user_id' => self::$user->id]);
        }
    }

    /**
     * Make sure user is logged in to be able to upload image
     */
    public function testUserMustBeLoggedIn()
    {
        $response = $this->json('POST', '/upload', [
            'image' => '',
            'privacy' => ''
        ]);

        $response->assertStatus(401);
    }

    /**
     * Make sure the image to be uploaded actually has a name
     */
    public function testImageHasFilename()
    {
        $response = $this->actingAs(self::$user)->json('POST', '/upload', [
            'image' => '',
            'privacy' => ''
        ]);

        $response->assertStatus(422);
    }

    /**
     * Make sure the image to be uploaded has a valid extension, not .exe or something like that
     */
    public function testImageHasValidExtension()
    {
        $response = $this->actingAs(self::$user)->json('POST', '/upload', [
            'image' => UploadedFile::fake()->image('image.random'),
            'privacy' => ''
        ]);

        $response->assertStatus(422);
    }

    /**
     * Make sure the provided privacy option is valid. Either 0, 1, or 2.
     *
     * See images migration
     */
    public function testImageHasValidPrivacy()
    {
        $response = $this->actingAs(self::$user)->json('POST', '/upload', [
            'image' => UploadedFile::fake()->image('image.jpg'),
            'privacy' => 9
        ]);

        $response->assertStatus(422);
    }

    /**
     * Check if the image was successfully uploaded to the server
     */
    public function testImageWasUploadedSuccessfully()
    {
        Storage::fake('public');

        $response = $this->actingAs(self::$user)->json('POST', '/upload', [
            'image' => UploadedFile::fake()->image('image.jpg'),
            'privacy' => '1'
        ]);

        $response->assertStatus(200);

        $this->assertContains('jpg', $response->json());
    }

    /**
     * Make sure the user is logged in before they like/dislike an image
     */
    public function testUserMustBeLoggedInToLike()
    {
        $imageId = self::$image->id;
        $response = $this->json('POST', "/graphql?query=mutation+images{likeImage(id: {$imageId}, type: \"1\"){id, likes, dislikes}}");
        $data = $response->json()['data'];

        $this->assertEquals(null, $data['likeImage']);
    }

    /**
     * Check if the user can successfully like an image
     */
    public function testUserSuccessfullyLikedImage()
    {
        $imageId = self::$image->id;
        $response = $this->actingAs(self::$user)->json('POST', "/graphql?query=mutation+images{likeImage(id: {$imageId}, type: \"1\"){id, likes, dislikes}}");
        $data = $response->json()['data'];

        $this->assertEquals(1, $data['likeImage']['likes']);
    }

    /**
     * Check if user has access to a private image, which means they can't like it
     */
    public function testUserDoesNotHaveAccessToImage()
    {
        $image = factory(\App\Image::class)->create(['user_id' => self::$user->id, 'private' => 2]);

        $response = $this->actingAs(self::$secondUser)->json('POST', "/graphql?query=mutation+images{likeImage(id: {$image->id}, type: \"1\"){id, likes, dislikes}}");
        $data = $response->json()['data'];

        $this->assertEquals(null, $data['likeImage']);
    }

    /**
     * Make sure the like is valid. Either 1 or 2.
     */
    public function testMustHaveValidLikeType()
    {
        $imageId = self::$image->id;
        $response = $this->actingAs(self::$user)->json('POST', "/graphql?query=mutation+images{likeImage(id: {$imageId}, type: \"3\"){id, likes, dislikes}}");
        $data = $response->json()['data'];

        $this->assertEquals(null, $data['likeImage']);
    }

    /**
     * Make sure when you re-click the like button, it removes the previous like and not adds to the count
     */
    public function testLikingImageWillBeReversed()
    {
        $imageId = self::$image->id;
        $response = $this->actingAs(self::$user)->json('POST', "/graphql?query=mutation+images{likeImage(id: {$imageId}, type: \"1\"){id, likes, dislikes}}");

        $data = $response->json()['data'];

        $this->assertEquals(0, $data['likeImage']['likes']);
    }
}
