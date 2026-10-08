<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\HomeSlider;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $products = $this->products();
        $slides   = $this->slides();
        $reviews  = $this->reviews();

        $seo = [
            'title'       => 'Bethika | Ladies Fashion Boutique',
            'description' => 'Shop Bethika for elegant, comfortable ladies clothing crafted from soft, premium fabrics. Cash on delivery across Bangladesh.',
            'keywords'    => 'ladies clothing, womens fashion bangladesh, ladies t-shirt bd, bethika, womens clothing dhaka',
            'image'       => asset('assets/images/logo.png'),
        ];

        return view('frontend.index', compact('products', 'slides', 'reviews', 'seo'));
    }

    /**
     * Hero slider content — not backed by the products table, left as-is.
     */
    private function slides(): array
    {
        $sliders = HomeSlider::where('status', true)
            ->orderBy('serial', 'asc')
            ->get();

        // If there is any active video, only show the video
        // with the lowest serial number.
        $videoSlider = $sliders
            ->where('slider_type', 'video')
            ->first();

        if ($videoSlider) {
            return [
                [
                    'type'     => 'video',
                    'image'    => asset($videoSlider->file_path),
                    'video'    => asset($videoSlider->file_path),
                    'tagline'  => $videoSlider->name ?? '',
                    'title'    => '',
                    'subtitle' => '',
                    'cta'      => '',
                    'serial'   => $videoSlider->serial,
                ],
            ];
        }

        // Otherwise show all active images
        return $sliders
            ->where('slider_type', 'image')
            ->map(function ($slider) {
                return [
                    'type'     => 'image',
                    'image'    => asset($slider->file_path),
                    'video'    => null,
                    'tagline'  => $slider->name ?? '',
                    'title'    => '',
                    'subtitle' => '',
                    'cta'      => '',
                    'serial'   => $slider->serial,
                ];
            })
            ->values()
            ->toArray();
    }


    /**
     * Build the catalogue in the exact shape frontend.index.blade.php's JS expects.
     */
    private function products(): array
    {
        $colorHexMap = [
            'black'     => '#1C1C1C',
            'deep blue' => '#1B3A6B',
            'pink'      => '#F4B8C4',
            'red'       => '#D1373F',
            'yellow'    => '#fbff00',
            'white'     => '#FFFFFF',
        ];

        $products = Product::with('category')
            ->where('status', 'Active')
            ->where('is_show_frontend', 1)
            ->get();

        return $products->map(function (Product $product) use ($colorHexMap) {
            [$sizes, $colors, $sizeLabel, $colorLabel] = $this->optionsFor($product->id, $colorHexMap);

            // specification is stored as JSON-encoded HTML string; pull <li> text out of it.
            $details = [];
            $spec = $product->getRawOriginal('specification');
            $spec = is_string($spec) ? (json_decode($spec) ?? $spec) : $spec;
            if (is_string($spec)) {
                preg_match_all('/<li>(.*?)<\/li>/is', $spec, $matches);
                foreach ($matches[1] ?? [] as $item) {
                    $details[] = trim(strip_tags($item));
                }
            }

            return [
                'id'          => $product->id,
                'name'        => $product->name,
                'category'    => $product->category->name ?? '',
                'price'       => (float) $product->price,
                'old_price'   => $product->previous_price ? (float) $product->previous_price : null,
                'rating'      => 0,
                'image'       => $product->featured_image
                                    ? asset('uploads/products/' . $product->featured_image)
                                    : asset('assets/images/placeholder.png'),
                'gallery'     => $this->galleryFor($product),
                'sizes'       => $sizes ?: ['One Size'],
                'size_label'  => $sizeLabel ?: 'Size',
                // FIX: Only provide colors if there are actual colors, otherwise use empty array
                'colors'      => !empty($colors) ? $colors : [],
                'color_label' => $colorLabel ?: 'Colour',
                'description' => strip_tags($product->short_description ?: $product->description),
                'details'     => $details,
            ];
        })->all();
    }

    /**
     * Resolve a product's gallery image URLs.
     * Assumes a `gallery_images` column holding a JSON array of filenames stored
     * alongside `featured_image` under public/uploads/products. Update the column
     * name / path here if your schema differs.
     */
    private function galleryFor(Product $product): array
    {
        $raw = $product->gallery_images ?? [];

        if (is_string($raw)) {
            $raw = json_decode($raw, true) ?: [];
        }

        return collect($raw)
            ->filter()
            ->map(fn ($file) => asset('uploads/products/' . ltrim($file, '/')))
            ->values()
            ->all();
    }

    /**
     * Site-wide testimonials from the `reviews` table (Review model has no
     * product relation, so these are shown as general "what parents say"
     * testimonials rather than per-product reviews).
     */
    private function reviews(): array
    {
        return Review::active()
            ->latest()
            ->get()
            ->map(function (Review $review) {
                return [
                    'name'        => $review->name,
                    'designation' => $review->designation,
                    // Assumes review photos are stored under public/uploads/reviews.
                    // Update the path below if your schema stores them elsewhere.
                    'photo'       => $review->photo ? asset('uploads/reviews/' . $review->photo) : null,
                    'message'     => strip_tags($review->message),
                    'rating'      => max(0, min(5, (int) $review->rating_point)),
                    'date'        => optional($review->created_at)->format('M d, Y'),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Pull distinct size/color options for a product — plus the *actual*
     * attribute name configured in admin (product_attributes.name) so the
     * detail page always labels each option group correctly, instead of a
     * hardcoded "Age / Size" / "Colour" string.
     *
     * Queried via DB facade (not Eloquent relations) since those models
     * weren't shared — safe regardless of their internal method names.
     */
    private function optionsFor(int $productId, array $colorHexMap): array
    {
        $sizes = collect();
        $colors = collect();
        $sizeLabel = null;
        $colorLabel = null;

        // Single-attribute variants
        DB::table('product_attribute_variants as v')
            ->join('product_attributes as a', 'a.id', '=', 'v.product_attribute_id')
            ->join('product_attribute_options as o', 'o.id', '=', 'v.product_attribute_option_id')
            ->where('v.product_id', $productId)
            ->select('a.slug', 'a.name as attribute_name', 'o.id as option_id', 'o.name as option_name')
            ->get()
            ->each(function ($row) use ($sizes, $colors, &$sizeLabel, &$colorLabel) {
                if (!$row->slug || !$row->option_id || !$row->option_name) {
                    return;
                }
                if (strtolower($row->slug) === 'size') {
                    $sizes->put($row->option_id, $row->option_name);
                    $sizeLabel = $sizeLabel ?? $row->attribute_name;
                } elseif (strtolower($row->slug) === 'color') {
                    $colors->put($row->option_id, $row->option_name);
                    $colorLabel = $colorLabel ?? $row->attribute_name;
                }
            });

        // Two-attribute combinations
        DB::table('product_attribute_variant_combinations as c')
            ->join('product_attributes as a1', 'a1.id', '=', 'c.product_attribute_one_id')
            ->join('product_attribute_options as o1', 'o1.id', '=', 'c.product_option_attribute_one_id')
            ->join('product_attributes as a2', 'a2.id', '=', 'c.product_attribute_two_id')
            ->join('product_attribute_options as o2', 'o2.id', '=', 'c.product_attribute_option_two_id')
            ->where('c.product_id', $productId)
            ->select(
                'a1.slug as slug1', 'a1.name as attribute_name1', 'o1.id as option1_id', 'o1.name as option1_name',
                'a2.slug as slug2', 'a2.name as attribute_name2', 'o2.id as option2_id', 'o2.name as option2_name'
            )
            ->get()
            ->each(function ($row) use ($sizes, $colors, &$sizeLabel, &$colorLabel) {
                $pairs = [
                    [$row->slug1, $row->attribute_name1, $row->option1_id, $row->option1_name],
                    [$row->slug2, $row->attribute_name2, $row->option2_id, $row->option2_name],
                ];
                foreach ($pairs as [$slug, $attrName, $optionId, $optionName]) {
                    if (!$slug || !$optionId || !$optionName) {
                        continue;
                    }
                    if (strtolower($slug) === 'size') {
                        $sizes->put($optionId, $optionName);
                        $sizeLabel = $sizeLabel ?? $attrName;
                    } elseif (strtolower($slug) === 'color') {
                        $colors->put($optionId, $optionName);
                        $colorLabel = $colorLabel ?? $attrName;
                    }
                }
            });

        $sizeList = $sizes->values()->all();

        $colorList = [];
        if ($colors->isNotEmpty()) {
            // প্রতিটা color option-এর সাথে লিংক করা gallery image filename টেনে আনি
            $optionImages = DB::table('product_attribute_option_images')
                ->where('product_id', $productId)
                ->whereIn('product_attribute_option_id', $colors->keys())
                ->pluck('image_path', 'product_attribute_option_id');

            $colorList = $colors->map(function ($name, $optionId) use ($colorHexMap, $optionImages) {
                $imagePath = $optionImages->get($optionId);

                return [
                    'name'  => $name,
                    'hex'   => $colorHexMap[strtolower($name)] ?? '#FFFFFF',
                    'image' => $imagePath ? asset('uploads/products/' . $imagePath) : null,
                ];
            })->values()->all();
        }

        return [$sizeList, $colorList, $sizeLabel, $colorLabel];
    }
}