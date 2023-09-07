<?php

namespace AnisAronno\MediaHelper\Http\Controllers;

use AnisAronno\MediaHelper\Facades\Media;
use AnisAronno\MediaHelper\Http\Requests\StoreImageRequest;
use AnisAronno\MediaHelper\Http\Requests\UpdateImageRequest;
use AnisAronno\MediaHelper\Http\Resources\ImageResources;
use AnisAronno\MediaHelper\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ImageController extends Controller
{
    /**
     * Get ALl Image
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $images = Image::query()
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where('title', 'LIKE', '%' . $request->input('search') . '%');
            })
            ->when($request->has('startDate') && $request->has('endDate'), function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    new \DateTime($request->input('startDate')),
                    new \DateTime($request->input('endDate'))
                ]);
            })
            ->orderBy($request->input('orderBy', 'id'), $request->input('order', 'desc'))
            ->paginate(20)->withQueryString();

        return response()->json(ImageResources::collection($images));
    }


    /**
     * Show Image
     *
     * @param Image $image
     * @return JsonResponse
     */
    public function show(Image $image): JsonResponse
    {
        return  response()->json(new ImageResources($image));
    }

    /**
     *   Image store
     *
     * @param StoreImageRequest $request
     * @return JsonResponse
     */
    public function store(StoreImageRequest $request): JsonResponse
    {
        $data['title'] = $request->input('title', 'Image');
        $data['user_id'] = auth()->user()?->id ;

        if ($request->image) {
            $data['url'] = Media::upload($request, 'image', 'images');
            $data['mimes'] = $request->image->extension();
            $data['type'] = $request->image->getClientMimeType();
            $data['size'] = number_format($request->image->getSize() / (1024 * 1024), 2, '.', '')."MB";
        }

        try {
            Image::create($data);
            return response()->json(['message' => 'Created successfull']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     * Image Update
     *
     * @param UpdateImageRequest $request
     * @param Image $image
     * @return JsonResponse
     */
    public function update(UpdateImageRequest $request, Image $image): JsonResponse
    {
        try {
            $image->update($request->only('title'));
            return response()->json(['message' => 'Update successfull']);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     * Delete image
     *
     * @param Image $image
     * @return JsonResponse
     */
    public function destroy(Image $image): JsonResponse
    {
        try {
            Media::delete($image->url);

            $image->delete();

            return response()->json(['message' => 'Deleted successfull']);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Deleted failed'], 400);
        }
    }

    /**
     * Image Group Delete
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function groupDelete(Request $request): JsonResponse
    {
        try {
            foreach ($request->data as  $image) {
                isset($image['url']) ? Media::delete($image['url']) : '';
            }

            $idArr = array_column($request->data, 'id');
            $result = Image::whereIn('id', $idArr)->delete();
            if ($result) {
                return response()->json(['message' => 'Deleted successfull']);
            }
            return response()->json(['message' => 'Deleted failed'], 400);

        } catch (\Throwable $th) {
            return response()->json(['message' => 'Deleted failed'], 400);
        }
    }
}
