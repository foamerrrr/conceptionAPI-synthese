<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            '_links' => [
                'self' => route('items.show', $this->id),
                'update' => route('items.update', $this->id),
                'delete' => route('items.destroy', $this->id),
                'list' => route('list.show'),
            ],
        ];
    }
}
