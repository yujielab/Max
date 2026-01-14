<?php

namespace App\Http\Controllers;

use App\Services\StorageGateway;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StorageController extends Controller
{
    public function __construct(private readonly StorageGateway $storage)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $path = $request->string('path', '/')->toString();

        return response()->json([
            'path' => $path,
            'items' => $this->storage->list($path),
        ]);
    }

    public function createFolder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'path' => ['required', 'string'],
            'name' => ['required', 'string'],
        ]);

        $folder = $this->storage->createFolder($data['path'], $data['name']);

        return response()->json(['folder' => $folder], 201);
    }

    public function upload(Request $request): JsonResponse
    {
        $data = $request->validate([
            'path' => ['required', 'string'],
            'file' => ['required', 'file'],
        ]);

        $upload = $this->storage->upload($data['path'], $data['file']);

        return response()->json(['upload' => $upload], 201);
    }
}
