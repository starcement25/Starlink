@canany(['branch.view','zones.view','employees.view','asm.view','masons.view','ledger.view','dealers.view','products.view','users.view','roles.view','catalogues.view','customer-stock.view','mason-categories.view','list.view','supports.view','redeemtions.view','banners.view'])
    <li class="nav-item menu-is-opening {{ Request::is('*products*', '*redeemtions*', '*supports*', '*list*', '*dealers*', '*masons*','*transfer/mason','*dealerlinkrequests*','*ledger*','*employees*', '*asm*', '*zones*',  '*branch*', '*users*', '*roles*', '*catalogues*', '*mason-categories*', '*banners*', '*customer-stock*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ Request::is('*products*', '*redeemtions*', '*supports*', '*list*', '*dealers*', '*masons*','*transfer/mason','*dealerlinkrequests*','*ledger*',  '*employees*', '*asm*', '*zones*', '*branch*', '*users*', '*roles*', '*catalogues*', '*mason-categories*', '*banners*', '*customer-stock*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-th"></i>
        <p>
        Master
        <i class="right fas fa-angle-left"></i>
        </p>
        </a>
        <ul class="nav nav-treeview" >
            @can('branch.view')
                <li class="nav-item">
                    <a href="{{ route('branch.index') }}"
                    class="nav-link {{ Request::is('*branch*') ? 'active' : '' }}">
                    <i class="far {{ Request::is('*branch*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                        <p>Branches</p>
                    </a>
                </li>
            @endcan
            @can('zones.view')
            <li class="nav-item">
                <a href="{{ route('zones.index') }}"
                class="nav-link {{ Request::is('*zones*') ? 'active' : '' }}">
                <i class="far {{ Request::is('*zones*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Zones</p>
                </a>
            </li>
            @endcan
            @can('employees.view')
            <li class="nav-item">
                <a href="{{ route('employees.index') }}"
                class="nav-link {{ Request::is('*employees*') ? 'active' : '' }}">
                <i class="far {{ Request::is('*employees*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Employees</p>
                </a>
            </li>
            @endcan
            @can('asm.view')
                <li class="nav-item">
                    <a href="{{ route('asm.index') }}"
                    class="nav-link {{ Request::is('*asm*') ? 'active' : '' }}">
                    <i class="far {{ Request::is('*asm*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                        <p>ASM</p>
                    </a>
                </li>
            @endcan
            @can('masons.view')
            <li class="nav-item">
                <a href="{{ route('masons.index') }}"
                class="nav-link {{ Request::is('*masons*') ? 'active' : '' }}">
                <i class="far {{ Request::is('*masons*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Masons</p>
                </a>
            </li>
            @endcan
            @can('masons.transfer')
            <li class="nav-item">
                <a href="{{ route('transfer.masons') }}"
                class="nav-link {{ Request::is('*transfer/mason') ? 'active' : '' }}">
                <i class="far {{ Request::is('*transfer/mason') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Masons Transferred</p>
                </a>
            </li>
            @endcan
            @can('dealer_link_requests.view')
            <li class="nav-item">
                <a href="{{ route('dealerlinkrequests.index') }}"
                class="nav-link {{ Request::is('*dealerlinkrequests*') ? 'active' : '' }}">
                <i class="far {{ Request::is('*dealerlinkrequests*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Dealer Link Requests</p>
                </a>
            </li>
            @endcan
            @can('ledger.view')
            <li class="nav-item">
                <a href="{{ route('mason.ledger') }}"
                class="nav-link {{ Request::is('*ledger*') ? 'active' : '' }}">
                <i class="far {{ Request::is('*ledger*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Ledger Master</p>
                </a>
            </li>
            @endcan
            @can('dealers.view')
            <li class="nav-item">
                <a href="{{ route('dealers.index') }}"
                class="nav-link {{ Request::is('*dealers*') ? 'active' : '' }}">
                <i class="far {{ Request::is('*dealers*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Dealers</p>
                </a>
            </li>
            @endcan
            @can('products.view')
            <li class="nav-item">
                <a href="{{ route('products.index') }}"
                class="nav-link {{ Request::is('*products*') ? 'active' : '' }}">
                <i class="far {{ Request::is('*products*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Products</p>
                </a>
            </li>
            @endcan
            @can('users.view')
            <li class="nav-item">
                <a href="{{ route('users.index') }}"
                class="nav-link {{ Request::is('*users*') ? 'active' : '' }}">
                
                <i class="far {{ Request::is('*users*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Users</p>
                </a>
            </li>
            @endcan

            @can('roles.view')
            <!-- Roles -->
            <li class="nav-item">
                <a href="{{ route('roles.index') }}"
                class="nav-link {{ Request::is('*roles*') ? 'active' : '' }}">
                
                <i class="far {{ Request::is('*roles*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Roles</p>
                </a>
            </li>
            @endcan
            @can('catalogues.view')

            <li class="nav-item">
                <a href="{{ route('catalogues.index') }}"
                class="nav-link {{ Request::is('*catalogues*') ? 'active' : '' }}">
                
                <i class="far {{ Request::is('*catalogues*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Catalogues</p>
                </a>
            </li>
            @endcan
            @can('customer-stock.view')
            <li class="nav-item">
                <a href="{{ route('customer-stock.index') }}"
                class="nav-link {{ Request::is('*customer-stock*') ? 'active' : '' }}">
                
                <i class="far {{ Request::is('*customer-stock*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Customer Lifting</p>
                </a>
            </li>
            @endcan
            @can('mason-categories.view')
            <li class="nav-item">
                <a href="{{ route('mason-categories.index') }}"
                class="nav-link {{ Request::is('*mason-categories*') ? 'active' : '' }}">
                
                <i class="far {{ Request::is('*mason-categories*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Mason Categories</p>
                </a>
            </li>
            @endcan
            @can('list.view')
            <li class="nav-item">
                <a href="{{ route('point.list') }}"
                class="nav-link {{ Request::is('*list*') ? 'active' : '' }}">
                
                <i class="far {{ Request::is('*list*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Point Add & Deduct</p>
                </a>
            </li>
            @endcan
            @can('supports.view')
            <li class="nav-item">
                <a href="{{ route('supports.index') }}"
                class="nav-link {{ Request::is('*supports*') ? 'active' : '' }}">
                
                <i class="far {{ Request::is('*supports*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Support Master</p>
                </a>
            </li>
            @endcan
            @can('redeemtions.view')
            <li class="nav-item">
                <a href="{{ route('redeemtions.index') }}"
                class="nav-link {{ Request::is('*redeemtions*') ? 'active' : '' }}">
                
                <i class="far {{ Request::is('*redeemtions*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Redeemtion Master</p>
                </a>
            </li>
            @endcan
            @can('banners.view')
            <li class="nav-item">
                <a href="{{ route('banners.index') }}"
                class="nav-link {{ Request::is('*banners*') ? 'active' : '' }}">
                
                <i class="far {{ Request::is('*banners*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Banners</p>
                </a>
            </li>
            @endcan

        </ul>
    </li>
@endcanany

@canany(['liftings.view','verify/lifting.view'])
    <li class="nav-item menu-is-opening {{ Request::is('*liftings*', '*verify/lifting*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ Request::is('*liftings*', '*verify/lifting*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-pen"></i>
        <p> Liftings <i class="right fas fa-angle-left"></i> </p>
        </a>
        <ul class="nav nav-treeview" >
            @can('liftings.view')
                <li class="nav-item">
                    <a href="{{ route('liftings.index') }}"  class="nav-link {{ Request::is('*liftings*') ? 'active' : '' }}">
                    <i class="far {{ Request::is('*liftings*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Liftings</p>
                    </a>
                </li>
            @endcan
            @can('verify/lifting.view')
                <li class="nav-item">
                    <a href="{{ route('verify.liftings') }}"  class="nav-link {{ Request::is('*verify/lifting*') ? 'active' : '' }}">
                    <i class="far {{ Request::is('*verify/lifting*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Verify Liftings</p>
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcanany

@canany(['pages.view','contacts.view','links.view'])
    <li class="nav-item menu-is-opening {{ Request::is('*pages*', '*contacts*', '*links*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ Request::is('*pages*', '*contacts*', '*links*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-pen"></i>
        <p> Pages & Links<i class="right fas fa-angle-left"></i> </p>
        </a>
        <ul class="nav nav-treeview" >
            @can('pages.view')
                <li class="nav-item">
                    <a href="{{ route('pages.index') }}"  class="nav-link {{ Request::is('*pages*') ? 'active' : '' }}">
                    <i class="far {{ Request::is('*pages*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p> Pages</p>
                    </a>
                </li>
            @endcan
            @can('contacts.view')
                <li class="nav-item">
                    <a href="{{ route('contacts.index') }}"  class="nav-link {{ Request::is('*contacts*') ? 'active' : '' }}">
                    <i class="far {{ Request::is('*contacts*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>Contact Page</p>
                    </a>
                </li>
            @endcan
            @can('links.view')
                <li class="nav-item">
                    <a href="{{ route('links.index') }}"  class="nav-link {{ Request::is('*links*') ? 'active' : '' }}">
                    <i class="far {{ Request::is('*links*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                    <p>All Links</p>
                    </a>
                </li>
            @endcan
        </ul>
    </li>
@endcanany

@can('reports.view')
    <li class="nav-item menu-is-opening {{ Request::is('*reports*') ? 'menu-open' : '' }}">
        <a href="#" class="nav-link {{ Request::is('*reports*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-pen"></i>
        <p> Reports<i class="right fas fa-angle-left"></i> </p>
        </a>
        <ul class="nav nav-treeview" >
            <li class="nav-item">
                <a href="{{ route('mason.points') }}"  class="nav-link {{ Request::is('*mason/points*') ? 'active' : '' }}">
                <i class="far {{ Request::is('*mason/points*') ? 'fa-dot-circle' : 'fa-circle' }}  nav-icon"></i>
                <p> Mason Points</p>
                </a>
            </li>
        </ul>
    </li>
@endcan

<!-- Dashboard Menu -->
@can('dashboard.view')
    <li class="nav-item menu-is-opening {{ Request::is('*dashboard*') ? 'menu-open' : '' }}">
        <a href="{{route('admin.employee.dashboard')}}" class="nav-link {{ Request::is('*dashboard*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-th"></i>
        <p> Dashboard  </p>
        </a>
    </li>
@endcan

<!-- Settings Menu -->
@can('settings.view')
    <li class="nav-item menu-is-opening {{ Request::is('*settings*') ? 'menu-open' : '' }}">
        <a href="{{route('settings.index')}}" class="nav-link {{ Request::is('*settings*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-cog"></i>
        <p> Settings  </p>
        </a>
    </li>
@endcan