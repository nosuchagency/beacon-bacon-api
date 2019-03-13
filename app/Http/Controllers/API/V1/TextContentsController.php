<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TextContentRequest;
use App\Http\Resources\ContentResource;
use App\Models\Container;
use App\Models\Content\TextContent;
use App\Models\Folder;
use App\Models\Tag;
use Illuminate\Http\Response;

class TextContentsController extends Controller
{

    /**
     * BuildingsController constructor.
     */
    public function __construct()
    {
        $this->middleware('permission:contents.create')->only(['store']);
        $this->middleware('permission:contents.update')->only(['update']);
        $this->middleware('permission:contents.delete')->only(['destroy']);
    }

    /**
     * @param TextContentRequest $request
     * @param Container $container
     * @param Folder $folder
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TextContentRequest $request, Container $container, Folder $folder)
    {
        $text = new TextContent($request->validated());
        $text->container()->associate($container);
        $text = $folder->texts()->save($text);

        foreach ($request->get('tags') as $tag) {
            $text->tags()->attach(Tag::find($tag['id']));
        }

        return response()->json(new ContentResource($text), Response::HTTP_CREATED);
    }

    /**
     * @param TextContentRequest $request
     * @param Container $container
     * @param Folder $folder
     * @param TextContent $text
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TextContentRequest $request, Container $container, Folder $folder, TextContent $text)
    {
        $text->fill($request->validated())->save();

        $text->tags()->sync([]);

        foreach ($request->get('tags') as $tag) {
            $text->tags()->attach(Tag::find($tag['id']));
        }

        return response()->json(new ContentResource($text), Response::HTTP_OK);
    }

    /**
     * @param Container $container
     * @param Folder $folder
     * @param TextContent $text
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Container $container, Folder $folder, TextContent $text)
    {
        $text->delete();

        return response()->json(null, Response::HTTP_OK);
    }
}
