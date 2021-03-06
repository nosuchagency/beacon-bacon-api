<?php

namespace Tests\Feature\Components;

use App\Models\Component;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ComponentsReadTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_guest_cannot_read_components()
    {
        $component = factory(Component::class)->create();

        $this->getJson(route('components.index'))->assertStatus(401);
        $this->getJson(route('components.show', ['component' => $component]))->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_without_read_permission_cannot_view_components()
    {
        $this->signIn();

        $this->getJson(route('components.index'))->assertStatus(403);
    }

    /** @test */
    public function an_authenticated_user_with_read_permission_can_view_components()
    {
        $role = $this->createRoleWithPermissions(['component:read']);

        $this->signIn(null, $role);

        $this->getJson(route('components.index'))->assertOk();
    }

    /** @test */
    public function an_authenticated_user_with_read_permission_can_view_components_paginated()
    {
        $role = $this->createRoleWithPermissions(['component:read']);

        $this->signIn(null, $role);

        $this->getJson(route('components.paginated'))->assertOk();
    }

    /** @test */
    public function an_authenticated_user_with_read_permission_can_view_a_specific_component()
    {
        $role = $this->createRoleWithPermissions(['component:read']);

        $this->signIn(null, $role);

        $component = factory(Component::class)->create();

        $this->getJson(route('components.show', ['component' => $component]))->assertOk();
    }
}
