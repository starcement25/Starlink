
<li class="nav-item menu-is-opening {{ Request::is('*tour-registrations') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ Request::is('*tour-registrations') ? 'active' : '' }}">
    <i class="nav-icon fas fa-th"></i>
    <p>
        All Pages
    <i class="right fas fa-angle-left"></i>
    </p>
    </a>
    <ul class="nav nav-treeview" >
        <li class="nav-item">
            <a href="{{ route('tour.registration') }}"
               class="nav-link {{ Request::is('*tour-registrations') ? 'active' : '' }}">
               <i class="far {{ Request::is('*tour-registrations') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                <p>All Lists</p>
            </a>
        </li>

    </ul>
</li>
