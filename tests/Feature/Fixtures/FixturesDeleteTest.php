<?php

namespace Tests\Feature\Fixtures;

use App\Models\Fixture;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FixturesDeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_cannot_delete_fixtures()
    {
        $this->postJson(route('fixtures.bulk-destroy'))->assertStatus(401);
        $this->deleteJson(route('fixtures.destroy', ['fixture' => factory(Fixture::class)->create()]))->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_without_delete_permission_cannot_delete_fixtures()
    {
        $this->signIn();
        $fixture = factory(Fixture::class)->create();
        $this->deleteJson(route('fixtures.destroy', ['fixture' => $fixture]))->assertStatus(403);

        $this->postJson(route('fixtures.bulk-destroy'), ['items' => []])->assertStatus(403);
    }

    /** @test */
    public function an_authenticated_user_with_delete_permission_can_delete_specific_fixture()
    {
        $role = $this->createRoleWithPermissions(['fixture:delete']);

        $this->signIn(null, $role);

        $fixture = factory(Fixture::class)->create();
        $this->deleteJson(route('fixtures.destroy', ['fixture' => $fixture]))->assertOk();
        $this->assertSoftDeleted('fixtures', ['id' => $fixture->id]);
    }

    /** @test */
    public function an_authenticated_user_with_delete_permission_can_delete_fixtures_in_bulk()
    {
        $role = $this->createRoleWithPermissions(['fixture:delete']);

        $this->signIn(null, $role);

        $fixtures = factory(Fixture::class, 5)->create();
        $this->assertCount(5, Fixture::all());
        $this->postJson(route('fixtures.bulk-destroy'), ['items' => $fixtures])->assertOk();
        $this->assertCount(0, Fixture::all());
    }
}
