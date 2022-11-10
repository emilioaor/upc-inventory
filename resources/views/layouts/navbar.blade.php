<div class="collapse navbar-collapse" id="navbarSupportedContent">
    <!-- Left Side Of Navbar -->
    <ul class="navbar-nav mr-auto">
        @auth
            @include('layouts.navbar-item', [
                    'label' => __('menu.users'),
                    'show' => Auth::user()->isAdmin(),
                    'items' => [
                        [
                            'label' => __('menu.addNew'),
                            'route' => route('user.create'),
                        ],
                        [
                            'label' => __('menu.list'),
                            'route' => route('user.index'),
                        ]
                    ]
                ])

            @include('layouts.navbar-item', [
                    'label' => __('menu.products'),
                    'show' => Auth::user()->isAdmin(),
                    'items' => [
                        [
                            'label' => __('menu.list'),
                            'route' => route('product.list'),
                        ]
                    ]
                ])

            @include('layouts.navbar-item', [
                    'label' => __('menu.digitalInventory'),
                    'show' => Auth::user()->isAdmin() || Auth::user()->isInventoryManager(),
                    'items' => [
                        [
                            'label' => __('menu.addNew'),
                            'route' => route('digital-inventory.create'),
                        ],
                        [
                            'label' => __('menu.list'),
                            'route' => route('digital-inventory.index'),
                        ]
                    ]
                ])

            @include('layouts.navbar-item', [
                    'label' => __('menu.warehouse'),
                    'items' => [
                        [
                            'label' => __('menu.inventoryCrossover'),
                            'route' => route('inventory.index'),
                        ]
                    ]
                ])

            @include('layouts.navbar-item', [
                    'label' => __('menu.productSerials'),
                    'items' => [
                        [
                            'label' => __('menu.addNew'),
                            'route' => route('product-serial.create'),
                        ],
                        [
                            'label' => __('menu.list'),
                            'route' => route('product-serial.index'),
                        ]
                    ]
                ])
        @endauth
    </ul>

    <!-- Right Side Of Navbar -->
    <ul class="navbar-nav ml-auto">
        <!-- Authentication Links -->
        @guest
            {{--<li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
            </li>
            @if (Route::has('register'))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                </li>
            @endif--}}
        @else
            <li class="nav-item dropdown">
                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    {{ Auth::user()->name }}
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="{{ route('user.config') }}">
                        {{ __('menu.security') }}
                    </a>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>
        @endguest
    </ul>
</div>
