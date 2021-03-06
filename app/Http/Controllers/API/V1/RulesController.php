<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RuleRequest;
use App\Http\Resources\RuleResource;
use App\Models\Container;
use App\Models\Rule;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RulesController extends Controller
{

    /**
     * @param RuleRequest $request
     * @param Container $container
     * @param $beaconId
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function store(RuleRequest $request, Container $container, $beaconId)
    {
        $beacon = $container->beacons()->findOrFail($beaconId);

        $rule = $beacon->pivot->rules()->create($request->validated());

        return $this->json(new RuleResource ($rule), Response::HTTP_CREATED);
    }

    /**
     * @param Container $container
     * @param $beaconId
     * @param $rule
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function show(Container $container, $beaconId, $rule)
    {
        $this->authorize('view', Rule::class);

        $beacon = $container->beacons()->findOrFail($beaconId);

        $rule = $beacon->pivot->rules()->findOrFail($rule);

        return $this->json(new RuleResource($rule), Response::HTTP_OK);
    }

    /**
     * @param RuleRequest $request
     * @param Container $container
     * @param $beaconId
     * @param $rule
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function update(RuleRequest $request, Container $container, $beaconId, $rule)
    {
        $beacon = $container->beacons()->findOrFail($beaconId);

        $rule = $beacon->pivot->rules()->findOrFail($rule);

        $rule->fill($request->validated())->save();

        return $this->json(new RuleResource($rule), Response::HTTP_OK);
    }

    /**
     * @param Container $container
     * @param $beaconId
     * @param $rule
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Container $container, $beaconId, $rule)
    {
        $this->authorize('delete', Rule::class);

        $beacon = $container->beacons()->findOrFail($beaconId);

        $beacon->pivot->rules()->findOrFail($rule)->delete();

        return $this->json(null, Response::HTTP_OK);
    }
}
