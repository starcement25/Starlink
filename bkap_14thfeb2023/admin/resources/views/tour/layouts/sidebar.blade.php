<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ url('/home') }}" class="brand-link">
        <img src="https://assets.infyom.com/logo/blue_logo_150x150.png"
             
             class="brand-image img-circle elevation-3">
        <span class="brand-text font-weight-light">Tour Management</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @include('tour.layouts.menu')
            </ul>
        </nav>
    </div>
</aside>
