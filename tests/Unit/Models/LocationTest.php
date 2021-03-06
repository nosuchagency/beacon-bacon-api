<?php

namespace Tests\Unit\Models;

use App\Models\Beacon;
use App\Models\Container;
use App\Models\Fixture;
use App\Models\Floor;
use App\Models\Location;
use App\Models\Poi;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_floor()
    {
        $location = factory(Location::class)->create();
        $this->assertInstanceOf(Floor::class, $location->floor);
    }

    /** @test */
    public function it_belongs_to_a_container()
    {
        $location = factory(Location::class)->create();
        $this->assertInstanceOf(Container::class, $location->container);
    }

    /** @test */
    public function it_belongs_to_a_poi()
    {
        $location = factory(Location::class)->create([
            'locatable_id' => factory(Poi::class)->create()->id,
            'locatable_type' => 'poi'
        ]);
        $this->assertInstanceOf(Poi::class, $location->locatable);
    }

    /** @test */
    public function it_belongs_to_a_fixture()
    {
        $location = factory(Location::class)->create([
            'locatable_id' => factory(Fixture::class)->create()->id,
            'locatable_type' => 'fixture'
        ]);
        $this->assertInstanceOf(Fixture::class, $location->locatable);
    }

    /** @test */
    public function it_belongs_to_a_beacon()
    {
        $location = factory(Location::class)->create([
            'locatable_id' => factory(Beacon::class)->create()->id,
            'locatable_type' => 'beacon'
        ]);
        $this->assertInstanceOf(Beacon::class, $location->locatable);
    }

    /** @test */
    public function a_location_is_soft_deleted()
    {
        $location = factory(Location::class)->create();
        $location->delete();
        $this->assertSoftDeleted('locations', ['id' => $location->id]);
    }
}
