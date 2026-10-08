<div class="header">
    <h1>@yield('page-title', 'Dashboard')</h1>
    <div class="user-info">
        <span>Welcome, {{ Auth::user()->name }}</span>
        <span class="badge badge-{{ Auth::user()->role }}">
            {{ ucfirst(Auth::user()->role) }}
        </span>
        <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
        <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </button>
        </form>
    </div>
</div>