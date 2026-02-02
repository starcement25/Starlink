
<li class="nav-item menu-is-opening {{ Request::is('*products', '*branch') ? 'menu-open' : '' }}"">
    <a href="#" class="nav-link active">
    <i class="nav-icon fas fa-th"></i>
    <p>
    Master
    <i class="right fas fa-angle-left"></i>
    </p>
    </a>
    <ul class="nav nav-treeview" style="display: block;">
        <li class="nav-item">
            <a href="{{ route('branch.index') }}"
               class="nav-link {{ Request::is('*branch') ? 'active' : '' }}">
                <p>Branches</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('products.index') }}"
               class="nav-link {{ Request::is('*products') ? 'active' : '' }}">
                <p>Products</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('users.index') }}"
               class="nav-link {{ Request::is('*users') ? 'active' : '' }}">
                <p>Users</p>
            </a>
        </li>
    </ul>
</li>
