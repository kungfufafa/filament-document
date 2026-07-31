<?php

namespace App\Http\Resources;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Document
 */
class DocumentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status?->value,
            'original_filename' => $this->original_filename,
            'page_count' => $this->page_count,
            'sent_at' => $this->sent_at,
            'completed_at' => $this->completed_at,
            'expires_at' => $this->expires_at,
            'recipients' => DocumentRecipientResource::collection($this->whenLoaded('recipients')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
