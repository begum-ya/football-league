<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStandingDataRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

  
    public function rules(): array
    {
     
        return[
            'team_id' => 'required|exists:teams,id',
            'team_score' => 'required|integer|min:0',
            'opponent_score' => 'required|integer|min:0'
        ];
    
    }
}
