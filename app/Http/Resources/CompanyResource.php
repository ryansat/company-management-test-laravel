<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'logo' => $this->logo ? asset('storage/' . $this->logo) : null,
            'website' => $this->website,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'employee_count' => $this->employees_count ?? 0,
        ];
    }
}
