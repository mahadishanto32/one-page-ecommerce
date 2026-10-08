<div class="sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Admin Logo">
        <h3>Admin Panel</h3>
    </div>

    <ul class="sidebar-menu">

        <li>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="icon"><i class="fa-solid fa-chart-line"></i></span> Dashboard
            </a>
        </li>

        <li>
            <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                <span class="icon"><i class="fa-solid fa-chart-line"></i></span> Orders
            </a>
        </li>

        {{-- ================= PRODUCT DROPDOWN ================= --}}
        <li class="has-submenu {{ request()->routeIs('admin.products*') || request()->routeIs('admin.product-categories*') || request()->routeIs('admin.product-subcategories*') || request()->routeIs('admin.brands*') || request()->routeIs('admin.attributes*') || request()->routeIs('admin.attribute-options*') ? 'open' : '' }}">
            <a href="#" class="dropdown-toggle {{ request()->routeIs('admin.products*') || request()->routeIs('admin.product-categories*') || request()->routeIs('admin.product-subcategories*') || request()->routeIs('admin.brands*') || request()->routeIs('admin.attributes*') || request()->routeIs('admin.attribute-options*') ? 'active' : '' }}">
                <span class="icon"><i class="fa-solid fa-boxes-stacked"></i></span> Products
                <i class="fa-solid fa-chevron-down submenu-arrow"></i>
            </a>
            <ul class="submenu">
                <li>
                    <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                        <span class="icon"><i class="fa-solid fa-bag-shopping"></i></span> Products
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.product-categories.index') }}" class="{{ request()->routeIs('admin.product-categories*') ? 'active' : '' }}">
                        <span class="icon"><i class="fa-solid fa-sitemap"></i></span> Product Categories
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.attributes.index') }}" class="{{ request()->routeIs('admin.attributes*') ? 'active' : '' }}">
                        <span class="icon"><i class="fa-solid fa-sliders"></i></span> Product Attributes
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.attribute-options.index') }}" class="{{ request()->routeIs('admin.attribute-options*') ? 'active' : '' }}">
                        <span class="icon"><i class="fa-solid fa-list-check"></i></span> Attribute Options
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a href="{{ route('admin.homesliders.index') }}" class="{{ request()->routeIs('admin.homesliders*') ? 'active' : '' }}">
                <span class="icon"><i class="fa-solid fa-images"></i></span> Home Sliders
            </a>
        </li>
        <li>
            <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews*') ? 'active' : '' }}">
                <span class="icon"><i class="fa-solid fa-star"></i></span> Reviews
            </a>
        </li>
        <li>
            <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <span class="icon"><i class="fa-solid fa-cog"></i></span> Settings
            </a>
        </li>
    </ul>
</div>