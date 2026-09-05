<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResources extends JsonResource
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
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phone' => $this->phone,
            'cin' => $this->cin,
            'nationality' => $this->nationality,
            'country' => $this->country,
            'city' => $this->city,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'role' => $this->role
        ];
    }
}
