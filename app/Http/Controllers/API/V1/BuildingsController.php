<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\BaseController;
use App\Http\Requests\BuildingRequest;
use App\Http\Requests\BulkDeleteRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\BuildingResource;
use App\Http\Resources\MapLocationResource;
use App\Models\Building;
use App\Models\Place;
use App\Services\BuildingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BuildingsController extends BaseController
{

    /**
     * @var BuildingService
     */
    protected $buildingService;

    /**
     * BuildingsController constructor.
     *
     * @param BuildingService $buildingService
     */
    public function __construct(BuildingService $buildingService)
    {
        $this->middleware('permission:buildings.create')->only(['store']);
        $this->middleware('permission:buildings.read')->only(['index', 'paginated', 'show']);
        $this->middleware('permission:buildings.update')->only(['update']);
        $this->middleware('permission:buildings.delete')->only(['destroy', 'bulkDestroy']);

        $this->buildingService = $buildingService;
    }

    /**
     *
     * @param Request $request
     * @param Place $place
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, Place $place)
    {
        $buildings = $place->buildings()->withRelations($request)->get();

        return response()->json(BuildingResource::collection($buildings), Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param Place $place
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function paginated(Request $request, Place $place)
    {
        $buildings = $place->buildings()->withRelations($request)->filter($request)->paginate($this->paginationNumber());

        return BuildingResource::collection($buildings);
    }

    /**
     * @param BuildingRequest $request
     * @param Place $place
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BuildingRequest $request, Place $place)
    {
        $building = $this->buildingService->create($request, $place);

        $building->load($building->relationships);

        return response()->json(new BuildingResource($building), Response::HTTP_CREATED);
    }

    /**
     * @param Place $place
     * @param Building $building
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Place $place, Building $building)
    {
        $building->load($building->relationships);

        return response()->json(new BuildingResource($building), Response::HTTP_OK);
    }

    /**
     * @param BuildingRequest $request
     * @param Place $place
     * @param Building $building
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(BuildingRequest $request, Place $place, Building $building)
    {
        $building = $this->buildingService->update($request, $building);

        $building->load($building->relationships);

        return response()->json(new BuildingResource($building), Response::HTTP_OK);
    }

    /**
     * @param Place $place
     * @param Building $building
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Place $place, Building $building)
    {
        $building->delete();

        return response()->json(null, Response::HTTP_OK);
    }

    /**
     * @param BulkDeleteRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDestroy(BulkDeleteRequest $request)
    {
        collect($request->get('items'))->each(function ($building) {
            if ($buildingToDelete = Building::find($building['id'])) {
                $buildingToDelete->delete();
            }
        });

        return response()->json(null, Response::HTTP_OK);
    }

    /**
     * @param SearchRequest $request
     * @param Place $place
     * @param Building $building
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(SearchRequest $request, Place $place, Building $building)
    {
        $locations = $this->searchForLocations($request->all(), $building->locations()->getQuery());

        return response()->json(MapLocationResource::collection($locations), Response::HTTP_OK);
    }
}
