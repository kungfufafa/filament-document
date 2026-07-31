<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DocumentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $documents = Document::query()
            ->where('user_id', $request->user()->id)
            ->with('recipients')
            ->latest()
            ->paginate(20);

        return DocumentResource::collection($documents);
    }

    public function show(Request $request, Document $document): DocumentResource
    {
        abort_unless($document->user_id === $request->user()->id, 404);

        return new DocumentResource($document->load('recipients'));
    }
}
