<?php

namespace Tests\Feature\Roles;

use App\Models\Role;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RolesUpdateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_guest_cannot_update_roles()
    {
        $role = factory(Role::class)->create();

        $this->putJson(route('roles.update', ['role' => $role]))->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_without_create_permission_cannot_update_role()
    {
        $this->signIn();

        $role = factory(Role::class)->create();

        $this->putJson(route('roles.update', ['role' => $role]))->assertStatus(403);
    }

    /** @test */
    public function an_authenticated_user_with_update_permission_can_update_roles()
    {
        $role = factory(Role::class)->create();

        $attributes = ['id' => $role->id, 'name' => $this->faker->title];

        $this->update($role, $attributes)->assertStatus(200);

        $this->assertDatabaseHas('roles', $attributes);
    }

    /**
     * @param $role
     * @param array $attributes
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function update($role, $attributes = [])
    {
        $this->signIn()->assignRole(
            $this->createRoleWithPermissions(['roles.update'])
        );

        return $this->putJson(route('roles.update', ['role' => $role]), $this->validFields($attributes));
    }

    /**
     * @param array $overrides
     *
     * @return array
     */
    protected function validFields($overrides = [])
    {
        return array_merge([
            'name' => $this->faker->title,
            'permissions' => factory(Permission::class, 2)->create()
        ], $overrides);
    }
}