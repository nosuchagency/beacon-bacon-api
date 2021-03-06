<?php

namespace Tests\Unit\Models;

use App\Models\Location;
use App\Models\Poi;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;

class PoiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_poi_has_tags()
    {
        $poi = factory(Poi::class)->create();
        $this->assertInstanceOf(Collection::class, $poi->tags);
    }

    /** @test */
    public function a_poi_has_locations()
    {
        $poi = factory(Poi::class)->create();
        $this->assertInstanceOf(Collection::class, $poi->locations);
    }

    /** @test */
    public function a_poi_is_soft_deleted()
    {
        $poi = factory(Poi::class)->create();
        $poi->delete();
        $this->assertSoftDeleted('pois', ['id' => $poi->id]);
    }

    /** @test */
    public function it_soft_deletes_related_locations()
    {
        $poi = factory(Poi::class)->create();
        $location = factory(Location::class)->create([
            'locatable_id' => $poi->id,
            'locatable_type' => 'poi'
        ]);

        $poi->delete();

        $this->assertSoftDeleted('locations', ['id' => $location->id]);
    }
}
