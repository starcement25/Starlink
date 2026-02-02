<?php 
    $pages = \App\Utils\Helper::getAllPages()
 ?>
 <li class="nav-item menu-is-opening {{ Request::is('*pages*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ Request::is('*pages*') ? 'active' : '' }}">
    <i class="nav-icon fas fa-th"></i>
    <p>
      Pages
      <i class="right fas fa-angle-left"></i>
    </p>
    </a>
    <ul class="nav nav-treeview" >
        <li class="nav-item">
            <a href="{{ route('tour.pages.index') }}"
               class="nav-link {{ Request::is('*pages*') ? 'active' : '' }}">
               <i class="far {{ Request::is('*pages*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                <p>
                    Page List
                </p>
            </a>
        </li>
    </ul>
</li>
<li class="nav-item menu-is-opening {{ Request::is('*page-contents*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ Request::is('*page-contents*') ? 'active' : '' }}">
    <i class="nav-icon fas fa-th"></i>
    <p>
      Page Contents
      <i class="right fas fa-angle-left"></i>
    </p>
    </a>
    <ul class="nav nav-treeview" >
        
        @if(!empty($pages))
            @foreach ($pages as $page)
                 <li class="nav-item">
            <a href="{{ route('tour.page.list', ['id'=> $page->id]) }}"
               class="nav-link {{ Request::is('*page-contents/'.$page->id) ? 'active' : '' }}">
               <i class="far {{ Request::is('*page-contents/'.$page->id) ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                <p>
                    {{ $page->name }}
                </p>
            </a>
        </li>

            @endforeach
        @endif
       

    </ul>
</li>
<li class="nav-item menu-is-opening {{ Request::is('*page-data*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ Request::is('*page-data*') ? 'active' : '' }}">
    <i class="nav-icon fas fa-th"></i>
    <p>
        All Page Data
    <i class="right fas fa-angle-left"></i>
    </p>
    </a>
    <ul class="nav nav-treeview" >
        @if(!empty($pages))
            @foreach ($pages as $page)
                 <li class="nav-item">
                    <a href="{{ route('tour.page.data', ['id'=> $page->id]) }}"
                    class="nav-link {{ Request::is('*page-data/page/'.$page->id) ? 'active' : '' }}">
                    <i class="far {{ Request::is('*page-data/page/'.$page->id) ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                        <p>
                            {{ $page->name }}
                        </p>
                    </a>
                </li>

            @endforeach
        @endif

    </ul>
</li>
