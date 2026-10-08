@php
    $product = $product ?? null;
    $old = fn ($key, $default = null) => old($key, $product->{$key} ?? $default);

    $existingVariantsData = isset($existingVariants)
        ? $existingVariants->map(fn ($optionsByAttribute) => $optionsByAttribute->map(fn ($v) => [
            'sku' => $v->sku,
            'quantity' => $v->quantity,
            'additional_price' => $v->additional_price,
            'image' => $v->image,
        ])->toArray())->toArray()
        : [];

    $existingCombinationsData = isset($existingCombinations)
        ? $existingCombinations->map(fn ($byOptionTwo) => $byOptionTwo->map(fn ($c) => [
            'sku' => $c->sku,
            'quantity' => $c->quantity,
            'additional_price' => $c->additional_price,
            'image' => $c->image,
        ])->toArray())->toArray()
        : [];

    /*
     * Which attribute checkboxes should be pre-checked on load.
     *
     * We don't rely on $selectedAttributeIds alone: for a 2-attribute
     * (combination) product, that value has been coming back incomplete
     * (only one of the two attributes), while $existingVariantsData /
     * $existingCombinationsData themselves are always correct. So we
     * derive the checked attributes directly from that real data as a
     * safety net and merge it with whatever the controller supplied.
     */
    $derivedSelectedAttributeIds = collect();

    // Single-attribute mode: keys of $existingVariantsData are attribute ids.
    if (!empty($existingVariantsData)) {
        $derivedSelectedAttributeIds = $derivedSelectedAttributeIds->merge(array_keys($existingVariantsData));
    }

    // Combination mode: $existingCombinationsData is keyed by option ids (not
    // attribute ids), so match those option ids back to their attribute via
    // each attribute's own options collection.
    if (!empty($existingCombinationsData)) {
        $comboOptionIds = collect(array_keys($existingCombinationsData))
            ->merge(
                collect($existingCombinationsData)->flatMap(fn ($byOptionTwo) => array_keys($byOptionTwo))
            )
            ->map(fn ($id) => (string) $id)
            ->unique();

        foreach ($attributes as $attribute) {
            $attributeOptionIds = collect($attribute->options ?? [])
                ->pluck('id')
                ->map(fn ($id) => (string) $id);

            if ($attributeOptionIds->intersect($comboOptionIds)->isNotEmpty()) {
                $derivedSelectedAttributeIds->push($attribute->id);
            }
        }
    }

    $selectedAttributeIds = old(
        'product_attribute_ids',
        collect($selectedAttributeIds ?? [])
            ->merge($derivedSelectedAttributeIds)
            ->unique()
            ->values()
            ->all()
    );

    $galleryImages = $product->gallery_images ?? [];
    $existingOptionImages = $product
        ? $product->attributeOptionImages()->pluck('image_path', 'product_attribute_option_id')->toArray()
        : [];
@endphp

<div class="product-form">
<div class="row g-3">

    {{-- Basic Info --}}
    <div class="col-12">
        <div class="product-form-section">
            <div class="product-form-section-header">
                <span class="section-icon"><i class="fa-solid fa-file-lines"></i></span>
                <div>
                    <h5>Basic Information</h5>
                    <div class="section-subtitle">Name, slug and the short pitch for this product</div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Product Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" value="{{ $old('name') }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" value="{{ $old('slug') }}"
                           class="form-control @error('slug') is-invalid @enderror"
                           placeholder="Leave blank to auto-generate">
                    @error('slug') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Short Description <span class="text-danger">*</span></label>
                    <div class="editor-field-wrap editor-field-light @error('short_description') is-invalid @enderror">
                        <textarea name="short_description" id="short_description" rows="2"
                                  class="ckeditor-field ckeditor-field-light @error('short_description') is-invalid @enderror" required>{{ $old('short_description') }}</textarea>
                    </div>
                    <div class="form-text">A one or two line summary shown in listing and search cards.</div>
                    @error('short_description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <div class="editor-field-wrap @error('description') is-invalid @enderror">
                        <textarea name="description" id="description" rows="6"
                                  class="ckeditor-field @error('description') is-invalid @enderror">{{ $old('description') }}</textarea>
                    </div>
                    @error('description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Pricing & Stock --}}
    <div class="col-12">
        <div class="product-form-section">
            <div class="product-form-section-header">
                <span class="section-icon"><i class="fa-solid fa-tag"></i></span>
                <div>
                    <h5>Pricing &amp; Stock</h5>
                    <div class="section-subtitle">Price, availability and order limits</div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Price <span class="text-danger">*</span></label>
                    <div class="input-group-icon">
                        <span class="input-icon">$</span>
                        <input type="number" step="0.01" min="0" name="price" value="{{ $old('price') }}"
                               class="form-control @error('price') is-invalid @enderror" required>
                    </div>
                    @error('price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Previous Price</label>
                    <div class="input-group-icon">
                        <span class="input-icon">$</span>
                        <input type="number" step="0.01" min="0" name="previous_price" value="{{ $old('previous_price') }}"
                               class="form-control @error('previous_price') is-invalid @enderror">
                    </div>
                    @error('previous_price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" min="0" name="quantity" value="{{ $old('quantity', 0) }}"
                           class="form-control @error('quantity') is-invalid @enderror" required>
                    @error('quantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Stock Status <span class="text-danger">*</span></label>
                    <select name="stock_status" class="form-select @error('stock_status') is-invalid @enderror" required>
                        @foreach (['In Stock', 'Out of Stock', 'Pre-Order'] as $status)
                            <option value="{{ $status }}" {{ $old('stock_status', 'In Stock') === $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                    @error('stock_status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" value="{{ $old('sku') }}"
                           class="form-control @error('sku') is-invalid @enderror">
                    @error('sku') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Min Order</label>
                    <input type="number" min="1" name="min_order" value="{{ $old('min_order') }}"
                           class="form-control @error('min_order') is-invalid @enderror">
                    @error('min_order') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Max Order</label>
                    <input type="number" min="1" name="max_order" value="{{ $old('max_order') }}"
                           class="form-control @error('max_order') is-invalid @enderror">
                    @error('max_order') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Media --}}
    <div class="col-12">
        <div class="product-form-section">
            <div class="product-form-section-header">
                <span class="section-icon"><i class="fa-solid fa-images"></i></span>
                <div>
                    <h5>Media</h5>
                    <div class="section-subtitle">Featured image, gallery and an optional video link</div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">
                        Featured Image {{ $product ? '' : '(required)' }}
                    </label>
                    <div class="media-upload-box">
                        <input type="file" name="featured_image" id="featured_image_input" accept="image/*"
                            class="form-control @error('featured_image') is-invalid @enderror" 
                            {{ $product ? '' : 'required' }}>
                    </div>
                    @error('featured_image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                    <div class="media-current-preview" id="featured_image_preview"
                        style="{{ ($product && $product->featured_image) ? '' : 'display:none;' }}">
                        <img src="{{ $product && $product->featured_image ? asset('uploads/products/' . $product->featured_image) : '' }}"
                            alt="Featured image" id="featured_image_preview_img">
                        <div class="form-text" id="featured_image_preview_text">
                            {{ ($product && $product->featured_image) ? 'Current image. Upload a new file to replace it.' : '' }}
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Gallery Images</label>
                    <div class="media-upload-box">
                        <input type="file" name="gallery_images[]" id="gallery_images_input" accept="image/*" multiple
                            class="form-control @error('gallery_images') is-invalid @enderror @error('gallery_images.*') is-invalid @enderror">
                    </div>
                    @error('gallery_images') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    @error('gallery_images.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    <div class="form-text">You can select multiple images. Uploading new files replaces the current gallery.</div>

                    @if ($product && $product->gallery_images)
                        <div class="gallery-thumb-grid" id="current-gallery">
                            @foreach ($product->gallery_images as $image)
                                <div class="gallery-thumb" data-filename="{{ $image }}">
                                    <img src="{{ asset('uploads/products/' . $image) }}">
                                    <button type="button"
                                            class="gallery-thumb-remove remove-gallery-image"
                                            data-product-id="{{ $product->id }}"
                                            data-filename="{{ $image }}"
                                            title="Remove">&times;</button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Live preview of newly selected files (not yet uploaded) --}}
                    <div class="gallery-thumb-grid" id="gallery-new-preview"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Product Video</label>
                    <div class="media-upload-box">
                        <input type="file" name="video" id="video_input" accept="video/*"
                            class="form-control @error('video') is-invalid @enderror">
                    </div>
                    <div class="form-text">Upload a video file directly (MP4, MOV, WebM, etc.). Uploading a new file replaces the current one.</div>
                    @error('video') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                    <div class="media-current-preview media-current-preview-video" id="video_preview"
                        style="{{ ($product && $product->video) ? '' : 'display:none;' }}">
                        <video src="{{ $product && $product->video ? asset('uploads/products/videos/' . $product->video) : '' }}"
                            controls id="video_preview_el"></video>
                        <div class="form-text" id="video_preview_text">
                            {{ ($product && $product->video) ? 'Current video. Upload a new file to replace it.' : '' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Categorization --}}
    <div class="col-12">
        <div class="product-form-section">
            <div class="product-form-section-header">
                <span class="section-icon"><i class="fa-solid fa-sitemap"></i></span>
                <div>
                    <h5>Categorization</h5>
                    <div class="section-subtitle">Where this product lives in your catalog</div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Category <span class="text-danger">*</span></label>
                    <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        <option value="">-- Select Category --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ (string) $old('category_id') === (string) $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Product Unit</label>
                    <select name="product_unit" class="form-select @error('product_unit') is-invalid @enderror">
                        <option value="">-- Select Unit --</option>
                        @foreach (['kg' => 'Kg', 'gm' => 'Gm', 'pcs' => 'Pcs'] as $value => $label)
                            <option value="{{ $value }}" {{ $old('product_unit') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('product_unit') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-8">
                    <label class="form-label">Product Tags</label>
                    <input type="text" name="product_tags"
                        value="{{ old('product_tags', $product && $product->product_tags ? implode(', ', $product->product_tags) : '') }}"
                        class="form-control @error('product_tags') is-invalid @enderror"
                        placeholder="e.g. summer, cotton, new-arrival">
                    <div class="form-text">Comma-separated tags.</div>
                    @error('product_tags') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Product Attributes --}}
    <div class="col-12">
        <div class="product-form-section">
            <div class="product-form-section-header">
                <span class="section-icon"><i class="fa-solid fa-sliders"></i></span>
                <div>
                    <h5>Product Attributes</h5>
                    <div class="section-subtitle">Pick every attribute this product varies by, e.g. Color and Size</div>
                </div>
            </div>

            <div class="row g-3">
                {{-- Attribute selection checkboxes: one attribute name per row, not inline --}}
                <div class="col-12">
                    <label class="form-label">Select Attributes</label>
                    <div class="d-flex flex-column gap-3 attribute-checkbox-list">
                        @foreach ($attributes as $attribute)
                            <div class="form-check py-1">
                                <input class="form-check-input attribute-toggle" type="checkbox"
                                    name="product_attribute_ids[]" value="{{ $attribute->id }}"
                                    id="attr_{{ $attribute->id }}"
                                    data-name="{{ $attribute->name }}"
                                    {{ in_array($attribute->id, $selectedAttributeIds) ? 'checked' : '' }}>
                                <label class="form-check-label" for="attr_{{ $attribute->id }}">{{ $attribute->name }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-text">
                        Check one attribute for single-option variants (e.g. just Color), or two attributes to build
                        combinations (e.g. Color + Size).
                    </div>
                    @error('product_attribute_ids') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                {{-- Click-to-build attribute option / variant area --}}
                <div class="col-12">
                    <div id="attribute-tabs-wrapper" class="attribute-tabs-wrapper">
                        <div class="text-muted small" id="attribute-tabs-placeholder">
                            <i class="fa-regular fa-hand-point-up me-1"></i> Check an attribute above to configure its options.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Specification --}}
    <div class="col-12">
        <div class="product-form-section">
            <div class="product-form-section-header">
                <span class="section-icon"><i class="fa-solid fa-list-check"></i></span>
                <div>
                    <h5>Specification</h5>
                    <div class="section-subtitle">Full technical details, formatted however you like</div>
                </div>
            </div>

            <div class="editor-field-wrap @error('specification') is-invalid @enderror">
                <textarea name="specification" id="specification" rows="6"
                          class="ckeditor-field @error('specification') is-invalid @enderror">{{ $old('specification', $product->specification ?? '') }}</textarea>
            </div>
            @error('specification') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- Size Chart --}}
    <div class="col-12">
        <div class="product-form-section">
            <div class="product-form-section-header">
                <span class="section-icon"><i class="fa-solid fa-ruler"></i></span>
                <div>
                    <h5>Size Chart</h5>
                    <div class="section-subtitle">Build a table or list of sizes and measurements</div>
                </div>
            </div>

            <div class="editor-field-wrap @error('size_chart') is-invalid @enderror">
                <textarea name="size_chart" id="size_chart" rows="6"
                          class="ckeditor-field @error('size_chart') is-invalid @enderror">{{ $old('size_chart', $product->size_chart ?? '') }}</textarea>
            </div>
            @error('size_chart') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
    </div>

    {{-- Visibility & Status --}}
    <div class="col-12">
        <div class="product-form-section">
            <div class="product-form-section-header">
                <span class="section-icon"><i class="fa-solid fa-eye"></i></span>
                <div>
                    <h5>Visibility &amp; Status</h5>
                    <div class="section-subtitle">Control where and how this product appears</div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="toggle-card">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1"
                               {{ $old('is_featured', $product ? $product->is_featured : true) ? 'checked' : '' }}>
                        <span>
                            <span class="toggle-card-title">Is Featured</span>
                            <span class="toggle-card-subtitle">Highlight in featured lists</span>
                        </span>
                    </label>
                </div>

                <div class="col-md-3">
                    <label class="toggle-card">
                        <input class="form-check-input" type="checkbox" name="is_show_frontend" id="is_show_frontend" value="1"
                               {{ $old('is_show_frontend', $product ? $product->is_show_frontend : true) ? 'checked' : '' }}>
                        <span>
                            <span class="toggle-card-title">Show on Frontend</span>
                            <span class="toggle-card-subtitle">Visible to store visitors</span>
                        </span>
                    </label>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        @foreach (['Active', 'Inactive'] as $status)
                            <option value="{{ $status }}" {{ $old('status', $product->status ?? 'Active') === $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                    @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    </div>

</div>
</div>

@push('styles')
<style>
    .option-pill-list { display: flex; flex-wrap: wrap; }
    .option-pill { border-radius: 999px; }
    .option-pill.active,
    .option-pill.btn-primary { box-shadow: 0 0 0 2px rgba(13,110,253,.15) inset; }
    .bulk-fill-panel { background: rgba(13,110,253,.04); }
    .bulk-fill-panel .form-control { min-width: 130px; }
    .attribute-options-table td, .attribute-options-table th { vertical-align: middle; }
    .no-selection-row td { border: none; }
</style>
@endpush

@push('scripts')
{{--
    CKEditor 5 is already loaded globally in resources/views/admin/layouts/app.blade.php,
    so it is NOT re-loaded here — only the initialisation logic lives in this partial.
--}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // CKEditor initialization
    if (typeof ClassicEditor !== 'undefined') {
        var fullToolbarConfig = {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'underline', '|',
                'bulletedList', 'numberedList', '|',
                'outdent', 'indent', '|',
                'blockQuote', 'insertTable', 'link', '|',
                'undo', 'redo'
            ]
        };

        // Lighter toolbar for short, single-line-ish fields (e.g. Short Description)
        var lightToolbarConfig = {
            toolbar: ['bold', 'italic', 'link', '|', 'undo', 'redo']
        };

        document.querySelectorAll('.ckeditor-field').forEach(function (textarea) {
            var config = textarea.classList.contains('ckeditor-field-light') ? lightToolbarConfig : fullToolbarConfig;

            ClassicEditor.create(textarea, config)
                .then(function (editor) {
                    textarea.ckeditorInstance = editor;
                    editor.model.document.on('change:data', function () {
                        textarea.value = editor.getData();
                    });
                })
                .catch(function (error) {
                    console.error('CKEditor failed to initialize:', error);
                });
        });
    }

    // ============================================
    // ATTRIBUTE VARIANT SYSTEM
    // (1 attribute -> product_attribute_variants, 2 attributes -> combinations)
    //
    // Interaction model:
    //   - Attribute options render as clickable "pills", not checkboxes in a table.
    //   - Single attribute: clicking a pill (e.g. "Red") adds one row of input
    //     fields (SKU / Quantity / Add. Price / Image) for that option.
    //   - Two attributes: clicking a pill in attribute one and a pill in
    //     attribute two builds the cartesian product of everything currently
    //     selected in both groups (Red+XL, Blue+XL, Red+M, Blue+M, ...).
    //   - Each attribute/combination panel has its own "bulk fill" row:
    //     a "Select All" checkbox plus common SKU / Quantity / Add. Price
    //     fields. While "Select All" is checked, typing into any of those
    //     common fields (or toggling it on) pushes that same value into
    //     every generated row's matching field.
    // ============================================
    const wrapper = document.getElementById('attribute-tabs-wrapper');
    const placeholder = document.getElementById('attribute-tabs-placeholder');
    const quantityInput = document.querySelector('input[name="quantity"]');

    const galleryImages = @json($galleryImages);
    // ---------- Gallery image linking (shared: single + combination mode) ----------

    let currentGalleryList = (galleryImages || []).map(function (filename) {
        return { value: filename, url: "{{ asset('uploads/products') }}/" + filename };
    });

    function renderGalleryPickList(selectedValue) {
        if (!currentGalleryList.length) {
            return '<span class="text-muted small">Gallery images select korle ekhane dekhabe (auto-linkable).</span>';
        }
        return currentGalleryList.map(function (img) {
            const isSelected = selectedValue && selectedValue === img.value;
            return `<img src="${img.url}" class="gallery-pick-thumb ${isSelected ? 'selected' : ''}" data-value="${img.value}" title="Click to link">`;
        }).join('');
    }

    function refreshAllGalleryPickers() {
        document.querySelectorAll('.gallery-pick-list').forEach(function (list) {
            const hiddenInput = list.parentElement.querySelector('.gallery-image-input');
            if (!hiddenInput) return;

            const stillValid = currentGalleryList.some(img => img.value === hiddenInput.value);
            if (!stillValid) hiddenInput.value = '';

            list.innerHTML = renderGalleryPickList(hiddenInput.value);
        });
    }

    // gallery_images[] input-এ নতুন ফাইল সিলেক্ট হলে তালিকা রিফ্রেশ হবে
    (function () {
        const input = document.getElementById('gallery_images_input');
        if (!input) return;
        input.addEventListener('change', function () {
            if (this.files && this.files.length) {
                currentGalleryList = Array.from(this.files).map(function (file, index) {
                    return { value: 'NEW_' + index, url: URL.createObjectURL(file) };
                });
            } else {
                currentGalleryList = (galleryImages || []).map(function (filename) {
                    return { value: filename, url: "{{ asset('uploads/products') }}/" + filename };
                });
            }
            refreshAllGalleryPickers();
        });
    })();


    const existingOptionImages = @json($existingOptionImages);

    let pendingAttributeLoads = 0;
    const productForm = wrapper.closest('form');
    const submitBtn = productForm ? productForm.querySelector('[type="submit"]') : null;

    function setLoadingGate(isLoading) {
        if (submitBtn) {
            submitBtn.disabled = isLoading;
            submitBtn.dataset.originalText = submitBtn.dataset.originalText || submitBtn.innerHTML;
            submitBtn.innerHTML = isLoading
                ? '<i class="fa-solid fa-spinner fa-spin me-1"></i> Loading attribute options…'
                : submitBtn.dataset.originalText;
        }
    }

    if (productForm) {
        productForm.addEventListener('submit', function (e) {
            if (pendingAttributeLoads > 0) {
                e.preventDefault();
                alert('Attribute options are still loading — please wait a moment and try saving again.');
            }
        });
    }

    // Single-attribute mode data: { attributeId: { optionId: {...} } }
    const existingVariants = @json($existingVariantsData ?? []);

    // Two-attribute combination mode data: { optionOneId: { optionTwoId: {...} } }
    const existingCombinations = @json($existingCombinationsData ?? []);

    // uid -> { enableAll, skuInput, qtyInput, priceInput, getRows }
    const bulkPanelState = {};

    function getSelectedAttributes() {
        return Array.from(document.querySelectorAll('.attribute-toggle:checked')).map(cb => ({
            id: cb.value,
            name: cb.dataset.name,
        }));
    }

    function clearAttributeSection() {
        wrapper.innerHTML = '';
    }

    function rebuildAttributeSection() {
        const selected = getSelectedAttributes();
        clearAttributeSection();

        if (!selected.length) {
            wrapper.appendChild(placeholder);
            recalcQuantity();
            return;
        }

        if (selected.length === 1) {
            buildSingleAttributeGrid(selected[0]);
            return;
        }

        if (selected.length > 2) {
            alert(`Only two attributes can be combined for variant options. Using "${selected[0].name}" and "${selected[1].name}" — please uncheck the rest.`);
        }

        buildCombinationGrid(selected[0], selected[1]);
    }

    // ---------- Shared: bulk fill panel ----------

    function bulkFillPanelHtml(uid) {
        return `
            <div class="bulk-fill-panel border rounded-3 p-2 mb-3 d-flex flex-wrap align-items-end gap-3" data-bulk-uid="${uid}">
                <div class="form-check mb-0">
                    <input class="form-check-input bulk-enable-all" type="checkbox" id="bulk_enable_all_${uid}">
                    <label class="form-check-label small fw-medium" for="bulk_enable_all_${uid}">Select All</label>
                </div>
                <div>
                    <label class="form-label small mb-0">Common SKU</label>
                    <input type="text" class="form-control form-control-sm bulk-sku" placeholder="SKU">
                </div>
                <div>
                    <label class="form-label small mb-0">Common Quantity</label>
                    <input type="number" min="0" class="form-control form-control-sm bulk-quantity" placeholder="Qty">
                </div>
                <div>
                    <label class="form-label small mb-0">Common Add. Price</label>
                    <input type="number" step="0.01" min="0" class="form-control form-control-sm bulk-price" placeholder="0.00">
                </div>
                <div class="form-text mb-1">
                    Check "Select All" then fill any field above to apply that same value to every row below.
                </div>
            </div>
        `;
    }

    function setupBulkPanel(container, uid, getRowsFn) {
        const panel = container.querySelector(`.bulk-fill-panel[data-bulk-uid="${uid}"]`);
        if (!panel) return;

        const enableAll = panel.querySelector('.bulk-enable-all');
        const skuInput = panel.querySelector('.bulk-sku');
        const qtyInput = panel.querySelector('.bulk-quantity');
        const priceInput = panel.querySelector('.bulk-price');

        bulkPanelState[uid] = { enableAll, skuInput, qtyInput, priceInput, getRows: getRowsFn };

        function applyToAllRows() {
            if (!enableAll.checked) return;
            getRowsFn().forEach(row => applyBulkValuesToRow(row, skuInput.value, qtyInput.value, priceInput.value));
            recalcQuantity();
        }

        enableAll.addEventListener('change', applyToAllRows);
        skuInput.addEventListener('input', applyToAllRows);
        qtyInput.addEventListener('input', applyToAllRows);
        priceInput.addEventListener('input', applyToAllRows);
    }

    function applyBulkValuesToRow(row, sku, quantity, price) {
        const skuField = row.querySelector('.variant-sku-input');
        const qtyField = row.querySelector('.variant-quantity-input');
        const priceField = row.querySelector('.variant-price-input');
        if (skuField && sku !== '') skuField.value = sku;
        if (qtyField && quantity !== '') qtyField.value = quantity;
        if (priceField && price !== '') priceField.value = price;
    }

    function applyBulkIfActive(uid, row) {
        const state = bulkPanelState[uid];
        if (!state || !state.enableAll.checked) return;
        applyBulkValuesToRow(row, state.skuInput.value, state.qtyInput.value, state.priceInput.value);
    }

    // ---------- Single-attribute mode ----------

    function renderSingleOptionRowHtml(attributeId, option, isColorAttribute) {
        const existing = (existingVariants[attributeId] || {})[option.id] || null;
        const linkedImage = existingOptionImages[option.id] || '';

        const galleryCellHtml = isColorAttribute ? `
            <td class="gallery-link-cell">
                <div class="gallery-pick-list" data-option-id="${option.id}">
                    ${renderGalleryPickList(linkedImage)}
                </div>
                <input type="hidden" class="gallery-image-input"
                    name="variants[${attributeId}][${option.id}][gallery_image]"
                    value="${linkedImage}">
            </td>
        ` : '';

        return `
            <tr class="option-row" data-option-id="${option.id}">
                <td class="option-name-cell">
                    <input type="hidden" name="variants[${attributeId}][${option.id}][enabled]" value="1">
                    <span class="fw-medium">${option.name}</span>
                </td>
                <td class="option-color-cell">
                    ${option.color_code ? `<span class="color-dot" style="background-color:${option.color_code};"></span>` : '—'}
                </td>
                <td><input type="text" name="variants[${attributeId}][${option.id}][sku]" class="form-control form-control-sm variant-sku-input" value="${existing && existing.sku ? existing.sku : ''}" placeholder="SKU"></td>
                <td><input type="number" min="0" name="variants[${attributeId}][${option.id}][quantity]" class="form-control form-control-sm variant-quantity-input" value="${existing && existing.quantity != null ? existing.quantity : ''}" placeholder="Qty"></td>
                <td>
                    <div class="input-group input-group-sm" style="flex-wrap: nowrap;">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" min="0" name="variants[${attributeId}][${option.id}][additional_price]" class="form-control form-control-sm variant-price-input" value="${existing && existing.additional_price ? existing.additional_price : ''}" placeholder="0.00">
                    </div>
                </td>
                <td>
                    <input type="file" name="variants[${attributeId}][${option.id}][image]" class="form-control form-control-sm variant-image-input" accept="image/*">
                    <div class="variant-image-preview mt-1">
                        ${existing && existing.image ? `<div class="form-text small">Current: ${existing.image}</div>` : ''}
                    </div>
                </td>
                ${galleryCellHtml}
                <td><button type="button" class="btn btn-sm btn-outline-danger remove-option-row" data-option-id="${option.id}" title="Remove">&times;</button></td>
            </tr>
        `;
    }

    function buildSingleAttributeGrid(attr) {
        const isColorAttribute = attr.name.trim().toLowerCase() === 'color';
        const panel = document.createElement('div');
        panel.className = 'attribute-tab-panel active';
        panel.innerHTML = `
            <div class="attribute-tab-content">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold">${attr.name} Options</span>
                    <span class="text-muted small">Click an option to add its variant fields</span>
                </div>
                <div class="attribute-options-loading text-muted small">Loading options...</div>
            </div>
        `;
        wrapper.appendChild(panel);

        pendingAttributeLoads++;
        setLoadingGate(true);

        fetch(`{{ route('admin.get-attribute-options') }}?attribute_id=${attr.id}`)
            .then(res => { if (!res.ok) throw new Error('Network response was not ok'); return res.json(); })
            .then(options => {
                const container = panel.querySelector('.attribute-tab-content');

                if (!options.length) {
                    container.innerHTML = `
                        <div class="attribute-tab-empty">
                            <i class="fa-regular fa-face-frown"></i>
                            <p>No options found for this attribute.</p>
                            <small class="text-muted">Please add options to this attribute first.</small>
                        </div>
                    `;
                    return;
                }

                const uid = `single_${attr.id}`;

                container.innerHTML = `
                    <div class="option-pill-list mb-3" data-role="pill-list"></div>
                    ${bulkFillPanelHtml(uid)}
                    <div class="table-responsive">
                        <table class="attribute-options-table">
                            <thead>
                                <tr>
                                    <th class="option-name-cell">Option</th>
                                    <th>Color</th>
                                    <th>SKU</th>
                                    <th>Quantity</th>
                                    <th>Add. Price</th>
                                    <th>Image</th>
                                    ${isColorAttribute ? '<th>Link Gallery Image</th>' : ''}
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody data-role="rows-body">
                                <tr class="no-selection-row">
                                    <td colspan="${isColorAttribute ? 8 : 7}" class="text-muted small text-center py-3">
                                        Click an option above to configure it.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-text mt-2">
                        <i class="fa-regular fa-circle-info me-1"></i>
                        Only options you click become active variants. Quantity is auto-summed from all rows below.
                    </div>
                `;

                const pillList = container.querySelector('[data-role="pill-list"]');
                const rowsBody = container.querySelector('[data-role="rows-body"]');
                const selected = new Set();

                function toggleNoSelectionRow() {
                    const noSel = rowsBody.querySelector('.no-selection-row');
                    if (selected.size && noSel) noSel.remove();
                    if (!selected.size && !rowsBody.querySelector('.no-selection-row')) {
                        rowsBody.innerHTML = `
                            <tr class="no-selection-row">
                                <td colspan="${isColorAttribute ? 8 : 7}" class="text-muted small text-center py-3">
                                    Click an option above to configure it.
                                </td>
                            </tr>
                        `;
                    }
                }

                function addRow(option) {
                    if (rowsBody.querySelector(`tr[data-option-id="${option.id}"]`)) return;
                    const tmpl = document.createElement('template');
                    tmpl.innerHTML = renderSingleOptionRowHtml(attr.id, option, isColorAttribute).trim();
                    const row = tmpl.content.firstChild;
                    rowsBody.appendChild(row);
                    applyBulkIfActive(uid, row);
                }

                function removeRow(optionId) {
                    const row = rowsBody.querySelector(`tr[data-option-id="${optionId}"]`);
                    if (row) row.remove();
                }

                options.forEach(option => {
                    const pill = document.createElement('button');
                    pill.type = 'button';
                    pill.className = 'option-pill btn btn-sm btn-outline-secondary me-1 mb-1';
                    pill.dataset.optionId = option.id;
                    pill.textContent = option.name;
                    if (option.color_code) pill.style.borderLeft = `4px solid ${option.color_code}`;

                    pill.addEventListener('click', function () {
                        const id = String(option.id);
                        if (selected.has(id)) {
                            selected.delete(id);
                            pill.classList.remove('btn-primary', 'active');
                            pill.classList.add('btn-outline-secondary');
                            removeRow(option.id);
                        } else {
                            selected.add(id);
                            pill.classList.remove('btn-outline-secondary');
                            pill.classList.add('btn-primary', 'active');
                            addRow(option);
                        }
                        toggleNoSelectionRow();
                        recalcQuantity();
                    });

                    pillList.appendChild(pill);
                });

                // Pre-select already-saved options (edit mode)
                const existingForAttr = existingVariants[attr.id] || {};
                Object.keys(existingForAttr).forEach(optId => {
                    const pill = pillList.querySelector(`.option-pill[data-option-id="${optId}"]`);
                    if (pill) pill.click();
                });

                setupBulkPanel(container, uid, () => rowsBody.querySelectorAll('tr[data-option-id]'));
                recalcQuantity();
            })
            .catch(error => {
                console.error('Error loading options:', error);
                panel.querySelector('.attribute-tab-content').innerHTML = `
                    <div class="admin-alert admin-alert-danger">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        Failed to load options. Please refresh and try again.
                    </div>
                `;
            })
            .finally(() => {
                pendingAttributeLoads--;
                if (pendingAttributeLoads <= 0) { pendingAttributeLoads = 0; setLoadingGate(false); }
            });
    }

    // ---------- Two-attribute combination mode ----------

    function renderCombinationRowHtml(optOne, optTwo, colorSide) {
        const existing = (existingCombinations[optOne.id] || {})[optTwo.id] || null;

        const colorOption = colorSide === 'one' ? optOne : (colorSide === 'two' ? optTwo : null);
        const fieldName = colorSide === 'one' ? 'gallery_image_one' : 'gallery_image_two';
        const linkedImage = colorOption ? (existingOptionImages[colorOption.id] || '') : '';

        const galleryCellHtml = colorSide ? `
            <td class="gallery-link-cell">
                <div class="gallery-pick-list" data-option-id="${colorOption.id}">
                    ${renderGalleryPickList(linkedImage)}
                </div>
                <input type="hidden" class="gallery-image-input"
                    name="combinations[${optOne.id}][${optTwo.id}][${fieldName}]"
                    value="${linkedImage}">
            </td>
        ` : '';

        return `
            <tr class="option-row" data-option-one-id="${optOne.id}" data-option-two-id="${optTwo.id}">
                <td>
                    <input type="hidden" name="combinations[${optOne.id}][${optTwo.id}][enabled]" value="1">
                    <span class="fw-medium">${optOne.name}</span>
                </td>
                <td><span class="fw-medium">${optTwo.name}</span></td>
                <td>
                    <input type="text" name="combinations[${optOne.id}][${optTwo.id}][sku]"
                        class="form-control form-control-sm variant-sku-input"
                        value="${existing && existing.sku ? existing.sku : ''}" placeholder="SKU">
                </td>
                <td>
                    <input type="number" min="0" name="combinations[${optOne.id}][${optTwo.id}][quantity]"
                        class="form-control form-control-sm variant-quantity-input"
                        value="${existing && existing.quantity != null ? existing.quantity : ''}"
                        placeholder="Qty">
                </td>
                <td>
                    <div class="input-group input-group-sm" style="flex-wrap: nowrap;">
                        <span class="input-group-text" style="flex-shrink:0;">$</span>
                        <input type="number" step="0.01" min="0"
                            name="combinations[${optOne.id}][${optTwo.id}][additional_price]"
                            class="form-control form-control-sm variant-price-input"
                            value="${existing && existing.additional_price ? existing.additional_price : ''}"
                            placeholder="0.00" style="flex:1 1 auto;">
                    </div>
                </td>
                <td>
                    <input type="file" name="combinations[${optOne.id}][${optTwo.id}][image]"
                        class="form-control form-control-sm variant-image-input" accept="image/*">
                    <div class="variant-image-preview mt-1">
                        ${existing && existing.image ? `<div class="form-text small">Current: ${existing.image}</div>` : ''}
                    </div>
                </td>
                ${galleryCellHtml}
            </tr>
        `;
    }

    function buildCombinationGrid(attrOne, attrTwo) {
        const colorSide = attrOne.name.trim().toLowerCase() === 'color' ? 'one'
            : (attrTwo.name.trim().toLowerCase() === 'color' ? 'two' : null);
        const panel = document.createElement('div');
        panel.className = 'attribute-tab-panel active';
        panel.innerHTML = `
            <div class="attribute-tab-content">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold">${attrOne.name} + ${attrTwo.name} Combinations</span>
                    <span class="text-muted small">Click options from both attributes to build combinations</span>
                </div>
                <div class="attribute-options-loading text-muted small">Loading combinations...</div>
            </div>
        `;
        wrapper.appendChild(panel);

        pendingAttributeLoads++;
        setLoadingGate(true);

        Promise.all([
            fetch(`{{ route('admin.get-attribute-options') }}?attribute_id=${attrOne.id}`).then(r => r.json()),
            fetch(`{{ route('admin.get-attribute-options') }}?attribute_id=${attrTwo.id}`).then(r => r.json()),
        ])
            .then(([optionsOne, optionsTwo]) => {
                const container = panel.querySelector('.attribute-tab-content');

                if (!optionsOne.length || !optionsTwo.length) {
                    container.innerHTML = `
                        <div class="attribute-tab-empty">
                            <i class="fa-regular fa-face-frown"></i>
                            <p>One of the selected attributes has no options.</p>
                            <small class="text-muted">Please add options to both attributes first.</small>
                        </div>
                    `;
                    return;
                }

                const uid = `combo_${attrOne.id}_${attrTwo.id}`;

                container.innerHTML = `
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="fw-medium small text-muted mb-1">${attrOne.name}</div>
                            <div class="option-pill-list" data-role="pill-list-one"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-medium small text-muted mb-1">${attrTwo.name}</div>
                            <div class="option-pill-list" data-role="pill-list-two"></div>
                        </div>
                    </div>
                    ${bulkFillPanelHtml(uid)}
                    <div class="table-responsive">
                        <table class="attribute-options-table">
                            <thead>
                                <tr>
                                    <th>${attrOne.name}</th>
                                    <th>${attrTwo.name}</th>
                                    <th>SKU</th>
                                    <th>Quantity</th>
                                    <th>Add. Price</th>
                                    <th>Image</th>
                                    ${colorSide ? '<th>Link Gallery Image</th>' : ''}
                                </tr>
                            </thead>
                            <tbody data-role="rows-body">
                                <tr class="no-selection-row">
                                    <td colspan="${colorSide ? 7 : 6}" class="text-muted small text-center py-3">
                                        Click options from both attributes above to build combinations.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-text mt-2">
                        <i class="fa-regular fa-circle-info me-1"></i>
                        Every combination of your clicked options (e.g. Red+XL, Blue+XL) appears below automatically.
                    </div>
                `;

                const pillListOne = container.querySelector('[data-role="pill-list-one"]');
                const pillListTwo = container.querySelector('[data-role="pill-list-two"]');
                const rowsBody = container.querySelector('[data-role="rows-body"]');
                const selectedOne = new Set();
                const selectedTwo = new Set();

                function toggleNoSelectionRow() {
                    const noSel = rowsBody.querySelector('.no-selection-row');
                    const hasRows = rowsBody.querySelector('tr[data-option-one-id]');
                    if (hasRows && noSel) noSel.remove();
                    if (!hasRows && !rowsBody.querySelector('.no-selection-row')) {
                        rowsBody.innerHTML = `
                            <tr class="no-selection-row">
                                <td colspan="${colorSide ? 7 : 6}" class="text-muted small text-center py-3">
                                    Click options from both attributes above to build combinations.
                                </td>
                            </tr>
                        `;
                    }
                }

                function addComboRow(optOne, optTwo) {
                    if (rowsBody.querySelector(`tr[data-option-one-id="${optOne.id}"][data-option-two-id="${optTwo.id}"]`)) return;
                    const tmpl = document.createElement('template');
                    tmpl.innerHTML = renderCombinationRowHtml(optOne, optTwo, colorSide).trim();
                    const row = tmpl.content.firstChild;
                    rowsBody.appendChild(row);
                    applyBulkIfActive(uid, row);
                }

                function removeComboRowsByOne(optionOneId) {
                    rowsBody.querySelectorAll(`tr[data-option-one-id="${optionOneId}"]`).forEach(r => r.remove());
                }

                function removeComboRowsByTwo(optionTwoId) {
                    rowsBody.querySelectorAll(`tr[data-option-two-id="${optionTwoId}"]`).forEach(r => r.remove());
                }

                function makePill(option, group) {
                    const pill = document.createElement('button');
                    pill.type = 'button';
                    pill.className = 'option-pill btn btn-sm btn-outline-secondary me-1 mb-1';
                    pill.dataset.optionId = option.id;
                    pill.textContent = option.name;
                    if (option.color_code) pill.style.borderLeft = `4px solid ${option.color_code}`;

                    pill.addEventListener('click', function () {
                        const set = group === 'one' ? selectedOne : selectedTwo;
                        const otherSet = group === 'one' ? selectedTwo : selectedOne;
                        const id = String(option.id);
                        const isSelected = set.has(id);

                        if (isSelected) {
                            set.delete(id);
                            pill.classList.remove('btn-primary', 'active');
                            pill.classList.add('btn-outline-secondary');
                            if (group === 'one') removeComboRowsByOne(option.id);
                            else removeComboRowsByTwo(option.id);
                        } else {
                            set.add(id);
                            pill.classList.remove('btn-outline-secondary');
                            pill.classList.add('btn-primary', 'active');
                            otherSet.forEach(otherId => {
                                const otherOption = group === 'one'
                                    ? optionsTwo.find(o => String(o.id) === otherId)
                                    : optionsOne.find(o => String(o.id) === otherId);
                                if (!otherOption) return;
                                if (group === 'one') addComboRow(option, otherOption);
                                else addComboRow(otherOption, option);
                            });
                        }

                        toggleNoSelectionRow();
                        recalcQuantity();
                    });

                    return pill;
                }

                optionsOne.forEach(o => pillListOne.appendChild(makePill(o, 'one')));
                optionsTwo.forEach(o => pillListTwo.appendChild(makePill(o, 'two')));

                // Pre-select already-saved combinations (edit mode)
                Object.keys(existingCombinations).forEach(optOneId => {
                    Object.keys(existingCombinations[optOneId] || {}).forEach(optTwoId => {
                        const pillOne = pillListOne.querySelector(`.option-pill[data-option-id="${optOneId}"]`);
                        const pillTwo = pillListTwo.querySelector(`.option-pill[data-option-id="${optTwoId}"]`);
                        if (pillOne && !selectedOne.has(String(optOneId))) pillOne.click();
                        if (pillTwo && !selectedTwo.has(String(optTwoId))) pillTwo.click();
                    });
                });

                setupBulkPanel(container, uid, () => rowsBody.querySelectorAll('tr[data-option-one-id]'));
                recalcQuantity();
            })
            .catch(error => {
                console.error('Error loading combination options:', error);
                panel.querySelector('.attribute-tab-content').innerHTML = `
                    <div class="admin-alert admin-alert-danger">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        Failed to load attribute options. Please refresh and try again.
                    </div>
                `;
            })
            .finally(() => {
                pendingAttributeLoads--;
                if (pendingAttributeLoads <= 0) { pendingAttributeLoads = 0; setLoadingGate(false); }
            });
    }

    // ---------- Quantity auto-sum (works for both modes) ----------
    // Presence of a `.option-row` now means "this variant/combination is active",
    // since rows only exist for options the user actually clicked.

    function recalcQuantity() {
        const rows = wrapper.querySelectorAll('.option-row');
        if (!rows.length) {
            quantityInput.readOnly = false;
            quantityInput.classList.remove('bg-light');
            quantityInput.title = '';
            return;
        }

        let total = 0;
        let anyQuantitySet = false;

        rows.forEach(row => {
            const qtyInput = row.querySelector('.variant-quantity-input');
            if (qtyInput) {
                const val = qtyInput.value.trim();
                if (val !== '') {
                    anyQuantitySet = true;
                    total += parseInt(val, 10) || 0;
                }
            }
        });

        if (anyQuantitySet) {
            quantityInput.value = total;
            quantityInput.readOnly = true;
            quantityInput.classList.add('bg-light');
            quantityInput.title = 'Auto-calculated from attribute option quantities';
        } else {
            quantityInput.readOnly = false;
            quantityInput.classList.remove('bg-light');
            quantityInput.title = '';
        }
    }

    // Recalculate whenever any row's quantity field changes (rows are added dynamically,
    // so this is delegated on the wrapper rather than bound per-input).
    wrapper.addEventListener('input', function (e) {
        if (e.target.classList.contains('variant-quantity-input')) {
            recalcQuantity();
        }
    });

    // Remove-row button (single-attribute mode) just toggles the matching pill off,
    // so the pill's on/off state and the table stay in sync.
    wrapper.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('.remove-option-row');
        if (!removeBtn) return;
        const optionId = removeBtn.dataset.optionId;
        const pill = wrapper.querySelector(`.option-pill.active[data-option-id="${optionId}"]`);
        if (pill) pill.click();
    });

    // ---------- Wire up checkbox events ----------

    document.querySelectorAll('.attribute-toggle').forEach(cb => {
        cb.addEventListener('change', function () {
            this.closest('.form-check').classList.toggle('attribute-selected', this.checked);
            rebuildAttributeSection();
        });

        cb.closest('.form-check').classList.toggle('attribute-selected', cb.checked);
    });

    // Initial render (handles edit mode with 1 or 2 pre-checked attributes)
    rebuildAttributeSection();

    // If no tabs after page load, ensure placeholder is visible
    if (!wrapper.querySelector('.attribute-tab-panel') && placeholder) {
        wrapper.appendChild(placeholder);
    }

    // ============================================
    // LIVE MEDIA PREVIEWS
    // ============================================

    function previewSingleFile(input, imgEl, wrapEl, textEl, newFileLabel) {
        if (!input) return;
        input.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const url = URL.createObjectURL(this.files[0]);
                if (imgEl) imgEl.src = url;
                if (wrapEl) wrapEl.style.display = '';
                if (textEl) textEl.textContent = newFileLabel;
            }
        });
    }

    // Featured image
    previewSingleFile(
        document.getElementById('featured_image_input'),
        document.getElementById('featured_image_preview_img'),
        document.getElementById('featured_image_preview'),
        document.getElementById('featured_image_preview_text'),
        'New image selected (not yet saved).'
    );

    // Video
    (function () {
        const input = document.getElementById('video_input');
        const videoEl = document.getElementById('video_preview_el');
        const vwrap = document.getElementById('video_preview');
        const text = document.getElementById('video_preview_text');
        if (!input) return;
        input.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                const url = URL.createObjectURL(this.files[0]);
                if (videoEl) videoEl.src = url;
                if (vwrap) vwrap.style.display = '';
                if (text) text.textContent = 'New video selected (not yet saved).';
            }
        });
    })();

    // Gallery images (multiple)
    (function () {
        const input = document.getElementById('gallery_images_input');
        const previewGrid = document.getElementById('gallery-new-preview');
        if (!input || !previewGrid) return;
        input.addEventListener('change', function () {
            previewGrid.innerHTML = '';
            if (!this.files || !this.files.length) return;
            Array.from(this.files).forEach(function (file) {
                const url = URL.createObjectURL(file);
                const div = document.createElement('div');
                div.className = 'gallery-thumb gallery-thumb-new';
                div.innerHTML = `<img src="${url}" alt="${file.name}">`;
                previewGrid.appendChild(div);
            });
        });
    })();

    // Variant images — delegated because rows are added dynamically
    wrapper.addEventListener('change', function (e) {
        const input = e.target.closest('.variant-image-input');
        if (!input) return;
        const previewContainer = input.parentElement.querySelector('.variant-image-preview');
        if (!previewContainer) return;
        if (input.files && input.files[0]) {
            const url = URL.createObjectURL(input.files[0]);
            previewContainer.innerHTML = `<img src="${url}" class="rounded border variant-thumb" alt="New variant image" style="max-width:60px;max-height:60px;">`;
        }
    });

    wrapper.addEventListener('click', function (e) {
        const thumb = e.target.closest('.gallery-pick-thumb');
        if (!thumb) return;
        const list = thumb.closest('.gallery-pick-list');
        const hiddenInput = list.parentElement.querySelector('.gallery-image-input');
        const value = thumb.dataset.value;

        hiddenInput.value = (hiddenInput.value === value) ? '' : value;
        list.innerHTML = renderGalleryPickList(hiddenInput.value);
    });
});
</script>
@endpush