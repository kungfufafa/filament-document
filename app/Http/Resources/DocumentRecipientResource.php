<?php

namespace App\Http\Resources;

use App\Models\DocumentRecipient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin DocumentRecipient
 */
class DocumentRecipientResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role?->value,
            'status' => $this->status?->value,
            'signing_order' => $this->signing_order,
            'signed_at' => $this->signed_at,
            'signing_url' => route('signing.show', $this->access_token),
        ];
    }
}
