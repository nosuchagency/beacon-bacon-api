<?php

namespace Tests\Feature\Contents;

use App\Models\Content\Content;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContentsDeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_cannot_delete_contents()
    {
        $this->postJson(route('contents.bulk-destroy'))->assertStatus(401);
        $this->deleteJson(route('contents.destroy', ['content' => factory(Content::class)->create()]))->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_without_delete_permission_cannot_delete_contents()
    {
        $this->signIn();
        $content = factory(Content::class)->create();
        $this->deleteJson(route('contents.destroy', ['content' => $content]))->assertStatus(403);

        $this->postJson(route('contents.bulk-destroy', ['items' => []]))->assertStatus(403);
    }

    /** @test */
    public function an_authenticated_user_with_delete_permission_may_delete_specific_content()
    {
        $this->signIn()->assignRole(
            $this->createRoleWithPermissions(['contents.delete'])
        );

        $content = factory(Content::class)->create();
        $this->deleteJson(route('contents.destroy', ['content' => $content]))->assertStatus(200);
        $this->assertSoftDeleted('contents', ['id' => $content->id]);
    }

    /** @test */
    public function an_authenticated_user_with_delete_permission_may_delete_contents_in_bulk()
    {
        $this->signIn()->assignRole(
            $this->createRoleWithPermissions(['contents.delete'])
        );

        $contents = factory(Content::class, 5)->create();
        $this->assertCount(5, Content::all());
        $this->postJson(route('contents.bulk-destroy'), ['items' => $contents])->assertStatus(200);
        $this->assertCount(0, Content::all());
    }
}