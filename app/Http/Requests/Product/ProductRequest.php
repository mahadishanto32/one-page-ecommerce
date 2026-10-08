<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;
        $isPost = $this->isMethod('post');

        return [
            'name'              => 'required|string|max:255',
            'slug'              => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'short_description' => 'required|string',
            'description'       => 'nullable|string',

            'price'             => 'required|numeric|min:0',
            'quantity'          => 'required|integer|min:0',
            'stock_status'      => 'required|in:In Stock,Out of Stock,Pre-Order',
            'previous_price'    => 'nullable|numeric|min:0',

            'sku'               => 'nullable|string|max:255',
            'min_order'         => 'nullable|integer|min:1',
            'max_order'         => 'nullable|integer|min:1|gte:min_order',

            // Fix: Use required_without for edit to handle existing files
            'featured_image'    => [
                $isPost ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ],
            'gallery_images'    => 'nullable|array',
            'gallery_images.*'  => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            'video'             => 'nullable|file|mimes:mp4,mov,avi,wmv,webm,mkv|max:51200',

            'category_id'       => 'required|exists:product_categories,id',
            'subcategory_id'    => 'nullable|exists:product_subcategories,id',
            'brand_id'          => 'nullable|exists:brands,id',

            'product_unit'      => 'nullable|string|max:50',

            'specification'     => 'nullable|string',
            'product_tags'      => 'nullable|string',

            'is_featured'       => 'nullable|boolean',
            'is_show_frontend'  => 'nullable|boolean',

            'size_chart'        => 'nullable|string',

            'status'            => 'required|in:Active,Inactive',

            'product_attribute_ids'   => 'nullable|array',
            'product_attribute_ids.*' => 'integer|exists:product_attributes,id',

            // Single-attribute mode variants
            'variants'                      => 'nullable|array',
            'variants.*'                    => 'array',
            'variants.*.*.enabled'          => 'nullable|boolean',
            'variants.*.*.image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'variants.*.*.sku'              => 'nullable|string|max:255',
            'variants.*.*.quantity'         => 'nullable|integer|min:0',
            'variants.*.*.additional_price' => 'nullable|numeric|min:0',
            'variants.*.*.gallery_image'        => 'nullable|string|max:255',
            'combinations.*.*.gallery_image_one' => 'nullable|string|max:255',
            'combinations.*.*.gallery_image_two' => 'nullable|string|max:255',

            // Two-attribute combination mode
            'combinations'                      => 'nullable|array',
            'combinations.*'                    => 'array',
            'combinations.*.*.enabled'          => 'nullable|boolean',
            'combinations.*.*.image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'combinations.*.*.sku'              => 'nullable|string|max:255',
            'combinations.*.*.quantity'         => 'nullable|integer|min:0',
            'combinations.*.*.additional_price' => 'nullable|numeric|min:0',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->validateSingleAttributeVariants($validator);
            $this->validateAttributeCombinations($validator);
        });
    }

    private function validateSingleAttributeVariants($validator): void
    {
        $variants = $this->input('variants', []);

        if (! is_array($variants) || empty($variants)) {
            return;
        }

        $attributeIds = array_keys($variants);

        $validAttributeIds = \App\Models\ProductAttribute::whereIn('id', $attributeIds)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->toArray();

        foreach ($variants as $attributeId => $options) {
            if (! in_array((string) $attributeId, $validAttributeIds, true)) {
                $validator->errors()->add('variants', "Attribute [$attributeId] in variants is invalid.");
                continue;
            }

            if (! is_array($options)) {
                continue;
            }

            $optionIds = array_keys($options);
            if (empty($optionIds)) {
                continue;
            }

            $validOptionIds = \App\Models\ProductAttributeOption::whereIn('id', $optionIds)
                ->where('attribute_id', $attributeId)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();

            foreach ($optionIds as $optionId) {
                if (! in_array((string) $optionId, $validOptionIds, true)) {
                    $validator->errors()->add(
                        'variants',
                        "Option [$optionId] does not belong to attribute [$attributeId]."
                    );
                }
            }
        }
    }

    private function validateAttributeCombinations($validator): void
    {
        $combinations = $this->input('combinations', []);

        if (! is_array($combinations) || empty($combinations)) {
            return;
        }

        $attributeIds = array_values(array_filter(array_map(
            'intval',
            $this->input('product_attribute_ids', [])
        )));

        if (count($attributeIds) < 2) {
            $validator->errors()->add(
                'combinations',
                'Combination data was submitted, but fewer than two attributes are selected.'
            );
            return;
        }

        [$attributeOneId, $attributeTwoId] = [$attributeIds[0], $attributeIds[1]];

        $optionOneIds = array_keys($combinations);

        $validOptionOneIds = \App\Models\ProductAttributeOption::whereIn('id', $optionOneIds)
            ->where('attribute_id', $attributeOneId)
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->toArray();

        foreach ($combinations as $optionOneId => $secondLevel) {
            if (! in_array((string) $optionOneId, $validOptionOneIds, true)) {
                $validator->errors()->add(
                    'combinations',
                    "Option [$optionOneId] does not belong to the first selected attribute [$attributeOneId]."
                );
                continue;
            }

            if (! is_array($secondLevel)) {
                continue;
            }

            $optionTwoIds = array_keys($secondLevel);
            if (empty($optionTwoIds)) {
                continue;
            }

            $validOptionTwoIds = \App\Models\ProductAttributeOption::whereIn('id', $optionTwoIds)
                ->where('attribute_id', $attributeTwoId)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();

            foreach ($optionTwoIds as $optionTwoId) {
                if (! in_array((string) $optionTwoId, $validOptionTwoIds, true)) {
                    $validator->errors()->add(
                        'combinations',
                        "Option [$optionTwoId] does not belong to the second selected attribute [$attributeTwoId]."
                    );
                }
            }
        }
    }

    public function messages(): array
    {
        return [
            'featured_image.required' => 'Please upload a featured image.',
            'category_id.required'    => 'Please select a category.',
            'max_order.gte'           => 'Max order must be greater than or equal to min order.',
            'video.mimes'             => 'The video must be a file of type: mp4, mov, avi, wmv, webm, mkv.',
            'video.max'               => 'The video must not be larger than 50MB.',
        ];
    }
}