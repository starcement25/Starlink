<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="JavaScript:void(0);" class="brand-link">
        <!-- <img src="https://assets.infyom.com/logo/blue_logo_150x150.png" > -->
        <img src="{{asset('logo/star_cement_logo.png')}}" 
             alt="{{ config('app.name') }} Logo"
             class="brand-image img-circle elevation-3" >
        <span class="brand-text font-weight-light">Star Link</span>
    </a>
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @include('admin.layouts.menu')
            </ul>
        </nav>
    </div>
</aside>
