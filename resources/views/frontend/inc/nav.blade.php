@if (get_setting('topbar_banner') != null)
    <div class="position-relative top-banner removable-session z-1035 d-none" data-key="top-banner" data-value="removed">
        <a href="{{ get_setting('topbar_banner_link') }}" class="d-block text-reset">
            <img src="{{ uploaded_asset(get_setting('topbar_banner')) }}" class="w-100 mw-100 h-50px h-lg-auto img-fit">
        </a>
        <button class="btn text-white absolute-top-right set-session" data-key="top-banner" data-value="removed"
            data-toggle="remove-parent" data-parent=".top-banner">
            <i class="la la-close la-2x"></i>
        </button>
    </div>
@endif
<!-- Top Bar -->
<div class="top-navbar bg-black text-white border-bottom border-soft-secondary z-1035">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 col">
                <ul class="list-inline d-flex justify-content-between justify-content-lg-start mb-0">
                    @if (get_setting('show_language_switcher') == 'on')
                        <li class="list-inline-item dropdown mr-3" id="lang-change">
                            @php
                                if (Session::has('locale')) {
                                    $locale = Session::get('locale', Config::get('app.locale'));
                                } else {
                                    $locale = 'en';
                                }
                            @endphp
                            <a href="javascript:void(0)" class="dropdown-toggle text-reset py-2" data-toggle="dropdown"
                                data-display="static">
                                <img src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                    data-src="{{ static_asset('assets/img/flags/' . $locale . '.png') }}"
                                    class="mr-2 lazyload"
                                    alt="{{ \App\Language::where('code', $locale)->first()->name }}" height="11">
                                <span
                                    class="opacity-80">{{ \App\Language::where('code', $locale)->first()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-left">
                                @foreach (\App\Language::all() as $key => $language)
                                    <li>
                                        <a href="javascript:void(0)" data-flag="{{ $language->code }}"
                                            class="dropdown-item @if ($locale == $language) active @endif">
                                            <img src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                data-src="{{ static_asset('assets/img/flags/' . $language->code . '.png') }}"
                                                class="mr-1 lazyload" alt="{{ $language->name }}" height="11">
                                            <span class="language">{{ $language->name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif

                    @if (get_setting('show_currency_switcher') == 'on')
                        <li class="list-inline-item dropdown" id="currency-change">
                            @php
                                if (Session::has('currency_code')) {
                                    $currency_code = Session::get('currency_code');
                                } else {
                                    $currency_code = \App\Currency::findOrFail(get_setting('system_default_currency'))->code;
                                }
                            @endphp
                            <a href="javascript:void(0)" class="dropdown-toggle text-reset py-2 opacity-80"
                                data-toggle="dropdown" data-display="static">
                                {{ \App\Currency::where('code', $currency_code)->first()->name }}
                                {{ \App\Currency::where('code', $currency_code)->first()->symbol }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right dropdown-menu-lg-left">
                                @foreach (\App\Currency::where('status', 1)->get() as $key => $currency)
                                    <li>
                                        <a class="dropdown-item @if ($currency_code == $currency->code) active @endif"
                                            href="javascript:void(0)"
                                            data-currency="{{ $currency->code }}">{{ $currency->name }}
                                            ({{ $currency->symbol }})
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="col-5 text-right">
                <ul class="list-inline mb-0 h-100 d-flex justify-content-end align-items-center">
                    @auth
                        @if (isAdmin())
                            <li class="list-inline-item mr-3 border-right border-left-0 pr-3 pl-0">
                                <a href="{{ route('admin.dashboard') }}"
                                    class="text-reset d-inline-block opacity-80">{{ translate('My Panel') }}</a>
                            </li>
                        @else
                            <li class="list-inline-item mr-3 border-right border-left-0 pr-3 pl-0 dropdown">
                                <a class="dropdown-toggle no-arrow text-reset" data-toggle="dropdown"
                                    href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                                    <span class="">
                                        <span class="position-relative d-inline-block">
                                            <i class="las la-bell fs-18"></i>
                                            @if (count(Auth::user()->unreadNotifications) > 0)
                                                <span
                                                    class="badge badge-sm badge-dot badge-circle badge-primary position-absolute absolute-top-right"></span>
                                            @endif
                                        </span>
                                    </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg py-0">
                                    <div class="p-3 bg-light border-bottom">
                                        <h6 class="mb-0">{{ translate('Notifications') }}</h6>
                                    </div>
                                    <div class="px-3 c-scrollbar-light overflow-auto " style="max-height:300px;">
                                        <ul class="list-group list-group-flush">
                                            @forelse(Auth::user()->unreadNotifications as $notification)
                                                <li class="list-group-item">
                                                    @if ($notification->type == 'App\Notifications\OrderNotification')
                                                        @if (Auth::user()->user_type == 'customer')
                                                            <a href="javascript:void(0)"
                                                                onclick="show_purchase_history_details({{ $notification->data['order_id'] }})"
                                                                class="text-reset">
                                                                <span class="ml-2">
                                                                    {{ translate(
                                                                        'Order code: ' .
                                                                            $notification->data['order_code'] .
                                                                            ' hasbeen ' .
                                                                            ucfirst(str_replace('_', ' ', $notification->data['status'])),
                                                                    ) }}
                                                                </span>
                                                            </a>
                                                        @elseif (Auth::user()->user_type == 'seller')
                                                            @if (Auth::user()->id == $notification->data['user_id'])
                                                                <a href="javascript:void(0)"
                                                                    onclick="show_purchase_history_details({{ $notification->data['order_id'] }})"
                                                                    class="text-reset">
                                                                    <span class="ml-2">
                                                                        {{ translate(
                                                                            'Order code: ' .
                                                                                $notification->data['order_code'] .
                                                                                ' hasbeen ' .
                                                                                ucfirst(str_replace('_', ' ', $notification->data['status'])),
                                                                        ) }}
                                                                    </span>
                                                                </a>
                                                            @else
                                                                <a href="javascript:void(0)"
                                                                    onclick="show_order_details({{ $notification->data['order_id'] }})"
                                                                    class="text-reset">
                                                                    <span class="ml-2">
                                                                        {{ translate(
                                                                            'Order code: ' .
                                                                                $notification->data['order_code'] .
                                                                                ' hasbeen ' .
                                                                                ucfirst(str_replace('_', ' ', $notification->data['status'])),
                                                                        ) }}
                                                                    </span>
                                                                </a>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </li>
                                            @empty
                                                <li class="list-group-item">
                                                    <div class="py-4 text-center fs-16">
                                                        {{ translate('No notification found') }}
                                                    </div>
                                                </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                    <div class="text-center border-top">
                                        <a href="{{ route('all-notifications') }}" class="text-reset d-block py-2">
                                            {{ translate('View All Notifications') }}
                                        </a>
                                    </div>
                                </div>
                            </li>

                            <li class="list-inline-item mr-3 border-right border-left-0 pr-3 pl-0">
                                <a href="{{ route('dashboard') }}"
                                    class="text-reset d-inline-block opacity-80">{{ translate('MyPanel') }}</a>
                            </li>
                        @endif
                        <li class="list-inline-item">
                            <a href="{{ route('logout') }}"
                                class="text-reset d-inline-block opacity-80">{{ translate('Logout') }}</a>
                        </li>
                    @else
                        <li class="list-inline-item mr-3 border-right border-left-0 pr-3 pl-0">
                            <a href="{{ route('user.login') }}"
                                class="text-reset d-inline-block opacity-80">{{ translate('Login') }}</a>
                        </li>
                        <li class="list-inline-item">
                            <a href="{{ route('user.registration') }}"
                                class="text-reset d-inline-block opacity-80">{{ translate('Registration') }}</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- END Top Bar -->
<header
    class="@if (get_setting('header_stikcy') == 'on') sticky-top @endif z-1020 bg-white border-bottom position-relative shadow-sm">
    <!-- <div class="position-relative logo-bar-area z-1">
        <div class="container">
            <div class="d-flex align-items-center">

                <div class="col-auto col-xl-3 pl-0 pr-3 d-flex align-items-center">
                    <a class="d-block py-20px mr-3 ml-0" href="{{ route('home') }}">
                        @php
                            $header_logo = get_setting('header_logo');
                        @endphp
                        @if ($header_logo != null)
<img src="{{ uploaded_asset($header_logo) }}" alt="{{ env('APP_NAME') }}" class="mw-100 h-30px h-md-40px" height="40">
@else
<img src="{{ static_asset('assets/img/logo.png') }}" alt="{{ env('APP_NAME') }}" class="mw-100 h-30px h-md-40px" height="40">
@endif
                    </a>

                    @if (Route::currentRouteName() != 'home')
<div class="d-none d-xl-block align-self-stretch category-menu-icon-box ml-auto mr-0">
                            <div class="h-100 d-flex align-items-center" id="category-menu-icon">
                                <div class="dropdown-toggle navbar-light bg-light h-40px w-50px pl-2 rounded border c-pointer">
                                    <span class="navbar-toggler-icon"></span>
                                </div>
                            </div>
                        </div>
@endif
                </div>
                <div class="d-lg-none ml-auto mr-0">
                    <a class="p-2 d-block text-reset" href="javascript:void(0);" data-toggle="class-toggle" data-target=".front-header-search">
                        <i class="las la-search la-flip-horizontal la-2x"></i>
                    </a>
                </div>

                <div class="flex-grow-1 front-header-search d-flex align-items-center bg-white">
                    <div class="position-relative flex-grow-1">
                        <form action="{{ route('search') }}" method="GET" class="stop-propagation">
                            <div class="d-flex position-relative align-items-center">
                                <div class="d-lg-none" data-toggle="class-toggle" data-target=".front-header-search">
                                    <button class="btn px-2" type="button"><i class="la la-2x la-long-arrow-left"></i></button>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="border-0 border-lg form-control" id="search" name="q" placeholder="{{ translate('I am shopping for...') }}" autocomplete="off">
                                    <div class="input-group-append d-none d-lg-block">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="la la-search la-flip-horizontal fs-18"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="typed-search-box stop-propagation document-click-d-none d-none bg-white rounded shadow-lg position-absolute left-0 top-100 w-100" style="min-height: 200px">
                            <div class="search-preloader absolute-top-center">
                                <div class="dot-loader"><div></div><div></div><div></div></div>
                            </div>
                            <div class="search-nothing d-none p-3 text-center fs-16">

                            </div>
                            <div id="search-content" class="text-left">

                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-none d-lg-none ml-3 mr-0">
                    <div class="nav-search-box">
                        <a href="#" class="nav-box-link">
                            <i class="la la-search la-flip-horizontal d-inline-block nav-box-icon"></i>
                        </a>
                    </div>
                </div>

                <div class="d-none d-lg-block ml-3 mr-0">
                    <div class="" id="compare">
                        @include('frontend.partials.compare')
                    </div>
                </div>

                <div class="d-none d-lg-block ml-3 mr-0">
                    <div class="" id="wishlist">
                        @include('frontend.partials.wishlist')
                    </div>
                </div>

                <div class="d-none d-lg-block  align-self-stretch ml-3 mr-0" data-hover="dropdown">
                    <div class="nav-cart-box dropdown h-100" id="cart_items">
                        @include('frontend.partials.cart')
                    </div>
                </div>

            </div>
        </div>
        @if (Route::currentRouteName() != 'home')
<div class="hover-category-menu position-absolute w-100 top-100 left-0 right-0 d-none z-3" id="hover-category-menu">
            <div class="container">
                <div class="row gutters-10 position-relative">
                    <div class="col-lg-3 position-static">
                        @include('frontend.partials.category_menu')
                    </div>
                </div>
            </div>
        </div>
@endif
    </div> -->
    @if (get_setting('header_menu_labels') != null)
        <div class="bg-white my-dropdown-menu border-top border-gray-200">
            <div class="container-fluid">
                <div>
                    <ul class="list-inline d-flex align-items-center text-right mb-0 pl-0 mobile-hor-swipe">
                        <li class="list-inline-item mr-auto">
                            <a class="py-20px mr-3 ml-0" href="{{ route('home') }}">
                                @php
                                    $header_logo = get_setting('header_logo');
                                @endphp
                                @if ($header_logo != null)
                                    <img src="{{ uploaded_asset($header_logo) }}" alt="{{ env('APP_NAME') }}"
                                        class="mw-100 h-md-750px" height="120">
                                @else
                                    <img src="{{ static_asset('assets/img/logo.png') }}" alt="{{ env('APP_NAME') }}"
                                        class="mw-100 h-md-70px" height="70">
                                @endif
                            </a>
                        </li>
                        @foreach (json_decode(get_setting('header_menu_labels'), true) as $key => $value)
                            <li class="list-inline-item d-md-block d-none hover-menu mr-0">
                                <a href="{{ json_decode(get_setting('header_menu_links'), true)[$key] }}"
                                   style="font-family: 'Hind', sans-serif;" class="fs-15 px-2 py-2 d-inline-block fw-700 text-uppercase hov-opacity-70 text-reset">
                                    {{ translate($value) }}
                                </a>
                               @if ($value == 'Shop By Concern')
                                    <div class="bg-white show-hover-menu">
                                        <ul>
                                            <li class="first-li"><a
                                                    href="{{ route('products.category', 'for-ageing-u8khz') }}"
                                                    class="text-reset"><span>For
                                                        Ageing</span></a>
                                                {{-- <div class="show-nested-menu">
                                                    <ul>
                                                        <a href="{{ route('products.category', 'Blue-Light-SunScreen--Gluta-Maxx-uTLV4') }}"
                                                            class="text-reset">
                                                            <li>Blue Light SunScreen & Gluta Maxx
                                                            </li>
                                                        </a>
                                                        <a href="{{ route('products.category', 'Co-Gluta-Face-wash-izrGW') }}"
                                                            class="text-reset">
                                                            <li>Co Gluta Face wash</li>
                                                        </a>
                                                        <a href="{{ route('products.category', 'Botox-Cream-aSo6X') }}"
                                                            class="text-reset">
                                                            <li>Botox Cream</li>
                                                        </a>

                                                    </ul>
                                                </div> --}}
                                            </li>
                                            <li class="first-li">
                                                <a href="{{ route('products.category', 'for-anti-hairfall-23ing') }}"
                                                    class="text-reset">For
                                                    Anti Hairfall</a>
                                                {{-- <div class="show-nested-menu">
                                                    <ul>
                                                        <a href="{{ route('products.category', 'Onion-Oil-WGQ5Q') }}"
                                                            class="text-reset">
                                                            <li>Onion Oil</li>
                                                        </a>
                                                        <a href="{{ route('products.category', 'Onion-Shampoo-UC1qK') }}"
                                                            class="text-reset">
                                                            <li>Onion Shampoo</li>
                                                        </a>
                                                        <a href="{{ route('products.category', 'Prosoft-nc6dS') }}"
                                                            class="text-reset">
                                                            <li>Prosoft</li>
                                                        </a>
                                                        <a href="{{ route('products.category', 'Foligroin-Hair-Serum-ludup') }}"
                                                            class="text-reset">
                                                            <li>Foligroin Hair Serum</li>
                                                        </a>
                                                        <a href="{{ route('products.category', 'Argain-Biotin-Shampoo-u2fgF') }}"
                                                            class="text-reset">
                                                            <li>Argain Biotin Shampoo</li>
                                                        </a>

                                                    </ul>
                                                </div> --}}
                                            </li>
                                            <li class="first-li"><a
                                                    href="{{ route('products.category', 'for-pigmentation-8aqlc') }}"
                                                    class="text-reset">For
                                                    Pigmentation & Brightening</a>
                                                {{-- <div class="show-nested-menu">
                                                    <ul>
                                                        <a href="{{ route('products.category', 'Gluta-Facewash-Sa1mB') }}"
                                                            class="text-reset">
                                                            <li>Gluta Facewash
                                                            </li>
                                                        </a>
                                                        <a href="{{ route('products.category', 'gluta-cream-jqeb3') }}"
                                                            class="text-reset">
                                                            <li>Gluta Cream</li>
                                                        </a>
                                                        <a href="{{ route('products.category', 'Gluta-Soap-QwNF3') }}"
                                                            class="text-reset">
                                                            <li>Gluta Soap</li>
                                                        </a>
                                                        <a href="{{ route('products.category', 'Foaming-Facewash-NDirL') }}"
                                                            class="text-reset">
                                                            <li>Foaming Facewash</li>
                                                        </a>
                                                        <a href="{{ route('products.category', 'Niacinamide-Serum-WLGeu') }}"
                                                            class="text-reset">
                                                            <li>Niacinamide Serum</li>
                                                        </a>
                                                    </ul>
                                                </div> --}}
                                            </li>
                                            <li class="first-li"><a
                                                    href="{{ route('products.category', 'for-acne-prone--anti-wrinkle-srgrw') }}"
                                                    class="text-reset">For
                                                    Anti Acne </a>
                                                {{-- <div class="show-nested-menu">
                                                    <ul>
                                                        <a href="{{ route('products.category', 'Vitamin-C-Facewash-2qrgF') }}"
                                                            class="text-reset">
                                                            <li>Vitamin C Facewash </li>
                                                        </a>
                                                        <a href="{{ route('products.category', 'Foaming-facewash-4AlwE') }}"
                                                            class="text-reset">
                                                            <li>Foaming facewash</li>
                                                        </a>
                                                        <a href="{{ route('products.category', 'Botox-Cream-WVeVF') }}"
                                                            class="text-reset">
                                                            <li>Botox Cream</li>
                                                        </a>
                                                    </ul>
                                                </div> --}}
                                            </li>
                                        </ul>
                                    </div>
                                @endif
                                @if ($value == 'Shop By Category')
                                    <div class="bg-white show-hover-menu">
                                        <ul>
                                            @foreach (\App\Category::where('level', 0)->orderBy('order_level', 'desc')->get()->take(11) as $key => $category)
                                            @if(trim($category->name) != "Shop by Concern")
                                                <li class="first-li" data-id="{{ $category->id }}">
                                                    <a href="{{ route('products.category', $category->slug) }}"
                                                        class="text-reset">
                                                        <img class="cat-image lazyload mr-2 opacity-60"
                                                            src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                            data-src="{{ uploaded_asset($category->icon) }}"
                                                            width="16"
                                                            alt="{{ $category->getTranslation('name') }}"
                                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                                        {{ $category->getTranslation('name') }}
                                                    </a>
                                                    @if (count(\App\Utility\CategoryUtility::get_immediate_children_ids($category->id)) > 0)
                                                        <div class="show-nested-menu">
                                                            <ul>
                                                                @foreach (\App\Category::where('parent_id', $category->id)->orderBy('order_level', 'desc')->get()->take(11) as $key => $sub_category)
                                                                    <a href="{{ route('products.category', $sub_category->slug) }}"
                                                                        class="text-reset header_sub">
                                                                        <li>{{ $sub_category->getTranslation('name') }}
                                                                        </li>
                                                                    </a>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                        <!--<div id="main-" class="d-md-none d-inline-block">-->
                        <!--    <span style="font-size:30px;cursor:pointer" onclick="openNav()">&#9776;</span>-->
                        <!--</div>-->
                        
                        <div id="main-" class="d-md-none mobile-search d-inline-block">
                            <ul class="list-inline d-flex mb-0 pl-0 mobile-hor-swipe">
                                <li class="show-input list-inline-item mr-0">
                                    <div class="ml-auto mr-0">
                                        <div class="p-2 d-block text-reset" data-toggle="class-toggle"
                                            data-target=".front-header-search">
                                            <i class="las la-search la-flip-horizontal la-2x"></i>
                                        </div>
                                    </div>
                                    <div class="show-input-none front-header-search">
                                        <div class="shadow position-relative">
                                            <form action="{{ route('search') }}" method="GET"
                                                class="stop-propagation">
                                                <div class="d-flex position-relative align-items-center">
                                                    <div class="input-group">
                                                        <input type="text" class="border-0 border-lg form-control"
                                                            id="search" name="q"
                                                            placeholder="{{ translate('I am shopping for...') }}"
                                                            autocomplete="off">
                                                        <div class="input-group-append d-none d-lg-block">
                                                            <button style="border-radius: 0 30px 30px 0"
                                                                class="btn btn-primary" type="submit">
                                                                <i class="la la-search la-flip-horizontal fs-18"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <div class="typed-search-box stop-propagation document-click-d-none d-none bg-white rounded shadow-lg position-absolute left-0 top-100 w-100"
                                                style="min-height: 200px">
                                                <div class="search-preloader absolute-top-center">
                                                    <div class="dot-loader">
                                                        <div></div>
                                                        <div></div>
                                                        <div></div>
                                                    </div>
                                                </div>
                                                <div class="search-nothing d-none p-3 text-center fs-16">
                                                </div>
                                                <div id="search-content" class="text-left">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <span style="font-size:30px;cursor:pointer" onclick="openNav()">&#9776;</span>
                                </li>
                            </ul>
                        </div>




                        <span class="position-relative d-md-block d-none ml-3">
                            <ul class="list-inline mb-0 pl-0 mobile-hor-swipe">

                                <li class="show-input list-inline-item mr-0">
                                    <div class="ml-auto mr-0">
                                        <div class="p-2 d-block text-reset" data-toggle="class-toggle"
                                            data-target=".front-header-search">
                                            <i class="las la-search la-flip-horizontal la-2x"></i>
                                        </div>
                                    </div>

                                    <div class="show-input-none front-header-search">
                                        <div class="flex-grow-1 position-absolute d-flex align-items-center">
                                            <div class="position-relative">
                                                <form action="{{ route('search') }}" method="GET"
                                                    class="stop-propagation">
                                                    <div class="d-flex position-relative align-items-center">
                                                        <div class="input-group">
                                                            <input style="border-radius: 30px 0 0 30px" type="text"
                                                                class="border-0 border-lg form-control" id="search"
                                                                name="q"
                                                                placeholder="{{ translate('I am shopping for...') }}"
                                                                autocomplete="off">
                                                            <div class="input-group-append d-none d-lg-block">
                                                                <button style="border-radius: 0 30px 30px 0"
                                                                    class="btn btn-primary" type="submit">
                                                                    <i
                                                                        class="la la-search la-flip-horizontal fs-18"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                                <div class="typed-search-box stop-propagation document-click-d-none d-none bg-white rounded shadow-lg position-absolute left-0 top-100 w-100"
                                                    style="min-height: 200px">
                                                    <div class="search-preloader absolute-top-center">
                                                        <div class="dot-loader">
                                                            <div></div>
                                                            <div></div>
                                                            <div></div>
                                                        </div>
                                                    </div>
                                                    <div class="search-nothing d-none p-3 text-center fs-16">

                                                    </div>
                                                    <div id="search-content" class="text-left">

                                                    </div>
                                                </div>
                                            </div>
                                            <!-- </div>
                    </div> -->
                                </li>

                                <li class="list-inline-item mr-0">
                                    <div class="d-none d-lg-block ml-2 mr-0">
                                        <div class="fw-600 " id="compare">
                                            @include('frontend.partials.compare')
                                        </div>
                                    </div>
                                </li>

                                <li class="list-inline-item mr-0">
                                    <div class="d-none d-lg-block ml-2 mr-0">
                                        <div class="fw-600 " id="wishlist">
                                            @include('frontend.partials.wishlist')
                                        </div>
                                    </div>
                                </li>
                                <li class="list-inline-item mr-0">
                                    <div class="d-none d-lg-block  align-self-stretch ml-2 mr-0"
                                        data-hover="dropdown">
                                        <div class="fw-600 nav-cart-box dropdown h-100" id="cart_items">
                                            @include('frontend.partials.cart')
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </span>
                    </ul>
                </div>
            </div>
        </div>
    @endif
</header>

{{-- mobile-navbar-only --}}
<div id="mySidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    {{-- <a href="#">About</a> --}}
    @foreach (json_decode(get_setting('header_menu_labels'), true) as $key => $value)
        <div class="hover-menu mr-0">
            <a @if ($value == 'Shop By Concern') onclick="myFunction('category_id')" @elseif ($value == 'Shop By Category') onclick="myFunction('category_subid')" @endif
                href="
            @if ($value == 'Shop By Concern' || $value == 'Shop By Category') #  @else
                {{ json_decode(get_setting('header_menu_links'), true)[$key] }} @endif"
                class="fs-14
                px-3 py-2 d-flex text-truncate justify-content-between fw-600 hov-opacity-70 text-light">
                {{ translate($value) }}
                @if ($value == 'Shop By Concern' || $value == 'Shop By Category')
                    <i class="las la-angle-down"></i>
                @endif

            </a>
            @if ($value == 'Shop By Concern')
                <div id="category_id" class="bg-white">
                    <ul>
                        <li class="first-li"><a href="{{ route('products.category', 'for-ageing-u8khz') }}"
                                class="text-reset"><span>For
                                    Ageing</span></a>
                            <div class="show-nested-menu">
                                <ul>
                                    <a href="{{ route('products.category', 'Blue-Light-SunScreen--Gluta-Maxx-uTLV4') }}"
                                        class="text-reset">
                                        <li>Blue Light SunScreen & Gluta Maxx
                                        </li>
                                    </a>
                                    <a href="{{ route('products.category', 'Co-Gluta-Face-wash-izrGW') }}"
                                        class="text-reset">
                                        <li>Co Gluta Face wash</li>
                                    </a>
                                    <a href="{{ route('products.category', 'Botox-Cream-aSo6X') }}"
                                        class="text-reset">
                                        <li>Botox Cream</li>
                                    </a>

                                </ul>
                            </div>
                        </li>
                        <li class="first-li">
                            <a href="{{ route('products.category', 'for-anti-hairfall-23ing') }}"
                                class="text-reset">For
                                anti Hairfall</a>
                            <div class="show-nested-menu">
                                <ul>
                                    <a href="{{ route('products.category', 'Onion-Oil-WGQ5Q') }}" class="text-reset">
                                        <li>Onion Oil</li>
                                    </a>
                                    <a href="{{ route('products.category', 'Onion-Shampoo-UC1qK') }}"
                                        class="text-reset">
                                        <li>Onion Shampoo</li>
                                    </a>
                                    <a href="{{ route('products.category', 'Prosoft-nc6dS') }}" class="text-reset">
                                        <li>Prosoft</li>
                                    </a>
                                    <a href="{{ route('products.category', 'Foligroin-Hair-Serum-ludup') }}"
                                        class="text-reset">
                                        <li>Foligroin Hair Serum</li>
                                    </a>
                                    <a href="{{ route('products.category', 'Argain-Biotin-Shampoo-u2fgF') }}"
                                        class="text-reset">
                                        <li>Argain Biotin Shampoo</li>
                                    </a>

                                </ul>
                            </div>
                        </li>
                        <li class="first-li"><a href="{{ route('products.category', 'for-pigmentation-8aqlc') }}"
                                class="text-reset">For
                                Pigmentation</a>
                            <div class="show-nested-menu">
                                <ul>
                                    <a href="{{ route('products.category', 'Gluta-Facewash-Sa1mB') }}"
                                        class="text-reset">
                                        <li>Gluta Facewash
                                        </li>
                                    </a>
                                    <a href="{{ route('products.category', 'gluta-cream-jqeb3') }}"
                                        class="text-reset">
                                        <li>Gluta Cream</li>
                                    </a>
                                    <a href="{{ route('products.category', 'Gluta-Soap-QwNF3') }}"
                                        class="text-reset">
                                        <li>Gluta Soap</li>
                                    </a>
                                    <a href="{{ route('products.category', 'Foaming-Facewash-NDirL') }}"
                                        class="text-reset">
                                        <li>Foaming Facewash</li>
                                    </a>
                                    <a href="{{ route('products.category', 'Niacinamide-Serum-WLGeu') }}"
                                        class="text-reset">
                                        <li>Niacinamide Serum</li>
                                    </a>
                                </ul>
                            </div>
                        </li>
                        <li class="first-li"><a
                                href="{{ route('products.category', 'for-acne-prone--anti-wrinkle-srgrw') }}"
                                class="text-reset">For
                                Acne prone & anti wrinkle</a>
                            <div class="show-nested-menu">
                                <ul>
                                    <a href="{{ route('products.category', 'Vitamin-C-Facewash-2qrgF') }}"
                                        class="text-reset">
                                        <li>Vitamin C Facewash </li>
                                    </a>
                                    <a href="{{ route('products.category', 'Foaming-facewash-4AlwE') }}"
                                        class="text-reset">
                                        <li>Foaming facewash</li>
                                    </a>
                                    <a href="{{ route('products.category', 'Botox-Cream-WVeVF') }}"
                                        class="text-reset">
                                        <li>Botox Cream</li>
                                    </a>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            @endif
            @if ($value == 'Shop By Category')
                <div id="category_subid" class="bg-white">
                    <ul>
                        @foreach (\App\Category::where('level', 0)->orderBy('order_level', 'desc')->get()->take(11) as $key => $category)
                            <li class="first-li" data-id="{{ $category->id }}">
                                <a href="{{ route('products.category', $category->slug) }}" class="text-reset">
                                    <img class="cat-image lazyload mr-2 opacity-60"
                                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                        data-src="{{ uploaded_asset($category->icon) }}" width="16"
                                        alt="{{ $category->getTranslation('name') }}"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                    {{ $category->getTranslation('name') }}
                                </a>
                                @if (count(\App\Utility\CategoryUtility::get_immediate_children_ids($category->id)) > 0)
                                    <div class="show-nested-menu">
                                        <ul>
                                            @foreach (\App\Category::where('parent_id', $category->id)->orderBy('order_level', 'desc')->get()->take(11) as $key => $sub_category)
                                                <a href="{{ route('products.category', $sub_category->slug) }}"
                                                    class="text-reset header_sub">
                                                    <li>{{ $sub_category->getTranslation('name') }}
                                                    </li>
                                                </a>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endforeach
</div>
{{-- mobile-navbar-only --}}

<script>
    function openNav() {
        document.getElementById("mySidenav").style.width = "250px";
        // document.getElementById("main").style.marginLeft = "250px";
    }

    function closeNav() {
        document.getElementById("mySidenav").style.width = "0";
        // document.getElementById("main").style.marginLeft = "0";
    }

    function myFunction($id) {
        var x = document.getElementById($id);
        if (x.style.display === "block") {
            x.style.display = "none";
        } else {
            x.style.display = "block";
        }
    }
</script>