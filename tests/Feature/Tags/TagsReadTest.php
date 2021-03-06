<?php

namespace Tests\Feature\Tags;

use App\Models\Tag;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagsReadTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_guest_cannot_read_tags()
    {
        $tag = factory(Tag::class)->create();

        $this->getJson(route('tags.index'))->assertStatus(401);
        $this->getJson(route('tags.show', ['tag' => $tag]))->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_without_read_permission_cannot_view_tags()
    {
        $this->signIn();

        $this->getJson(route('tags.index'))->assertStatus(403);
    }

    /** @test */
    public function an_authenticated_user_with_read_permission_can_view_tags()
    {
        $role = $this->createRoleWithPermissions(['tag:read']);

        $this->signIn(null, $role);

        $this->getJson(route('tags.index'))->assertOk();
    }

    /** @test */
    public function an_authenticated_user_with_read_permission_can_view_tags_paginated()
    {
        $role = $this->createRoleWithPermissions(['tag:read']);

        $this->signIn(null, $role);

        $this->getJson(route('tags.paginated'))->assertOk();
    }

    /** @test */
    public function an_authenticated_user_with_read_permission_can_view_a_specific_tag()
    {
        $role = $this->createRoleWithPermissions(['tag:read']);

        $this->signIn(null, $role);

        $tag = factory(Tag::class)->create();

        $this->getJson(route('tags.show', ['tag' => $tag]))->assertOk();
    }
}
