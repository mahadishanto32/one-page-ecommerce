<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- ============ 1) SEO ============
     $seo is passed from HomeController@index. Every key has a fallback so the
     page never renders empty tags even if $seo isn't set. --}}
@php
    $seoTitle       = $seo['title'] ?? '';
    $seoDescription = $seo['description'] ?? '';
    $seoKeywords    = $seo['keywords'] ?? '';
    $seoImage       = $seo['image'] ?? asset('assets/images/logo.png');
@endphp
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="keywords" content="{{ $seoKeywords }}">
<meta name="robots" content="index, follow">
<meta name="author" content="Bethika">
<link rel="canonical" href="{{ url()->current() }}">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="Bethika">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">

<link rel="icon" type="image/png" href="{{ $settings?->favicon ? asset('uploads/settings/'.$settings->favicon) : asset('assets/images/favicon.jpeg') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

</head>
<body>

<header class="site-header" id="siteHeader">
    <div class="container nav-inner">
        <a href="#home" class="logo">
            <img src="{{ $settings?->logo ? asset('uploads/settings/'.$settings->logo) : asset('assets/images/logo.png') }}" alt="{{ $settings?->app_name ?? 'Bethika' }} logo"> 
        </a>
        <nav class="nav-links">
            <a href="#home">Home</a>
            <a href="#shop">Shop</a>
            <a href="#detail">Details</a>
            <a href="#reviews">Reviews</a>
        </nav>

        {{-- ============ 5) Cart icon + count + hover dropdown ============ --}}
        <div class="cart-wrapper" id="cartWrapper">
            <a href="#shop" class="cart-btn" title="View cart" id="cartBtn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"/>
                    <circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <span class="cart-count" id="cartCount">0</span>
            </a>
            <div class="cart-dropdown" id="cartDropdown">
                <div class="cart-dropdown-empty" id="cartEmpty">Your cart is empty.</div>
                <div id="cartItemsList"></div>
            </div>
        </div>
    </div>
</header>

<!-- ============ SECTION 1: HERO SLIDER ============ -->
<section id="home" class="hero-slider">
    <div class="froggy-badge">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 3l1.8 4.6L18 9l-4.2 1.4L12 15l-1.8-4.6L6 9l4.2-1.4L12 3z" fill="#dca225" stroke="none"/>
            <path d="M19 14l.8 2 2 .8-2 .8-.8 2-.8-2-2-.8 2-.8.8-2z" fill="#f9abdf" stroke="none"/>
        </svg>
        New Season, New You
    </div>
    @foreach ($slides as $index => $slide)
    <div class="slide {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}">

        @if ($slide['type'] === 'video')
            <video
                class="hero-slide-video"
                width="1920"
                height="1080"
                autoplay
                muted
                loop
                playsinline
                preload="auto"
            >
                <source src="{{ $slide['video'] }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        @else
            <img
                src="{{ $slide['image'] }}"
                alt="{{ $slide['title'] ?? $slide['tagline'] ?? 'Home slider' }}"
            >
        @endif

        <div class="slide-content">
            <p class="eyebrow">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke="#dca225" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <polyline points="20 12 4 12"/>
                    <polyline points="10 18 4 12 10 6"/>
                </svg>

                {{ $slide['tagline'] ?? '' }}
            </p>

            @if (!empty($slide['title']))
                <h1>{{ $slide['title'] }}</h1>
            @endif

            @if (!empty($slide['subtitle']))
                <p>{{ $slide['subtitle'] }}</p>
            @endif

            @if (!empty($slide['cta']))
                <a href="#shop" class="btn btn-primary">
                    {{ $slide['cta'] }}

                    <svg width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="5 12 19 12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
            @endif
        </div>
    </div>
    @endforeach


    <div class="slider-arrow prev" onclick="changeSlide(-1)">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
    </div>
    <div class="slider-arrow next" onclick="changeSlide(1)">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 6 15 12 9 18"/>
        </svg>
    </div>
    <div class="slider-nav">
        @foreach ($slides as $index => $slide)
            <div class="slider-dot {{ $index === 0 ? 'active' : '' }}" onclick="goToSlide({{ $index }})"></div>
        @endforeach
    </div>
</section>

<!-- ============ SECTION 2: PRODUCTS ============ -->
<section id="shop" class="products-section">
    <div class="container">
        <div class="section-deco">
            <span></span><span></span><span></span>
        </div>
        <div class="section-heading">
            <p class="eyebrow">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#376a6d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M12 7V3"/>
                    <path d="M12 21v-4"/>
                    <path d="M7 12H3"/>
                    <path d="M21 12h-4"/>
                    <path d="M8.5 8.5L5.5 5.5"/>
                    <path d="M18.5 18.5l-3-3"/>
                    <path d="M5.5 18.5l3-3"/>
                    <path d="M18.5 5.5l-3 3"/>
                </svg>
                The Bethika Collection
            </p>
            <h2>Styles Made For Every <span class="highlight">Woman</span></h2>
            <p>Soft, flattering fabrics finished with boutique care — click any piece to see full details, sizing and reviews.</p>
        </div>

        <div class="product-grid" id="productGrid">
            @foreach ($products as $product)
                <div class="product-card reveal" id="card-{{ $product['id'] }}" onclick="showProduct({{ $product['id'] }})">
                    <div class="product-thumb">
                        @if(!empty($product['old_price']))
                            <span class="badge-sale">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                SALE
                            </span>
                        @endif
                        <span class="badge-age">{{ $product['sizes'][0] }}&ndash;{{ end($product['sizes']) }}</span>
                        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}">
                        <div class="product-overlay">
                            <span>View Details</span>
                        </div>
                    </div>
                    <div class="product-info">
                        <p class="cat">{{ $product['category'] }}</p>
                        <h3>{{ $product['name'] }}</h3>
                        <div class="price-row">
                            <span class="price-now">৳{{ number_format($product['price']) }}</span>
                            @if(!empty($product['old_price']))
                                <span class="price-old">৳{{ number_format($product['old_price']) }}</span>
                            @endif
                        </div>
                        <div class="product-rating">
                            <span class="stars" style="font-size:.75rem;">
                                @php
                                    $full = round($product['rating']);
                                    echo str_repeat('★', $full) . str_repeat('☆', 5 - $full);
                                @endphp
                            </span>
                            <span class="rating-count">{{ $product['rating'] }}</span>
                        </div>
                        <button type="button" class="view-btn">View Details</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ============ SECTION 3: PRODUCT DETAIL ============ -->
<section id="detail" class="detail-section">
    <div class="container">
        <div class="detail-grid">
            <div class="detail-image">
                <div class="detail-image-main">
                    <img id="detImage" src="" alt="">
                </div>
                <div class="detail-gallery" id="detGallery"></div>
            </div>
            <div class="detail-info">
                <p class="cat" id="detCategory"></p>
                <h2 id="detName"></h2>
                <div class="rating-row">
                    <span class="stars" id="detStars"></span>
                    <span id="detRatingText"></span>
                </div>
                <div class="detail-price">
                    <span class="now" id="detPrice"></span>
                    <span class="old" id="detOldPrice"></span>
                </div>
                <p class="detail-desc" id="detDesc"></p>
                <ul class="detail-list" id="detFeatures"></ul>

                <div class="option-block">
                    <span class="option-label" id="detSizeLabel">Size</span>
                    <div class="size-options" id="detSizes"></div>
                </div>
                <div class="option-block" id="detColorBlock">
                    <span class="option-label" id="detColorLabel">Colour</span>
                    <div class="color-options" id="detColors"></div>
                </div>
                <div class="option-block">
                    <span class="option-label">Quantity</span>
                    <div class="qty-row">
                        <div class="qty-control">
                            <button type="button" onclick="changeQty(-1)">&minus;</button>
                            <span id="detQty">1</span>
                            <button type="button" onclick="changeQty(1)">+</button>
                        </div>
                    </div>
                </div>

                <div class="detail-actions">
                    <button type="button" class="btn btn-primary" onclick="openCheckout()">
                        Buy Now
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="5 12 19 12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </button>

                    {{-- ============ 5) Add to Cart button ============ --}}
                    <button type="button" class="btn btn-outline" onclick="addToCart()">
                        Add to Cart
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"/>
                            <circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                        </svg>
                    </button>

                    <a href="#reviews" class="btn btn-outline">See Reviews</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ SECTION 4: REVIEWS ============ -->
<section id="reviews" class="reviews-section">
    <div class="container">
        <div class="reviews-head">
            <div>
                <p class="eyebrow">What Our Customers Say</p>
                <h2 id="reviewsTitle">Loved By Our Customers</h2>
            </div>
            @if(count($reviews))
                @php
                    $avg = collect($reviews)->avg('rating');
                    $avgFull = (int) round($avg);
                @endphp
                <div class="reviews-summary">
                    <span class="avg-score">{{ number_format($avg, 1) }}</span>
                    <div>
                        <div class="stars">{{ str_repeat('★', $avgFull) . str_repeat('☆', 5 - $avgFull) }}</div>
                        <span style="font-size:.8rem;color:var(--clr-muted);font-weight:700;">{{ count($reviews) }} verified reviews</span>
                    </div>
                </div>
            @endif
        </div>
        <div class="review-grid">
            @forelse ($reviews as $review)
                <div class="review-card reveal">
                    <div class="review-top">
                        <div class="reviewer-info">
                            @if(!empty($review['photo']))
                                <img src="{{ $review['photo'] }}" alt="{{ $review['name'] }}" class="reviewer-avatar">
                            @endif
                            <div class="reviewer-meta">
                                <span class="reviewer">{{ $review['name'] }}</span>
                                @if(!empty($review['designation']))
                                    <span class="reviewer-role">{{ $review['designation'] }}</span>
                                @endif
                            </div>
                        </div>
                        <span class="review-date">{{ $review['date'] }}</span>
                    </div>
                    <div class="stars" style="margin-bottom:8px;">
                        {{ str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) }}
                    </div>
                    <p>{{ $review['message'] }}</p>
                </div>
            @empty
                <div class="empty-hint">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#376a6d" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10" fill="#eaf5f3" stroke="none"/>
                        <path d="M12 6l1.6 4 4.2.3-3.3 2.7 1.1 4.1L12 14.7 8.4 17.1l1.1-4.1-3.3-2.7 4.2-.3L12 6z" fill="#dca225" stroke="none"/>
                    </svg>
                    No reviews yet — check back soon.
                </div>
            @endforelse
        </div>
    </div>
</section>

<footer>
    <div class="container">
        <div class="footer-inner footer-inner-4col">
            <!-- Column 1: Logo + description -->
            <div class="footer-col footer-col-brand">
                <a href="#home" class="logo">
                    <img src="{{ $settings?->logo ? asset('uploads/settings/' . $settings->logo) : asset('assets/images/logo.png') }}" alt="{{ $settings?->app_name ?? 'Bethika' }} logo">
                </a>
                <p class="footer-note">Elegant, everyday ladies fashion made with soft, breathable fabrics designed for comfort and grace.</p>
                <div class="footer-socials">
                    @if($settings?->facebook_link)
                        <a href="{{ $settings->facebook_link }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    @endif
                    @if($settings?->instagram_link)
                        <a href="{{ $settings->instagram_link }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    @endif
                    @if($settings?->youtube_link)
                        <a href="{{ $settings->youtube_link }}" target="_blank" rel="noopener" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                    @endif
                    @if($settings?->x_id)
                        <a href="{{ $settings->x_id }}" target="_blank" rel="noopener" aria-label="X"><i class="bi bi-twitter-x"></i></a>
                    @endif
                </div>
            </div>

            <!-- Column 2: Contact -->
            <div class="footer-col">
                <p class="footer-heading">Contact</p>
                <p class="footer-note">
                    {{ $settings?->address }}
                    @if($settings?->alternative_address)
                        <br>{{ $settings->alternative_address }}
                    @endif
                    <br>{{ $settings?->email }} · {{ $settings?->phone_number }}
                </p>
            </div>

            <!-- Column 3: Map + social icons -->
            <div class="footer-col">
                <p class="footer-heading">Find Us</p>
                <div class="map-embed">
                    {!! $settings?->google_map_link !!}
                </div>
                
            </div>

            <!-- Column 4: Our Other Brands -->
            <div class="footer-col">
                <p class="footer-heading">Our Other Brands</p>
                <div class="footer-brands">
                    <a href="https://cods.com.bd" target="_blank" rel="noopener" title="Cods">
                        <img src="{{ asset('assets/images/brands/cods.png') }}" alt="Cods">
                    </a>
                    <a href="https://speedycloth.com" target="_blank" rel="noopener" title="Speedy Cloth">
                        <img src="{{ asset('assets/images/brands/speedy.png') }}" alt="Speedy Cloth">
                    </a>
                    <a href="https://froggykidz.com" target="_blank" rel="noopener" title="Froggy Kidz">
                        <img src="{{ asset('assets/images/brands/froggy.png') }}" alt="Froggy Kidz">
                    </a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">&copy; {{ date('Y') }} {{ $settings?->app_name ?? 'Bethika' }}. All rights reserved.</div>
    </div>
</footer>

<!-- ============ CHECKOUT MODAL ============ -->
<div class="modal-overlay" id="checkoutModal">
    <div class="modal-box" id="modalBox">
        <button class="modal-close" onclick="closeCheckout()">&times;</button>

        <div id="checkoutForm">
            <h3>Checkout</h3>
            <p class="modal-sub">Fill in your delivery details — cash on delivery &amp; mobile banking supported.</p>

            <div class="order-summary-list" id="coItemsList">
                <img id="coImage" src="" alt="">
                <div>
                    <div class="os-name" id="coName"></div>
                    <div class="os-meta" id="coMeta"></div>

                    {{-- ============ 4) Quantity increase/decrease inside modal ============ --}}
                    <div class="os-qty">
                        <button type="button" onclick="changeCheckoutQty(-1)">&minus;</button>
                        <span id="coQty">1</span>
                        <button type="button" onclick="changeCheckoutQty(1)">+</button>
                    </div>
                </div>
                <div class="os-price" id="coPrice"></div>
            </div>

            <form id="checkoutFormEl" onsubmit="submitCheckout(event)">
                <div id="checkoutError" style="display:none;color:#c0392b;background:#fdecea;padding:10px 14px;border-radius:8px;margin-bottom:14px;font-size:.85rem;"></div>

                <div class="form-grid">
                    <div class="form-field full">
                        <label>Name</label>
                        <input type="text" name="name" required placeholder="e.g. Farzana Akter">
                    </div>
                    <div class="form-field">
                        <label>Phone Number</label>
                        {{-- ============ 2) Live 11-digit phone validation ============ --}}
                        <input
                            type="tel"
                            name="phone_number"
                            id="phoneInput"
                            required
                            placeholder="01XXXXXXXXX"
                            maxlength="11"
                            inputmode="numeric"
                            autocomplete="tel"
                        >
                        <small class="field-error" id="phoneError" style="display:none;color:#c0392b;font-size:.75rem;">
                            Phone number must be exactly 11 digits (e.g. 01XXXXXXXXX).
                        </small>
                    </div>
                    <div class="form-field">
                        <label>Email (optional)</label>
                        <input type="email" name="email" placeholder="you@example.com">
                    </div>
                    <div class="form-field full">
                        <label>Delivery Address</label>
                        <textarea name="address" rows="2" required placeholder="House, Road, Area, City"></textarea>
                    </div>
                    <div class="form-field">
                        <label>District</label>
                        <select name="city" id="citySelect" onchange="updateDeliveryDisplay()">
                            <!-- Dhaka Division -->
                            <option value="Dhaka">Dhaka</option>
                            <option value="Faridpur">Faridpur</option>
                            <option value="Gazipur">Gazipur</option>
                            <option value="Gopalganj">Gopalganj</option>
                            <option value="Kishoreganj">Kishoreganj</option>
                            <option value="Madaripur">Madaripur</option>
                            <option value="Manikganj">Manikganj</option>
                            <option value="Munshiganj">Munshiganj</option>
                            <option value="Narayanganj">Narayanganj</option>
                            <option value="Narsingdi">Narsingdi</option>
                            <option value="Rajbari">Rajbari</option>
                            <option value="Shariatpur">Shariatpur</option>
                            <option value="Tangail">Tangail</option>

                            <!-- Chattogram Division -->
                            <option value="Chattogram">Chattogram</option>
                            <option value="Bandarban">Bandarban</option>
                            <option value="Brahmanbaria">Brahmanbaria</option>
                            <option value="Chandpur">Chandpur</option>
                            <option value="Cox's Bazar">Cox's Bazar</option>
                            <option value="Cumilla">Cumilla</option>
                            <option value="Feni">Feni</option>
                            <option value="Khagrachari">Khagrachari</option>
                            <option value="Lakshmipur">Lakshmipur</option>
                            <option value="Noakhali">Noakhali</option>
                            <option value="Rangamati">Rangamati</option>

                            <!-- Rajshahi Division -->
                            <option value="Rajshahi">Rajshahi</option>
                            <option value="Bogura">Bogura</option>
                            <option value="Chapainawabganj">Chapainawabganj</option>
                            <option value="Joypurhat">Joypurhat</option>
                            <option value="Naogaon">Naogaon</option>
                            <option value="Natore">Natore</option>
                            <option value="Pabna">Pabna</option>
                            <option value="Sirajganj">Sirajganj</option>

                            <!-- Khulna Division -->
                            <option value="Khulna">Khulna</option>
                            <option value="Bagerhat">Bagerhat</option>
                            <option value="Chuadanga">Chuadanga</option>
                            <option value="Jashore">Jashore</option>
                            <option value="Jhenaidah">Jhenaidah</option>
                            <option value="Kushtia">Kushtia</option>
                            <option value="Magura">Magura</option>
                            <option value="Meherpur">Meherpur</option>
                            <option value="Narail">Narail</option>
                            <option value="Satkhira">Satkhira</option>

                            <!-- Barishal Division -->
                            <option value="Barishal">Barishal</option>
                            <option value="Barguna">Barguna</option>
                            <option value="Bhola">Bhola</option>
                            <option value="Jhalokathi">Jhalokathi</option>
                            <option value="Patuakhali">Patuakhali</option>
                            <option value="Pirojpur">Pirojpur</option>

                            <!-- Sylhet Division -->
                            <option value="Sylhet">Sylhet</option>
                            <option value="Habiganj">Habiganj</option>
                            <option value="Moulvibazar">Moulvibazar</option>
                            <option value="Sunamganj">Sunamganj</option>

                            <!-- Rangpur Division -->
                            <option value="Rangpur">Rangpur</option>
                            <option value="Dinajpur">Dinajpur</option>
                            <option value="Gaibandha">Gaibandha</option>
                            <option value="Kurigram">Kurigram</option>
                            <option value="Lalmonirhat">Lalmonirhat</option>
                            <option value="Nilphamari">Nilphamari</option>
                            <option value="Panchagarh">Panchagarh</option>
                            <option value="Thakurgaon">Thakurgaon</option>

                            <!-- Mymensingh Division -->
                            <option value="Mymensingh">Mymensingh</option>
                            <option value="Jamalpur">Jamalpur</option>
                            <option value="Netrokona">Netrokona</option>
                            <option value="Sherpur">Sherpur</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label>Delivery Charge</label>
                        <input type="text" id="coDeliveryDisplay" value="৳70 (Inside Dhaka)" disabled>
                    </div>
                    <div class="form-field full">
                        <label>Order Note (optional)</label>
                        <textarea name="note" rows="2" placeholder="Anything we should know about your order?"></textarea>
                    </div>
                </div>

                <div class="option-label" style="margin-top:16px;">Payment Method</div>
                <div class="payment-options" id="payOptions">
                    <div class="pay-chip active" onclick="selectPay(this)">Cash on Delivery</div>
                </div>

                <div class="total-row">
                    <span>Total Payable</span>
                    <span id="coTotal">৳0</span>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Confirm Order</button>
            </form>
        </div>
    </div>
</div>

<script>
    /* ============ DATA FROM CONTROLLER ============ */
    const PRODUCTS = @json($products);

    let currentProduct = null;
    let currentSize = null;
    let currentColor = null;
    let currentQty = 1;
    let currentPayment = 'Cash on Delivery';

    /* ============ 2 & 3) Phone validation + incomplete-order capture state ============ */
    let phoneValid = false;
    let incompleteOrderId = null;
    let incompleteOrderTimer = null;

    /* ============ 5) Cart state ============ */
    let cart = [];
    let checkoutItems = [];

    /* ============ HERO SLIDER ============ */
    let slideIndex = 0;
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.slider-dot');

    function renderSlide() {
        slides.forEach((s, i) => s.classList.toggle('active', i === slideIndex));
        dots.forEach((d, i) => d.classList.toggle('active', i === slideIndex));
    }
    function changeSlide(step) {
        slideIndex = (slideIndex + step + slides.length) % slides.length;
        renderSlide();
    }
    function goToSlide(i) {
        slideIndex = i;
        renderSlide();
    }
    setInterval(() => changeSlide(1), 6000);

    /* ============ FORMAT HELPERS ============ */
    function bdt(amount) {
        return '৳' + Number(amount).toLocaleString('en-US');
    }
    function starString(rating) {
        const full = Math.round(rating);
        return '★'.repeat(full) + '☆'.repeat(5 - full);
    }

    /* ============ SECTION 3: SHOW PRODUCT DETAIL ============ */
    function showProduct(id, shouldScroll = true) {
        const product = PRODUCTS.find(p => p.id === id);
        if (!product) return;

        currentProduct = product;
        currentSize = product.sizes[0];
        currentColor = (product.colors && product.colors.length > 0) ? product.colors[0] : null;
        currentQty = 1;

        document.querySelectorAll('.product-card').forEach(c => c.classList.remove('active-card'));
        document.getElementById('card-' + id).classList.add('active-card');

        document.getElementById('detImage').src = (currentColor && currentColor.image) ? currentColor.image : product.image;
        document.getElementById('detImage').alt = product.name;
        document.getElementById('detCategory').textContent = product.category;
        document.getElementById('detName').textContent = product.name;
        document.getElementById('detStars').textContent = starString(product.rating);
        document.getElementById('detRatingText').textContent = product.rating > 0
            ? product.rating.toFixed(1) + ' rating'
            : 'No ratings yet';
        document.getElementById('detPrice').textContent = bdt(product.price);
        document.getElementById('detOldPrice').textContent = product.old_price ? bdt(product.old_price) : '';
        document.getElementById('detDesc').textContent = product.description;

        const featuresEl = document.getElementById('detFeatures');
        featuresEl.innerHTML = '';
        product.details.forEach(d => {
            const li = document.createElement('li');
            li.textContent = d;
            featuresEl.appendChild(li);
        });

        const galleryEl = document.getElementById('detGallery');
        galleryEl.innerHTML = '';
        const allImages = [product.image, ...(product.gallery || [])];
        if (allImages.length > 1) {
            galleryEl.style.display = 'flex';
            allImages.forEach((src, idx) => {
                const thumb = document.createElement('img');
                thumb.src = src;
                thumb.alt = product.name + ' photo ' + (idx + 1);
                if (idx === 0) thumb.classList.add('active');
                thumb.onclick = () => {
                    document.getElementById('detImage').src = src;
                    galleryEl.querySelectorAll('img').forEach(img => img.classList.remove('active'));
                    thumb.classList.add('active');
                };
                galleryEl.appendChild(thumb);
            });
        } else {
            galleryEl.style.display = 'none';
        }

        document.getElementById('detSizeLabel').textContent = product.size_label || 'Size';
        document.getElementById('detColorLabel').textContent = product.color_label || 'Colour';

        const sizesEl = document.getElementById('detSizes');
        sizesEl.innerHTML = '';
        product.sizes.forEach(size => {
            const chip = document.createElement('div');
            chip.className = 'size-chip' + (size === currentSize ? ' active' : '');
            chip.textContent = size;
            chip.onclick = () => {
                currentSize = size;
                sizesEl.querySelectorAll('.size-chip').forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
            };
            sizesEl.appendChild(chip);
        });

        const colorsEl = document.getElementById('detColors');
        const colorBlock = document.getElementById('detColorBlock');
        colorsEl.innerHTML = '';

        if (product.colors && product.colors.length > 0) {
            colorBlock.style.display = '';
            product.colors.forEach(color => {
                const chip = document.createElement('div');
                const isActive = currentColor && color.hex === currentColor.hex;
                chip.className = 'color-chip' + (isActive ? ' active' : '');
                chip.style.background = color.hex;
                chip.title = color.name;
                chip.onclick = () => {
                    currentColor = color;
                    colorsEl.querySelectorAll('.color-chip').forEach(c => c.classList.remove('active'));
                    chip.classList.add('active');

                    // এই কালারের সাথে লিংক করা gallery image থাকলে সেটাই main image-এ দেখাই,
                    // না থাকলে featured image-এই ফিরে যাই।
                    const imgEl = document.getElementById('detImage');
                    imgEl.src = color.image || product.image;

                    // থাম্বনেইল গ্যালারিতেও "active" state সিঙ্ক করি, যদি সেই ইমেজটা গ্যালারিতে থাকে
                    document.querySelectorAll('#detGallery img').forEach(img => {
                        img.classList.toggle('active', img.src === imgEl.src);
                    });
                };
                colorsEl.appendChild(chip);
            });
        } else {
            colorBlock.style.display = 'none';
            currentColor = null;
        }

        document.getElementById('detQty').textContent = currentQty;

        const detailSection = document.getElementById('detail');
        detailSection.classList.add('show');

        if (shouldScroll) {
            detailSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function changeQty(step) {
        currentQty = Math.max(1, currentQty + step);
        document.getElementById('detQty').textContent = currentQty;
    }

    /* ============ CART ============ */
    function addToCart() {
        if (!currentProduct) return;
        const key = currentProduct.id + '-' + currentSize + '-' + (currentColor ? currentColor.name : 'none');
        const existing = cart.find(item => item.key === key);
        if (existing) {
            existing.qty += currentQty;
        } else {
            cart.push({
                key, id: currentProduct.id, name: currentProduct.name, image: currentProduct.image,
                price: currentProduct.price, qty: currentQty, size: currentSize, color: currentColor,
            });
        }
        renderCart();
    }

    function removeFromCart(key) {
        cart = cart.filter(i => i.key !== key);
        renderCart();
    }

    function renderCart() {
        const countEl = document.getElementById('cartCount');
        const totalItems = cart.reduce((sum, i) => sum + i.qty, 0);
        countEl.style.display = totalItems > 0 ? 'flex' : 'none';
        if (totalItems > 0) countEl.textContent = totalItems;

        const listEl = document.getElementById('cartItemsList');
        const emptyEl = document.getElementById('cartEmpty');
        listEl.innerHTML = '';

        let footer = document.getElementById('cartDropFooter');
        if (cart.length === 0) {
            emptyEl.style.display = 'block';
            if (footer) footer.remove();
            // Save empty cart to storage
            saveCartToStorage();
            return;
        }
        emptyEl.style.display = 'none';

        cart.forEach(item => {
            const row = document.createElement('div');
            row.className = 'cart-drop-item';
            row.innerHTML = `
                <img src="${item.image}" alt="${item.name}">
                <div class="cart-drop-info">
                    <div class="cart-drop-name">${item.name}</div>
                    <div class="cart-drop-meta">${item.size || ''}${item.color ? ' · ' + item.color.name : ''} · Qty: ${item.qty}</div>
                    <div class="cart-drop-price">${bdt(item.price * item.qty)}</div>
                </div>
                <button type="button" class="cart-drop-remove" data-remove-key="${item.key}" title="Remove">&times;</button>
            `;
            listEl.appendChild(row);
        });

        listEl.querySelectorAll('[data-checkout-key]').forEach(btn =>
            btn.addEventListener('click', () => checkoutFromCart(btn.dataset.checkoutKey)));
        listEl.querySelectorAll('[data-remove-key]').forEach(btn =>
            btn.addEventListener('click', (e) => { 
                e.stopPropagation(); 
                removeFromCart(btn.dataset.removeKey); 
            }));

        if (!footer) {
            footer = document.createElement('div');
            footer.id = 'cartDropFooter';
            footer.className = 'cart-drop-footer';
            document.getElementById('cartDropdown').appendChild(footer);
        }
        footer.innerHTML = `<button type="button" class="cart-checkout-all" id="cartCheckoutAllBtn">Checkout All (${totalItems})</button>`;
        document.getElementById('cartCheckoutAllBtn').onclick = checkoutAllCart;

        // Save cart to storage after render
        saveCartToStorage();
    }

    function checkoutFromCart(key) {
        const item = cart.find(i => i.key === key);
        if (item) openCheckoutWithItems([{ ...item }]);
    }

    function checkoutAllCart() {
        if (cart.length) {
            // Copy items for checkout but don't clear cart yet
            const items = cart.map(i => ({ ...i }));
            openCheckoutWithItems(items);
        }
    }

    /* ============ CHECKOUT MODAL (multi-item) ============ */
    let currentDeliveryCharge = 70;

    function openCheckout() { // "Buy Now" on the detail page — single item
        if (!currentProduct) return;
        openCheckoutWithItems([{
            key: currentProduct.id + '-' + currentSize + '-' + (currentColor ? currentColor.name : 'none'),
            id: currentProduct.id, name: currentProduct.name, image: currentProduct.image,
            price: currentProduct.price, qty: currentQty, size: currentSize, color: currentColor,
        }]);
    }

    // Load cart from sessionStorage on page load
    function loadCartFromStorage() {
        try {
            const savedCart = sessionStorage.getItem('bethika_cart');
            if (savedCart) {
                cart = JSON.parse(savedCart);
            } else {
                cart = [];
            }
        } catch (e) {
            cart = [];
        }
    }

    // Save cart to sessionStorage
    function saveCartToStorage() {
        try {
            sessionStorage.setItem('bethika_cart', JSON.stringify(cart));
        } catch (e) {
            console.warn('Could not save cart to sessionStorage');
        }
    }

    function openCheckoutWithItems(items) {
        if (!items || !items.length) return;
        checkoutItems = items.map(i => ({ ...i }));

        phoneValid = false;
        incompleteOrderId = null;
        clearTimeout(incompleteOrderTimer);
        document.getElementById('phoneInput').value = '';
        document.getElementById('phoneError').style.display = 'none';
        document.getElementById('checkoutError').style.display = 'none';

        renderCheckoutItems();
        updateDeliveryDisplay();
        document.getElementById('checkoutModal').classList.add('show');
    }

    function renderCheckoutItems() {
        const wrap = document.getElementById('coItemsList');
        wrap.innerHTML = '';

        checkoutItems.forEach((item, idx) => {
            const row = document.createElement('div');
            row.className = 'order-summary';
            row.innerHTML = `
                <img src="${item.image}" alt="${item.name}">
                <div>
                    <div class="os-name">${item.name}</div>
                    <div class="os-meta">${item.size || ''}${item.color ? ' · ' + item.color.name : ''}</div>
                    <div class="os-qty">
                        <button type="button" data-idx="${idx}" data-step="-1">&minus;</button>
                        <span>${item.qty}</span>
                        <button type="button" data-idx="${idx}" data-step="1">+</button>
                    </div>
                </div>
                <div class="os-price">${bdt(item.price * item.qty)}</div>
                ${checkoutItems.length > 1 ? `<button type="button" class="cart-drop-remove" data-remove-idx="${idx}" title="Remove">&times;</button>` : ''}
            `;
            wrap.appendChild(row);
        });

        wrap.querySelectorAll('[data-step]').forEach(btn => btn.addEventListener('click', () => {
            const idx = Number(btn.dataset.idx), step = Number(btn.dataset.step);
            checkoutItems[idx].qty = Math.max(1, checkoutItems[idx].qty + step);
            renderCheckoutItems();
            updateTotal();
            if (phoneValid) { clearTimeout(incompleteOrderTimer); incompleteOrderTimer = setTimeout(saveIncompleteOrder, 400); }
        }));
        wrap.querySelectorAll('[data-remove-idx]').forEach(btn => btn.addEventListener('click', () => {
            checkoutItems.splice(Number(btn.dataset.removeIdx), 1);
            if (!checkoutItems.length) { closeCheckout(); return; }
            renderCheckoutItems();
            updateTotal();
        }));
    }

    function closeCheckout() {
        document.getElementById('checkoutModal').classList.remove('show');
    }

    function selectPay(el) {
        document.querySelectorAll('.pay-chip').forEach(c => c.classList.remove('active'));
        el.classList.add('active');
        currentPayment = el.textContent;
    }

    function updateDeliveryDisplay() {
        const city = document.getElementById('citySelect')?.value || 'Dhaka';
        currentDeliveryCharge = city === 'Dhaka' ? 70 : 130;
        document.getElementById('coDeliveryDisplay').value =
            bdt(currentDeliveryCharge) + (city === 'Dhaka' ? ' (Inside Dhaka)' : ' (Outside Dhaka)');
        updateTotal();
    }

    function updateTotal() {
        if (!checkoutItems.length) return;
        const subtotal = checkoutItems.reduce((sum, i) => sum + i.price * i.qty, 0);
        document.getElementById('coTotal').textContent = bdt(subtotal + currentDeliveryCharge);
    }

    /* ============ phone validation + incomplete draft ============ */
    const phoneInput = document.getElementById('phoneInput');
    const phoneError = document.getElementById('phoneError');

    phoneInput.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 11);
        const digits = this.value;
        phoneValid = digits.length === 11 && /^01\d{9}$/.test(digits);

        if (digits.length === 0) {
            phoneError.style.display = 'none';
        } else if (!phoneValid) {
            phoneError.textContent = digits.length < 11
                ? `Enter ${11 - digits.length} more digit(s) — phone number must be 11 digits.`
                : 'Enter a valid Bangladeshi number starting with 01.';
            phoneError.style.display = 'block';
        } else {
            phoneError.style.display = 'none';
            clearTimeout(incompleteOrderTimer);
            incompleteOrderTimer = setTimeout(saveIncompleteOrder, 500);
        }
    });

    async function saveIncompleteOrder() {
        if (!checkoutItems.length || !phoneValid) return;
        const nameField = document.querySelector('#checkoutFormEl [name="name"]');
        try {
            const response = await fetch('{{ route("frontend.order.incomplete") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({
                    order_id: incompleteOrderId,
                    phone_number: phoneInput.value,
                    name: nameField ? (nameField.value || null) : null,
                    items: checkoutItems.map(i => ({
                        product_id: i.id, quantity: i.qty, price: i.price,
                        size: i.size || null, color: i.color ? i.color.name : null,
                    })),
                }),
            });
            const data = await response.json();
            if (response.ok && data.order_id) incompleteOrderId = data.order_id;
        } catch (err) {
            console.warn('Could not save incomplete order draft:', err);
        }
    }

    async function submitCheckout(e) {
        e.preventDefault();
        if (!checkoutItems.length) return;

        const form = document.getElementById('checkoutFormEl');
        const submitBtn = form.querySelector('button[type="submit"]');
        const errorBox = document.getElementById('checkoutError');
        errorBox.style.display = 'none';
        errorBox.innerHTML = '';

        if (!phoneValid) {
            phoneError.textContent = 'Please enter a valid 11 digit phone number before confirming.';
            phoneError.style.display = 'block';
            phoneInput.focus();
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Placing Order...';

        const payload = {
            order_id: incompleteOrderId,
            name: form.querySelector('[name="name"]').value,
            phone_number: phoneInput.value,
            email: form.querySelector('[name="email"]').value || null,
            address: form.querySelector('[name="address"]').value,
            city: form.querySelector('[name="city"]').value,
            note: form.querySelector('[name="note"]').value || null,
            payment_method: currentPayment,
            delivery_charge: currentDeliveryCharge,
            items: checkoutItems.map(i => ({
                product_id: i.id, quantity: i.qty, price: i.price,
                size: i.size || null, color: i.color ? i.color.name : null,
            })),
        };

        try {
            const response = await fetch('{{ route("frontend.order.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify(payload),
            });
            const data = await response.json();

            if (!response.ok) {
                const messages = data.errors ? Object.values(data.errors).flat() : [data.message || 'Something went wrong. Please try again.'];
                errorBox.innerHTML = messages.join('<br>');
                errorBox.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Confirm Order';
                return;
            }

            const orderedKeys = new Set(checkoutItems.map(i => i.key).filter(Boolean));
            cart = cart.filter(i => !orderedKeys.has(i.key));
            renderCart();

            window.location.href = data.redirect_url;
        } catch (err) {
            errorBox.textContent = 'Network error, please check your connection and try again.';
            errorBox.style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Confirm Order';
        }
    }

    // close modal when clicking outside the box
    document.getElementById('checkoutModal').addEventListener('click', function (e) {
        if (e.target === this) closeCheckout();
    });

    // subtle header shadow once the page is scrolled
    const siteHeader = document.getElementById('siteHeader');
    window.addEventListener('scroll', () => {
        siteHeader.classList.toggle('scrolled', window.scrollY > 8);
    });

    // fade cards/reviews in as they enter the viewport
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

    // ============ CART HOVER - INSTANT SHOW, DELAYED HIDE ============
    let cartHoverTimer = null;
    let isCartHovered = false;
    let cartDropdownVisible = false;

    const cartWrapper = document.getElementById('cartWrapper');
    const cartDropdown = document.getElementById('cartDropdown');

    function showCartDropdown() {
        cartDropdownVisible = true;
        cartDropdown.style.display = 'block';
    }

    function hideCartDropdown() {
        cartDropdownVisible = false;
        cartDropdown.style.display = 'none';
    }

    cartWrapper.addEventListener('mouseenter', function() {
        isCartHovered = true;
        if (cartHoverTimer) {
            clearTimeout(cartHoverTimer);
            cartHoverTimer = null;
        }
        showCartDropdown();
    });

    cartWrapper.addEventListener('mouseleave', function() {
        isCartHovered = false;
        if (cartHoverTimer) {
            clearTimeout(cartHoverTimer);
            cartHoverTimer = null;
        }
        cartHoverTimer = setTimeout(() => {
            if (!isCartHovered) {
                hideCartDropdown();
            }
            cartHoverTimer = null;
        }, 1500);
    });

    document.addEventListener('click', function(e) {
        if (!cartWrapper.contains(e.target) && cartDropdownVisible) {
            isCartHovered = false;
            if (cartHoverTimer) {
                clearTimeout(cartHoverTimer);
                cartHoverTimer = null;
            }
            hideCartDropdown();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        loadCartFromStorage();
        
        if (PRODUCTS.length) {
            showProduct(PRODUCTS[0].id, false);
        }
        renderCart();
        window.scrollTo(0, 0);
    });
</script>

 <!-- WhatsApp Button  -->
 <div id="whatsapp">
  <a href="https://wa.me/{{ preg_replace('/\D/', '', '88'.$settings?->whatsapp_number) }}" target="_blank" id="toggle1" class="wtsapp">
    <i class="bi bi-whatsapp"></i>
  </a>
 </div>

</body>
</html>