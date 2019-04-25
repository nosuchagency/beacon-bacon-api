<?php

namespace Tests\Feature\Contents;

use App\Models\Container;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContentsUpdateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_guest_cannot_update_containers()
    {
        $container = factory(Container::class)->create();

        $this->putJson(route('containers.update', ['container' => $container]))->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_user_without_create_permission_cannot_update_containers()
    {
        $this->signIn();

        $container = factory(Container::class)->create();

        $this->putJson(route('containers.update', ['container' => $container]))->assertStatus(403);
    }

    /** @test */
    public function an_authenticated_user_with_update_permission_can_update_containers()
    {
        $container = factory(Container::class)->create();

        $attributes = ['id' => $container->id, 'name' => $this->faker->title];

        $this->update($container, $attributes)->assertStatus(200);

        $this->assertDatabaseHas('containers', $attributes);
    }

    /**
     * @param $container
     * @param array $attributes
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function update($container, $attributes = [])
    {
        $this->signIn()->assignRole(
            $this->createRoleWithPermissions(['containers.update'])
        );

        return $this->putJson(route('containers.update', ['container' => $container]), $this->validFields($attributes));
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
            'description' => $this->faker->paragraph,
            'folders_enabled' => $this->faker->boolean,
            'category' => null,
            'tags' => []
        ], $overrides);
    }
}
