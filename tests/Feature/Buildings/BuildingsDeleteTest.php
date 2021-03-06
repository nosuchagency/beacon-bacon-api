<?php

namespace Tests\Feature\Buildings;

use App\Models\Building;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BuildingsDeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_cannot_delete_buildings()
    {
        $this->postJson(route('buildings.bulk-destroy'))->assertStatus(401);
        $this->deleteJson(route('buildings.destroy', ['building' => factory(Building::class)->create()]))->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_without_delete_permission_cannot_delete_buildings()
    {
        $this->signIn();
        $building = factory(Building::class)->create();
        $this->deleteJson(route('buildings.destroy', ['building' => $building]))->assertStatus(403);

        $this->postJson(route('buildings.bulk-destroy'), ['items' => []])->assertStatus(403);
    }

    /** @test */
    public function an_authenticated_user_with_delete_permission_can_delete_specific_building()
    {
        $role = $this->createRoleWithPermissions(['building:delete']);

        $this->signIn(null, $role);

        $building = factory(Building::class)->create();
        $this->deleteJson(route('buildings.destroy', ['building' => $building]))->assertOk();
        $this->assertSoftDeleted('buildings', ['id' => $building->id]);
    }

    /** @test */
    public function an_authenticated_user_with_delete_permission_can_delete_buildings_in_bulk()
    {
        $role = $this->createRoleWithPermissions(['building:delete']);

        $this->signIn(null, $role);

        $buildings = factory(Building::class, 5)->create();
        $this->assertCount(5, Building::all());
        $this->postJson(route('buildings.bulk-destroy'), ['items' => $buildings])->assertOk();
        $this->assertCount(0, Building::all());
    }
}
