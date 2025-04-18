@if (auth()->user()->role === 'admin')
    <div class="sidebar pe-4 pb-3">
        <nav class="navbar navbar-light">
            <a href="{{ url('/') }}" class="navbar-brand mx-4 mb-3">
                <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>{{ config('app.name', 'DiscountHub') }}</h3>
            </a>
            <div class="navbar-nav w-100">

                <a href="{{ route('home') }}" class="nav-item nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                    <i class="fa fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a href="{{ route('user') }}"
                    class="nav-item nav-link {{ request()->routeIs('user') ? 'active' : '' }}">
                    <i class="fa fa-user me-2"></i>Users
                </a>
                <a href="{{ route('product') }}"
                    class="nav-item nav-link {{ request()->routeIs('product') ? 'active' : '' }}">
                    <i class="fa fa-box me-2"></i>Products
                </a>
                <a href="{{ route('report') }}"
                    class="nav-item nav-link {{ request()->routeIs('report') ? 'active' : '' }}">
                    <i class="fa fa-chart-bar me-2"></i>Reports
                </a>
            </div>
        </nav>
    </div>
@endif
