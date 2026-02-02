
<li class="nav-item menu-is-opening {{ Request::is('*products', '*branch', '*users', '*catalogues') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ Request::is('*products', '*branch', '*users', '*catalogues') ? 'active' : '' }}">
    <i class="nav-icon fas fa-th"></i>
    <p>
    Master
    <i class="right fas fa-angle-left"></i>
    </p>
    </a>
    <ul class="nav nav-treeview" >
        <li class="nav-item">
            <a href="{{ route('branch.index') }}"
               class="nav-link {{ Request::is('*branch') ? 'active' : '' }}">
               <i class="far {{ Request::is('*branch') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                <p>Branches</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('products.index') }}"
               class="nav-link {{ Request::is('*products') ? 'active' : '' }}">
               <i class="far {{ Request::is('*products') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                <p>Products</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('users.index') }}"
               class="nav-link {{ Request::is('*users') ? 'active' : '' }}">
               
               <i class="far {{ Request::is('*users') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                <p>Users</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('catalogues.index') }}"
               class="nav-link {{ Request::is('*catalogues') ? 'active' : '' }}">
               
               <i class="far {{ Request::is('*catalogues') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                <p>Catalogues</p>
            </a>
        </li>

    </ul>
</li>
<li class="nav-item menu-is-opening {{ Request::is('*liftings*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ Request::is('*liftings*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-th"></i>
    <p> Liftings <i class="right fas fa-angle-left"></i> </p>
    </a>
    <ul class="nav nav-treeview" >
        <li class="nav-item">
            <a href="{{ route('liftings.index') }}"  class="nav-link {{ Request::is('*liftings*') ? 'active' : '' }}">
               <i class="far {{ Request::is('*liftings*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
               <p>Liftings</p>
            </a>
        </li>
    </ul>
</li>
<li class="nav-item menu-is-opening {{ Request::is('*pages*', '*contacts*', '*links*') ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ Request::is('*pages*', '*contacts*', '*links*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-pen"></i>
    <p> Pages & Links<i class="right fas fa-angle-left"></i> </p>
    </a>
    <ul class="nav nav-treeview" >
        <li class="nav-item">
            <a href="{{ route('pages.index') }}"  class="nav-link {{ Request::is('*pages*') ? 'active' : '' }}">
               <i class="far {{ Request::is('*pages*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
               <p> Pages</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('contacts.index') }}"  class="nav-link {{ Request::is('*contacts*') ? 'active' : '' }}">
               <i class="far {{ Request::is('*contacts*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
               <p>Contact Page</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('links.index') }}"  class="nav-link {{ Request::is('*links*') ? 'active' : '' }}">
               <i class="far {{ Request::is('*links*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
               <p>All Links</p>
            </a>
        </li>
    </ul>
</li>
