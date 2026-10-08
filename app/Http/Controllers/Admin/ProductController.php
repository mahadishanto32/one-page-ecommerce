<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductRequest;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeOption;
use App\Models\ProductCategory;
use App\Models\ProductAttributeVariant;
use App\Models\ProductAttributeVariantCombination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\ProductAttributeOptionImage;

class ProductController extends Controller
{

    protected string $uploadPath = 'uploads/products';
    protected string $videoUploadPath = 'uploads/products/videos';
    protected string $variantUploadPath = 'uploads/products/variants';

    public function index(Request $request)
    {
        // Full collection (not paginate()) — the index view now uses DataTables
        // for search, category filtering and pagination on the client side.
        $products = Product::with(['category', 'productAttribute', 'productAttributeOption'])
            ->latest()
            ->get();

        $categories = ProductCategory::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories    = ProductCategory::orderBy('name')->get();
        $attributes    = ProductAttribute::where('status', true)->orderBy('name')->get();
        $allAttributeOptions = ProductAttributeOption::with('attribute')
                                ->where('status', true)
                                ->orderBy('name')
                                ->get();

        return view('admin.products.create', compact(
            'categories', 
            'attributes',
            'allAttributeOptions'
        ));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->uploadImage($request->file('featured_image'));
        }

        if ($request->hasFile('gallery_images')) {
            $data['gallery_images'] = $this->uploadGallery($request->file('gallery_images'));
        }

        // video arrives as an uploaded file — store it and save the filename
        $data['video'] = $request->hasFile('video')
            ? $this->uploadVideo($request->file('video'))
            : null;

        // specification & size_chart arrive as CKEditor HTML — stored as-is
        $data['specification'] = $request->filled('specification') ? $request->input('specification') : null;
        $data['size_chart']    = $request->filled('size_chart') ? $request->input('size_chart') : null;

        $data['product_tags'] = $this->buildTagsArray($request->input('product_tags'));

        $data['is_featured']      = $request->boolean('is_featured');
        $data['has_featured']     = $request->boolean('has_featured');
        $data['is_show_frontend'] = $request->boolean('is_show_frontend');

        // Handle attribute selection
        // the real source of truth is now the productAttributeVariants relation.
        $data['product_attribute_id'] = null;
        $data['product_attribute_option_id'] = null;

        $variantsInput = $request->input('variants', []);
        $attributeIds = array_values(array_filter(array_map('intval', $request->input('product_attribute_ids', []))));

        $variantQuantityTotal = count($attributeIds) >= 2
            ? $this->calculateCombinationQuantityTotal($request->input('combinations', []))
            : $this->calculateVariantQuantityTotal($request->input('variants', []));

        if ($variantQuantityTotal !== null) {
            $data['quantity'] = $variantQuantityTotal;
        }

        $product = Product::create($data);

        $this->syncVariants($request, $product);
        $this->syncAttributeOptionImages($request, $product);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::orderBy('name')->get();
        $attributes = ProductAttribute::where('status', true)->orderBy('name')->get();

        // Single-attribute variants: [attribute_id => [option_id => model]]
        $existingVariants = $product->productAttributeVariants
            ->groupBy('product_attribute_id')
            ->map(fn ($variants) => $variants->keyBy('product_attribute_option_id'));

        // Combinations: [option_one_id => [option_two_id => model]]
        $existingCombinations = $product->productAttributeVariantCombinations
            ->groupBy('product_option_attribute_one_id')
            ->map(fn ($rows) => $rows->keyBy('product_attribute_option_two_id'));

        // Get selected attribute IDs
        $selectedAttributeIds = [];

        // From variants (single-attribute mode)
        $variantAttributeIds = $product->productAttributeVariants
            ->pluck('product_attribute_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $selectedAttributeIds = array_merge($selectedAttributeIds, $variantAttributeIds);

        /*
         * From combinations (two-attribute mode).
         *
         * IMPORTANT: don't trust product_attribute_two_id blindly — on
         * existing rows it can come back null (missing column / not in
         * $fillable), which previously caused only the "attribute one" id
         * to be added here. We instead resolve each attribute id via the
         * option relationship first (always reliable, since every combo
         * row must point at real options), and only fall back to the raw
         * columns if those relations aren't loaded for some reason.
         */
        if ($product->productAttributeVariantCombinations->isNotEmpty()) {
            $firstCombo = $product->productAttributeVariantCombinations->first();

            $attrOneId = optional($firstCombo->optionOne)->attribute_id
                ?? $firstCombo->product_attribute_id;

            $attrTwoId = optional($firstCombo->optionTwo)->attribute_id
                ?? $firstCombo->product_attribute_two_id;

            if ($attrOneId) {
                $selectedAttributeIds[] = $attrOneId;
            }

            if ($attrTwoId) {
                $selectedAttributeIds[] = $attrTwoId;
            }
        }

        // Remove duplicates and re-index
        $selectedAttributeIds = array_values(array_unique(array_filter($selectedAttributeIds)));

        $allAttributeOptions = ProductAttributeOption::with('attribute')
            ->where('status', true)
            ->orderBy('name')
            ->get();

        return view('admin.products.edit', compact(
            'product',
            'categories',
            'attributes',
            'allAttributeOptions',
            'existingVariants',
            'existingCombinations',
            'selectedAttributeIds'
        ));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->validated();

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        // Only process featured_image if it was uploaded
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($product->featured_image) {
                $this->deleteImage($product->featured_image);
            }
            $data['featured_image'] = $this->uploadImage($request->file('featured_image'));
        } else {
            // Keep existing image if no new one was uploaded
            $data['featured_image'] = $product->featured_image;
        }

        // Only process gallery_images if any were uploaded
        if ($request->hasFile('gallery_images')) {
            // Delete old gallery images
            if ($product->gallery_images) {
                $this->deleteGallery($product->gallery_images);
            }
            $data['gallery_images'] = $this->uploadGallery($request->file('gallery_images'));
        } else {
            $data['gallery_images'] = $product->gallery_images;
        }

        // Only process video if it was uploaded
        if ($request->hasFile('video')) {
            if ($product->video) {
                $this->deleteVideo($product->video);
            }
            $data['video'] = $this->uploadVideo($request->file('video'));
        } else {
            $data['video'] = $product->video;
        }

        // Handle other fields
        $data['specification'] = $request->filled('specification') ? $request->input('specification') : null;
        $data['size_chart']    = $request->filled('size_chart') ? $request->input('size_chart') : null;
        $data['product_tags'] = $this->buildTagsArray($request->input('product_tags'));
        $data['is_featured']      = $request->boolean('is_featured');
        $data['has_featured']     = $request->boolean('has_featured');
        $data['is_show_frontend'] = $request->boolean('is_show_frontend');

        $data['product_attribute_id'] = null;
        $data['product_attribute_option_id'] = null;

        $attributeIds = array_values(array_filter(array_map('intval', $request->input('product_attribute_ids', []))));

        $variantQuantityTotal = count($attributeIds) >= 2
            ? $this->calculateCombinationQuantityTotal($request->input('combinations', []))
            : $this->calculateVariantQuantityTotal($request->input('variants', []));

        if ($variantQuantityTotal !== null) {
            $data['quantity'] = $variantQuantityTotal;
        }

        $product->update($data);
        $this->syncVariants($request, $product);
        $this->syncAttributeOptionImages($request, $product);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->deleteImage($product->featured_image);
        $this->deleteGallery($product->gallery_images);
        $this->deleteVideo($product->video);

        foreach ($product->productAttributeVariants as $variant) {
            $this->deleteVariantImage($variant->image);
        }

        $product->productAttributeVariants()->delete();

        foreach ($product->productAttributeVariantCombinations as $combo) {
            $this->deleteVariantImage($combo->image);
        }

        $product->productAttributeVariantCombinations()->delete();
        $product->attributeOptionImages()->delete();

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Remove a single gallery image via AJAX (used on the edit screen).
     */
    public function removeGalleryImage(Request $request, Product $product)
    {
        $filename = $request->input('filename');
        $gallery  = $product->gallery_images ?? [];

        if (($key = array_search($filename, $gallery)) !== false) {
            $this->deleteImage($filename);
            unset($gallery[$key]);
            $product->update(['gallery_images' => array_values($gallery)]);
        }

        return response()->json(['success' => true]);
    }


    /**
     * AJAX: fetch attribute options for a specific attribute.
     */
    public function getAttributeOptions(Request $request)
    {
        $options = ProductAttributeOption::where('attribute_id', $request->attribute_id)
            ->where('status', true)
            ->orderBy('name')
            ->get(['id', 'name', 'added_price', 'sku', 'quantity']);

        return response()->json($options);
    }

    private function uploadImage($file): string
    {
        $destination = public_path($this->uploadPath);

        if (! File::isDirectory($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move($destination, $filename);

        return $filename;
    }

    private function uploadGallery(array $files): array
    {
        $filenames = [];

        foreach ($files as $file) {
            $filenames[] = $this->uploadImage($file);
        }

        return $filenames;
    }

    private function uploadVideo($file): string
    {
        $destination = public_path($this->videoUploadPath);

        if (! File::isDirectory($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move($destination, $filename);

        return $filename;
    }

    private function deleteImage(?string $filename): void
    {
        if (! $filename) {
            return;
        }

        $path = public_path($this->uploadPath . '/' . $filename);

        if (File::exists($path)) {
            File::delete($path);
        }
    }

    private function deleteGallery(?array $filenames): void
    {
        if (! $filenames) {
            return;
        }

        foreach ($filenames as $filename) {
            $this->deleteImage($filename);
        }
    }

    private function deleteVideo(?string $filename): void
    {
        if (! $filename) {
            return;
        }

        $path = public_path($this->videoUploadPath . '/' . $filename);

        if (File::exists($path)) {
            File::delete($path);
        }
    }

    private function buildTagsArray(?string $tags): ?array
    {
        if (! $tags) {
            return null;
        }

        $tags = array_values(array_filter(array_map('trim', explode(',', $tags))));

        return empty($tags) ? null : $tags;
    }

        private function uploadVariantImage($file): string
    {
        $destination = public_path($this->variantUploadPath);

        if (! File::isDirectory($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $filename = time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move($destination, $filename);

        return $filename;
    }

    private function deleteVariantImage(?string $filename): void
    {
        if (! $filename) {
            return;
        }

        $path = public_path($this->variantUploadPath . '/' . $filename);

        if (File::exists($path)) {
            File::delete($path);
        }
    }


    /**
     * Dispatch to single-attribute variants or two-attribute combinations,
     * depending on how many attributes were selected, and clean up the
     * table that's NOT being used.
     */
    private function syncVariants(Request $request, Product $product): void
    {
        $attributeIds = array_values(array_filter(array_map('intval', $request->input('product_attribute_ids', []))));

        if (count($attributeIds) >= 2) {
            $this->syncAttributeCombinations($request, $product, $attributeIds);

            foreach ($product->productAttributeVariants as $existing) {
                $this->deleteVariantImage($existing->image);
            }
            $product->productAttributeVariants()->delete();
        } else {
            $this->syncSingleAttributeVariants($request, $product);

            foreach ($product->productAttributeVariantCombinations as $existing) {
                $this->deleteVariantImage($existing->image);
            }
            $product->productAttributeVariantCombinations()->delete();
        }
    }

    /**
     * Create/replace ProductAttributeVariant rows (single-attribute mode).
     */
    private function syncSingleAttributeVariants(Request $request, Product $product): void
    {
        foreach ($product->productAttributeVariants as $existing) {
            $this->deleteVariantImage($existing->image);
        }
        $product->productAttributeVariants()->delete();

        $variants = $request->input('variants', []);

        foreach ($variants as $attributeId => $options) {
            if (! is_array($options)) continue;
            if (! ProductAttribute::where('id', $attributeId)->exists()) continue;

            foreach ($options as $optionId => $variantData) {
                if (empty($variantData['enabled'])) continue;

                $option = ProductAttributeOption::where('id', $optionId)
                    ->where('attribute_id', $attributeId)
                    ->first();

                if (! $option) continue;

                $image = null;
                if ($request->hasFile("variants.$attributeId.$optionId.image")) {
                    $file = $request->file("variants.$attributeId.$optionId.image");
                    if ($file->isValid()) {
                        $image = $this->uploadVariantImage($file);
                    }
                }

                ProductAttributeVariant::create([
                    'product_id'                  => $product->id,
                    'product_attribute_id'        => $attributeId,
                    'product_attribute_option_id' => $optionId,
                    'image'                       => $image,
                    'sku'                         => $variantData['sku'] ?? null,
                    'quantity'                    => ($variantData['quantity'] ?? '') !== '' ? (int) $variantData['quantity'] : null,
                    'additional_price'            => ($variantData['additional_price'] ?? '') !== '' ? $variantData['additional_price'] : null,
                ]);
            }
        }
    }

    /**
     * Create/replace ProductAttributeVariantCombination rows (two-attribute mode).
     * $attributeIds[0] = "attribute one", $attributeIds[1] = "attribute two"
     * — extra selected attributes beyond these two are ignored.
     */
    private function syncAttributeCombinations(Request $request, Product $product, array $attributeIds): void
    {
        // Delete existing combinations and their images
        foreach ($product->productAttributeVariantCombinations as $existing) {
            $this->deleteVariantImage($existing->image);
        }
        $product->productAttributeVariantCombinations()->delete();

        $attributeOneId = $attributeIds[0];
        $attributeTwoId = $attributeIds[1];

        $combinations = $request->input('combinations', []);

        foreach ($combinations as $optionOneId => $secondLevel) {
            if (! is_array($secondLevel)) continue;

            $optionOne = ProductAttributeOption::where('id', $optionOneId)
                ->where('attribute_id', $attributeOneId)
                ->first();

            if (! $optionOne) continue;

            foreach ($secondLevel as $optionTwoId => $comboData) {
                if (empty($comboData['enabled'])) continue;

                $optionTwo = ProductAttributeOption::where('id', $optionTwoId)
                    ->where('attribute_id', $attributeTwoId)
                    ->first();

                if (! $optionTwo) continue;

                $image = null;
                if ($request->hasFile("combinations.$optionOneId.$optionTwoId.image")) {
                    $file = $request->file("combinations.$optionOneId.$optionTwoId.image");
                    if ($file->isValid()) {
                        $image = $this->uploadVariantImage($file);
                    }
                }

                ProductAttributeVariantCombination::create([
                    'product_id'                        => $product->id,
                    'product_attribute_id'              => $attributeOneId,  // Store the first attribute ID
                    'product_attribute_one_id'          => $attributeOneId,  // If your schema has this field
                    'product_attribute_two_id'          => $attributeTwoId,  // If your schema has this field
                    'product_option_attribute_one_id'   => $optionOneId,
                    'product_attribute_option_two_id'   => $optionTwoId,
                    'image'                             => $image,
                    'sku'                               => $comboData['sku'] ?? null,
                    'quantity'                          => ($comboData['quantity'] ?? '') !== '' ? (int) $comboData['quantity'] : null,
                    'additional_price'                  => ($comboData['additional_price'] ?? '') !== '' ? $comboData['additional_price'] : null,
                ]);
            }
        }
    }

    private function calculateVariantQuantityTotal(array $variants): ?int
    {
        $hasAnyQuantity = false;
        $total = 0;

        foreach ($variants as $optionsByAttribute) {
            if (! is_array($optionsByAttribute)) {
                continue;
            }

            foreach ($optionsByAttribute as $variantData) {
                if (empty($variantData['enabled'])) {
                    continue;
                }

                if (isset($variantData['quantity']) && $variantData['quantity'] !== '') {
                    $hasAnyQuantity = true;
                    $total += (int) $variantData['quantity'];
                }
            }
        }

        return $hasAnyQuantity ? $total : null;
    }

    private function calculateCombinationQuantityTotal(array $combinations): ?int
    {
        $hasAnyQuantity = false;
        $total = 0;

        foreach ($combinations as $secondLevel) {
            if (! is_array($secondLevel)) continue;

            foreach ($secondLevel as $comboData) {
                if (empty($comboData['enabled'])) continue;

                if (isset($comboData['quantity']) && $comboData['quantity'] !== '') {
                    $hasAnyQuantity = true;
                    $total += (int) $comboData['quantity'];
                }
            }
        }

        return $hasAnyQuantity ? $total : null;
    }

    private function syncAttributeOptionImages(Request $request, Product $product): void
    {
        $product->attributeOptionImages()->delete();

        // Product::create()/update() এর পর $product->gallery_images ইতিমধ্যে
        // চূড়ান্ত (সদ্য আপলোড হওয়া অথবা আগের) filename array রাখে।
        $galleryImages = $product->gallery_images ?? [];

        $resolve = function ($value) use ($galleryImages) {
            if (! $value) return null;

            // ফ্রন্টএন্ডে সদ্য সিলেক্ট করা (তখনো আপলোড হয়নি) ফাইলের জন্য placeholder
            if (str_starts_with($value, 'NEW_')) {
                $index = (int) substr($value, 4);
                return $galleryImages[$index] ?? null;
            }

            // আগে থেকেই সেভ করা গ্যালারির filename — সিকিউরিটি চেক
            return in_array($value, $galleryImages, true) ? $value : null;
        };

        $linked = []; // optionId => filename, শেষ পর্যন্ত bulk insert হবে

        // Single-attribute (variants) mode
        foreach ($request->input('variants', []) as $attributeId => $options) {
            if (! is_array($options)) continue;

            foreach ($options as $optionId => $variantData) {
                if (empty($variantData['enabled'])) continue;

                $filename = $resolve($variantData['gallery_image'] ?? null);
                if ($filename) {
                    $linked[$optionId] = $filename;
                }
            }
        }

        // Two-attribute (combinations) mode
        foreach ($request->input('combinations', []) as $optionOneId => $secondLevel) {
            if (! is_array($secondLevel)) continue;

            foreach ($secondLevel as $optionTwoId => $comboData) {
                if (empty($comboData['enabled'])) continue;

                $filenameOne = $resolve($comboData['gallery_image_one'] ?? null);
                $filenameTwo = $resolve($comboData['gallery_image_two'] ?? null);

                if ($filenameOne) $linked[$optionOneId] = $filenameOne;
                if ($filenameTwo) $linked[$optionTwoId] = $filenameTwo;
            }
        }

        foreach ($linked as $optionId => $filename) {
            ProductAttributeOptionImage::create([
                'product_id'                   => $product->id,
                'product_attribute_option_id'  => $optionId,
                'image_path'                   => $filename,
            ]);
        }
    }
}