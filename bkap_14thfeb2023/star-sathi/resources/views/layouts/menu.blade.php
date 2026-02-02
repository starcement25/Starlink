
<li class="nav-item">
    <a href="{{ route('tests.index') }}" class="nav-link {{ Request::is('tests*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Tests</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('dealers.index') }}" class="nav-link {{ Request::is('dealers*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Dealers</p>
    </a>
</li>
