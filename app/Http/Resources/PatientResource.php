<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'cpf' => $this->cpf,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'phone' => $this->phone,
            'health_unit_id' => $this->health_unit_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'health_unit' => [
    'name' => $this->healthUnit?->name,
    'city' => $this->healthUnit?->city,
],
        ];
    }
}