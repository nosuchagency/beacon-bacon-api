<?php

namespace Tests\Feature\Structures;

use App\Models\Structure;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StructuresDeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_cannot_delete_structures()
    {
        $this->postJson(route('structures.bulk-destroy'))->assertStatus(401);
        $this->deleteJson(route('structures.destroy', ['structure' => factory(Structure::class)->create()]))->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_without_delete_permission_cannot_delete_structures()
    {
        $this->signIn();
        $structure = factory(Structure::class)->create();
        $this->deleteJson(route('structures.destroy', ['structure' => $structure]))->assertStatus(403);

        $this->postJson(route('structures.bulk-destroy'), ['items' => []])->assertStatus(403);
    }

    /** @test */
    public function an_authenticated_user_with_delete_permission_can_delete_specific_structure()
    {
        $role = $this->createRoleWithPermissions(['structure:delete']);

        $this->signIn(null, $role);

        $structure = factory(Structure::class)->create();
        $this->deleteJson(route('structures.destroy', ['structure' => $structure]))->assertOk();
        $this->assertSoftDeleted('structures', ['id' => $structure->id]);
    }

    /** @test */
    public function an_authenticated_user_with_delete_permission_can_delete_structures_in_bulk()
    {
        $role = $this->createRoleWithPermissions(['structure:delete']);

        $this->signIn(null, $role);

        $structures = factory(Structure::class, 5)->create();
        $this->assertCount(5, Structure::all());
        $this->postJson(route('structures.bulk-destroy'), ['items' => $structures])->assertOk();
        $this->assertCount(0, Structure::all());
    }
}
