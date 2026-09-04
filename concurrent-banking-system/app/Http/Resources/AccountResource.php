<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'account_number' => $this->account_number,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'balance'=> $this->balance,
            'currency' => $this->currency,
            'user' => $this->user
        ];
    }
}
