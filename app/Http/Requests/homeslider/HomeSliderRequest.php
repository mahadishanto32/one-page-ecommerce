<?php

namespace App\Http\Requests\homeslider;

use Illuminate\Foundation\Http\FormRequest;

class HomeSliderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'nullable|string|max:255',
            'slider_type' => 'required|in:image,video',
            'serial' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ];

        $fileRules = [
            $this->isMethod('POST') ? 'required' : 'nullable',
            'file',
            'max:51200', // 50 MB
        ];

        // Apply different validation depending on slider type
        if ($this->slider_type === 'image') {
            $fileRules[] = 'dimensions:width=1920,height=740';
            $fileRules[] = 'mimes:jpg,jpeg,png,webp,gif';
        } elseif ($this->slider_type === 'video') {
            $fileRules[] = 'mimes:mp4,mov,avi,webm';
        }

        $rules['file'] = $fileRules;

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'file' => 'image or video file',
            'slider_type' => 'slider type',
            'serial' => 'serial number',
            'status' => 'status',
            'name' => 'name',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Please upload a file.',
            'file.max' => 'The file size must not exceed 50 MB.',
            'file.dimensions' => 'The image must be exactly 1920x740 pixels.',
            'file.mimes' => 'The uploaded file type is not valid.',
            'slider_type.required' => 'Please select a slider type.',
            'slider_type.in' => 'The slider type must be either image or video.',
            'serial.integer' => 'The serial must be a number.',
            'serial.min' => 'The serial must be at least 0.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('status')) {
            $this->merge([
                'status' => filter_var($this->status, FILTER_VALIDATE_BOOLEAN)
            ]);
        }
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated();

        if (!isset($validated['serial'])) {
            $validated['serial'] = 0;
        }

        return $validated;
    }
}
