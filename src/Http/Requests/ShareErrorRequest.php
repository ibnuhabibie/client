<?php

namespace Laracatch\Client\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShareErrorRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'error' => 'required',
            'tabs' => 'required|array|min:1',
            'lineSelection' => [],
        ];
    }
}
