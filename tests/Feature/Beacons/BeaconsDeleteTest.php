<?php

namespace Tests\Feature\Beacons;

use App\Models\Beacon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BeaconsDeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_cannot_delete_beacons()
    {
        $this->postJson(route('beacons.bulk-destroy'))->assertStatus(401);
        $this->deleteJson(route('beacons.destroy', ['beacon' => factory(Beacon::class)->create()]))->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_without_delete_permission_cannot_delete_beacons()
    {
        $this->signIn();
        $beacon = factory(Beacon::class)->create();
        $this->deleteJson(route('beacons.destroy', ['beacon' => $beacon]))->assertStatus(403);

        $this->postJson(route('beacons.bulk-destroy'), ['items' => []])->assertStatus(403);
    }

    /** @test */
    public function an_authenticated_user_with_delete_permission_can_delete_specific_beacon()
    {
        $role = $this->createRoleWithPermissions(['beacon:delete']);

        $this->signIn(null, $role);

        $beacon = factory(Beacon::class)->create();
        $this->deleteJson(route('beacons.destroy', ['beacon' => $beacon]))->assertOk();
        $this->assertSoftDeleted('beacons', ['id' => $beacon->id]);
    }

    /** @test */
    public function an_authenticated_user_with_delete_permission_can_delete_beacons_in_bulk()
    {
        $role = $this->createRoleWithPermissions(['beacon:delete']);

        $this->signIn(null, $role);

        $beacons = factory(Beacon::class, 5)->create();
        $this->assertCount(5, Beacon::all());
        $this->postJson(route('beacons.bulk-destroy'), ['items' => $beacons])->assertOk();
        $this->assertCount(0, Beacon::all());
    }
}
