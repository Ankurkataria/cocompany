@extends('frontend.layouts.app')

@section('content')
    {{-- Categories , Sliders . Today's deal --}}
    <div class="home-banner-area mb-4 test">
        <div class="row gutters-10 position-relative">

            @php
                $num_todays_deal = count(filter_products(\App\Product::where('published', 1)->where('todays_deal', 1))->get());
                $featured_categories = \App\Category::where('featured', 1)->get();
            @endphp


            <div class="@if ($num_todays_deal > 0) col-md-12 @else @endif">
                @if (get_setting('home_slider_images') != null)
                    <div class="aiz-carousel dots-inside-bottom mobile-img-auto-height" data-arrows="true" data-dots="true"
                        data-autoplay="true">
                        @php $slider_images = json_decode(get_setting('home_slider_images'), true);  @endphp
                        @foreach ($slider_images as $key => $value)
                            <div class="carousel-box">
                                <a href="{{ json_decode(get_setting('home_slider_links'), true)[$key] }}">
                                    <img class="d-block mw-100 img-fit shadow-sm overflow-hidden"
                                        src="{{ uploaded_asset($slider_images[$key]) }}" alt="{{ env('APP_NAME') }} promo"
                                        @if (count($featured_categories) == 0) height="400"
                                            @else
                                            height="315" @endif
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
                @if (count($featured_categories) > 0)
                    <ul class="list-unstyled mb-0 row gutters-5">
                        @foreach ($featured_categories as $key => $category)
                            <li class="minw-0 col-4 col-md mt-3">
                                <a href="{{ route('products.category', $category->slug) }}"
                                    class="d-block rounded bg-white p-2 text-reset shadow-sm">
                                    <img src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                        data-src="{{ uploaded_asset($category->banner) }}"
                                        alt="{{ $category->getTranslation('name') }}" class="lazyload img-fit"
                                        height="78"
                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';">
                                    <div class="text-truncate fs-12 fw-600 mt-2 opacity-70">
                                        {{ $category->getTranslation('name') }}</div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>


    {{-- Banner section 1 --}}
    @if (get_setting('home_banner1_images') != null)
        <div class="mb-4">
            <div class="container-fluid">
                <div class="row gutters-10">
                    @php $banner_1_imags = json_decode(get_setting('home_banner1_images')); @endphp
                    @foreach ($banner_1_imags as $key => $value)
                        <div class="col-xl col-md-6">
                            <div class="mb-4 mb-lg-0">
                                <a href="{{ json_decode(get_setting('home_banner1_links'), true)[$key] }}"
                                    class="d-block text-reset">
                                    <img src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                                        data-src="{{ uploaded_asset($banner_1_imags[$key]) }}"
                                        alt="{{ env('APP_NAME') }} promo" class="img-fluid rounded lazyload w-100">
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif


    {{-- Flash Deal --}}
    @php
        $flash_deal = \App\FlashDeal::where('status', 1)
            ->where('featured', 1)
            ->first();
    @endphp
    @if (
        $flash_deal != null &&
            strtotime(date('Y-m-d H:i:s')) >= $flash_deal->start_date &&
            strtotime(date('Y-m-d H:i:s')) <= $flash_deal->end_date)
        <section class="mb-4">
            <div class="container-fluid">
                <div class="px-2 py-4 px-md-4 py-md-3 bg-white shadow-sm rounded">

                   <div class="d-flex flex-wrap mb-3 align-items-baseline border-bottom">
  <div class="col-md-3 px-0"></div>
  <div class="col-md-6 text-center">
    <h3 class="h5 fw-900 text-uppercase mb-0">
      <span
        class="border-bottom border-primary border-width-2 pb-3 d-inline-block"
        >{{ translate('Flash Sale') }}</span
      >
    <span
      class="aiz-count-down fs-24 d-inline-flex ml-auto ml-lg-3 align-items-center"
      data-date="{{ date('Y/m/d H:i:s', $flash_deal->end_date) }}"
    ></span>
    </h3>
  </div>
  <div class="col-md-3 text-right px-0">
    <a
      href="{{ route('flash-deal-details', $flash_deal->slug) }}"
      class="ml-auto mr-0 btn btn-primary btn-sm shadow-md w-100 w-md-auto"
      >{{ translate('View More') }}</a
    >
  </div>
</div>


                    <div class="aiz-carousel gutters-10 half-outside-arrow" data-items="6" data-xl-items="5"
                        data-lg-items="4" data-md-items="3" data-sm-items="2" data-xs-items="2" data-arrows='true'>
                        @foreach ($flash_deal->flash_deal_products as $key => $flash_deal_product)
                            @php
                                $product = \App\Product::find($flash_deal_product->product_id);
                            @endphp
                            @if ($product != null && $product->published != 0)
                                <div class="carousel-box">
                                    @include('frontend.partials.product_box_1', ['product' => $product])
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif



    {{-- Best Selling  --}}
    <div id="section_best_selling">

    </div>

    {{-- Featured Section --}}
    <div id="section_featured">

    </div>

    @if ($num_todays_deal > 0)
        <!-- <div class="bg-soft-primary rounded-top p-3 d-flex align-items-center justify-content-center">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span class="fw-600 fs-16 mr-2 text-truncate">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        {{ translate('Todays Deal') }}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span class="badge badge-primary badge-inline">{{ translate('Hot') }}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div> -->
        {{-- <div class="col-lg-3 position-static d-none d-lg-block">
                    @include('frontend.partials.category_menu')
                </div> --}}
        <div class="container-fluid">
            <div class="px-2 py-4 px-md-4 py-md-3 bg-white shadow-sm rounded">
                <div class="d-flex mb-3 align-items-baseline border-bottom">
                    <div class="col-md-3 px-0"></div>
                    <div class="col-md-6 text-center">
                        <h3 class="h5 fw-900 text-uppercase mb-0">
                            <span class="border-bottom fs-24 border-primary border-width-2 pb-3 d-inline-block">NEW
                                LAUNCHES
                            </span>
                        </h3>
                    </div>
                    <div class="col-md-3 px-0"></div>
                </div>
                <div class="aiz-carousel gutters-10 half-outside-arrow" data-items="6" data-xl-items="4" data-lg-items="4"
                    data-md-items="4" data-sm-items="2" data-xs-items="2" data-arrows='true'>
                    @foreach (filter_products(\App\Product::where('published', 1)->where('todays_deal', '1'))->get() as $key => $product)
                        @if ($product != null)
                            <div class="carousel-box">
                                <a href="{{ route('product', $product->slug) }}"
                                    class="d-block p-2 text-reset bg-white border-primary h-100 rounded">
                                    <div class="row gutters-5 align-items-center">
                                        <div class="col-xxl">
                                            <div class="img">
                                                <img class="lazyload img-fit h-140px h-lg-280px"
                                                    src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                    data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                                                    alt="{{ $product->getTranslation('name') }}"
                                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                            </div>
                                        </div>
                                        <div class="col-xxl mt-1">
                                            <div class="fs-16">
                                                <span
                                                    class="d-block text-primary fw-600">{{ home_discounted_base_price($product) }}</span>
                                                @if (home_base_price($product) != home_discounted_base_price($product))
                                                    <del class="d-block opacity-70">{{ home_base_price($product) }}</del>
                                                @endif
                                                <div class="rating rating-sm mt-1">
                                                    {{ renderStarRating($product->rating) }}
                                                </div>
                                                <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0 h-35px">
                                                    {{ $product->name }}
                                                </h3>
                                            </div>
                                        </div>

                                    </div>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <?php
        //$customData = ["cleanse", "correct", "moisturize", "protect"] ;
        //for ($i = 0; $i <= count($customData); $i++) {
        //echo "<div class=\"col-lg-3 mt-3\"> <div class=\"bg-white rounded p-3\">
        //<div class=\"d-flex align-items-center\" style=\"min-height: 100px;\">
        //<div style=\"width:60px !important;\">
        //<a href=\"javascript:void(0)\">
        //<img width=\"100%\" class=\"first-image\" style=\"width:60px !important;\" src=\"https://cocompany.in/assets/images/favicon/{{$i}}.png\">
        //</a>
        //</div>
        //<div class=\"ml-3\">
        //<h4 class='mb-3 fs-15 fw-700'><a href=\"javascript:void(0)\" title=\"Rapid Delivery\">
        //{{$customData[$i]}}</a></h4>
        //</div>
        //</div>
        //</div>
        //</div>";
        //}
        ?>
    @endif

    <div class="my-4">
        <div class="container-fluid">
            <div class="px-2 px-md-4 py-5 bg-white shadow-sm rounded">
                <div class="row gutters-10">
                    <div class="col-md-12 mb-5 text-center">
                        <h3 class="h5 fw-900 text-uppercase mb-0">
                            <span class="border-bottom fs-24 border-primary border-width-2 pb-3 d-inline-block">SHOP BY
                                CONCERN</span>
                        </h3>
                    </div>
                    @php
                        $imgarray = ['/1.png', '/2.png', '/3.png', '/4.png'];
                        $array = ['Ageing Skin', 'Pigmentation', 'Acne Prone & Anti-Winkle', 'Anti-Hairfall'];
                        $color = ['#ffa54b', '#ff5751', '#e03d7e', '#6138a3'];
                        $slug = ['for-ageing-u8khz', 'for-pigmentation-8aqlc', 'for-acne-prone--anti-wrinkle-srgrw', 'for-anti-hairfall-23ing'];
                    @endphp
                    @foreach ($imgarray as $k => $item)
                        <div class="col-xl col-md-6">
                            <a href="{{ route('products.category', $slug[$k]) }}">
                                <div class="d-flex align-items-center">
                                    <div class="z-1">
                                        <img src="{{ static_asset('assets/img/concern/') }}{{ $item }}"
                                            width="100" />
                                    </div>
                                    <div style="border-radius: 0 50rem 50rem 0; box-shadow: 0 0px 65px -2px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) "
                                        class="w-100 ml-n5 z-0 text-center bg-white p-3">
                                        <h3 class="h5 text-dark fw-700 mb-0">
                                            Solution For
                                        </h3>
                                        <h6 class="mb-0 fs-13 fw-700" style="color: {{ $color[$k] }}">
                                            {{ $array[$k] }}
                                        </h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>


    {{-- Banner Section 2 --}}
    @if (get_setting('home_banner2_images') != null)
        <div class="mb-4">
            <div class="container-fluid">
                <div class="row gutters-10">
                    @php
                        $banner_2_imags = json_decode(get_setting('home_banner2_images'));
                    @endphp
                    @foreach ($banner_2_imags as $key => $value)
                        <!--<div class="col-xl col-md-6">-->
                        <div class="col-6 col-md-3">
                            <div class="mb-3 shadow p-1 mb-lg-0">
                                <a href="{{ json_decode(get_setting('home_banner2_links'), true)[$key] }}"
                                    class="d-block text-reset">
                                    {{-- <img height="300" src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                                        data-src="{{ uploaded_asset($banner_2_imags[$key]) }}"
                                        alt="{{ env('APP_NAME') }} promo" class="mw-100 object-fit lazyload w-100"> --}}
                                    <video autoplay loop muted style="width: -webkit-fill-available"
                                        src="{{ uploaded_asset($banner_2_imags[$key]) }}"></video>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Banner Section 3 --}}
    <div class="mb-4">
        <div class="container-fluid">
            <div class="row gutters-10">
                <div class="col-xl col-md-12">
                    <div class="mb-3 mb-lg-0">
                        <img src="{{ static_asset('assets/img/banner3.jpg') }}" alt="cocompany"
                            class="mw-100 object-fit lazyload w-100">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Category wise Products --}}
    <div id="section_home_categories">

    </div>

    {{-- Classified Product --}}
    @if (get_setting('classified_product') == 1)
        @php
            $classified_products = \App\CustomerProduct::where('status', '1')
                ->where('published', '1')
                ->take(10)
                ->get();
        @endphp
        @if (count($classified_products) > 0)
            <section class="mb-4">
                <div class="container-fluid">
                    <div class="px-2 py-4 px-md-4 py-md-3 bg-white shadow-sm rounded">
                        <div class="d-flex mb-3 align-items-baseline border-bottom">
                            <h3 class="h5 fw-700 mb-0">
                                <span
                                    class="border-bottom border-primary border-width-2 pb-3 d-inline-block">{{ translate('Classified Ads') }}</span>
                            </h3>
                            <a href="{{ route('customer.products') }}"
                                class="ml-auto mr-0 btn btn-primary btn-sm shadow-md">{{ translate('View More') }}</a>
                        </div>
                        <div class="aiz-carousel gutters-10 half-outside-arrow" data-items="6" data-xl-items="5"
                            data-lg-items="4" data-md-items="3" data-sm-items="2" data-xs-items="2" data-arrows='true'>
                            @foreach ($classified_products as $key => $classified_product)
                                <div class="carousel-box">
                                    <div
                                        class="aiz-card-box border border-light rounded hov-shadow-md my-2 has-transition">
                                        <div class="position-relative">
                                            <a href="{{ route('customer.product', $classified_product->slug) }}"
                                                class="d-block">
                                                <img class="img-fit lazyload mx-auto h-140px h-md-210px"
                                                    src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                    data-src="{{ uploaded_asset($classified_product->thumbnail_img) }}"
                                                    alt="{{ $classified_product->getTranslation('name') }}"
                                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                            </a>
                                            <div class="absolute-top-left pt-2 pl-2">
                                                @if ($classified_product->conditon == 'new')
                                                    <span
                                                        class="badge badge-inline badge-success">{{ translate('new') }}</span>
                                                @elseif($classified_product->conditon == 'used')
                                                    <span
                                                        class="badge badge-inline badge-danger">{{ translate('Used') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="p-md-3 p-2 text-left">
                                            <div class="fs-15 mb-1">
                                                <span
                                                    class="fw-700 text-primary">{{ single_price($classified_product->unit_price) }}</span>
                                            </div>
                                            <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0 h-35px">
                                                <a href="{{ route('customer.product', $classified_product->slug) }}"
                                                    class="d-block text-reset">{{ $classified_product->getTranslation('name') }}</a>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @endif

    {{-- Banner Section 2 --}}
    @if (get_setting('home_banner3_images') != null)
        <div class="mb-4">
            <div class="container-fluid">
                <div class="d-flex mb-3 align-items-baseline border-bottom">
                    <div class="col-md-3 px-0"></div>
                    <div class="col-md-6 text-center">
                        <h3 class="h5 fw-900 text-uppercase mb-0">
                            <span class="border-bottom fs-24 border-primary border-width-2 pb-3 d-inline-block">What Our Customers Have To Say</span>
                        </h3>
                    </div>
                    
                </div>
                <div class="row gutters-10">
                    @php $banner_3_imags = json_decode(get_setting('home_banner3_images')); @endphp
                    @foreach ($banner_3_imags as $key => $value)
                        <div class="col-xl col-md-6">
                            <div class="mb-3 mb-lg-0">
                                <a href="{{ json_decode(get_setting('home_banner3_links'), true)[$key] }}"
                                    class="d-block text-reset">
                                    <img height="300" src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                                        data-src="{{ uploaded_asset($banner_3_imags[$key]) }}"
                                        alt="{{ env('APP_NAME') }} promo" class="mw-100 object-fit lazyload w-100">
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Best Seller --}}
    @if (get_setting('vendor_system_activation') == 1)
        <div id="section_best_sellers">

        </div>
    @endif

    {{-- Top 10 categories and Brands --}}
    @if (get_setting('top10_categories') != null && get_setting('top10_brands') != null)
        <section class="mb-4">
            <div class="container-fluid">
                <div class="row gutters-10">
                    @if (get_setting('top10_categories') != null)
                        <div class="col-lg-6">
                            <div class="d-flex mb-3 align-items-baseline border-bottom">
                                <h3 class="h5 fw-700 mb-0">
                                    <span
                                        class="border-bottom border-primary border-width-2 pb-3 d-inline-block">{{ translate('Top 10 Categories') }}</span>
                                </h3>
                                <a href="{{ route('categories.all') }}"
                                    class="ml-auto mr-0 btn btn-primary btn-sm shadow-md">{{ translate('View All Categories') }}</a>
                            </div>
                            <div class="row gutters-5">
                                @php $top10_categories = json_decode(get_setting('top10_categories')); @endphp
                                @foreach ($top10_categories as $key => $value)
                                    @php $category = \App\Category::find($value); @endphp
                                    @if ($category != null)
                                        <div class="col-sm-6">
                                            <a href="{{ route('products.category', $category->slug) }}"
                                                class="bg-white border d-block text-reset rounded p-2 hov-shadow-md mb-2">
                                                <div class="row align-items-center no-gutters">
                                                    <div class="col-3 text-center">
                                                        <img src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                            data-src="{{ uploaded_asset($category->banner) }}"
                                                            alt="{{ $category->getTranslation('name') }}"
                                                            class="img-fluid img lazyload h-60px"
                                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                                    </div>
                                                    <div class="col-7">
                                                        <div class="text-truncat-2 pl-3 fs-14 fw-600 text-left">
                                                            {{ $category->getTranslation('name') }}</div>
                                                    </div>
                                                    <div class="col-2 text-center">
                                                        <i class="la la-angle-right text-primary"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if (get_setting('top10_brands') != null)
                        <div class="col-lg-6">
                            <div class="d-flex mb-3 align-items-baseline border-bottom">
                                <h3 class="h5 fw-700 mb-0">
                                    <span
                                        class="border-bottom border-primary border-width-2 pb-3 d-inline-block">{{ translate('Top 10 Brands') }}</span>
                                </h3>
                                <a href="{{ route('brands.all') }}"
                                    class="ml-auto mr-0 btn btn-primary btn-sm shadow-md">{{ translate('View All Brands') }}</a>
                            </div>
                            <div class="row gutters-5">
                                @php $top10_brands = json_decode(get_setting('top10_brands')); @endphp
                                @foreach ($top10_brands as $key => $value)
                                    @php $brand = \App\Brand::find($value); @endphp
                                    @if ($brand != null)
                                        <div class="col-sm-6">
                                            <a href="{{ route('products.brand', $brand->slug) }}"
                                                class="bg-white border d-block text-reset rounded  p-2 hov-shadow-md mb-2">
                                                <div class="row align-items-center no-gutters">
                                                    <div class="col-4 text-center">
                                                        <img src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                            data-src="{{ uploaded_asset($brand->logo) }}"
                                                            alt="{{ $brand->getTranslation('name') }}"
                                                            class="img-fluid img lazyload h-60px"
                                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="text-truncate-2 pl-3 fs-14 fw-600 text-left">
                                                            {{ $brand->getTranslation('name') }}</div>
                                                    </div>
                                                    <div class="col-2 text-center">
                                                        <i class="la la-angle-right text-primary"></i>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $.post('{{ route('home.section.featured') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_featured').html(data);
                AIZ.plugins.slickCarousel();
            });
            $.post('{{ route('home.section.best_selling') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_best_selling').html(data);
                AIZ.plugins.slickCarousel();
            });
            $.post('{{ route('home.section.home_categories') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_home_categories').html(data);
                AIZ.plugins.slickCarousel();
            });

            @if (get_setting('vendor_system_activation') == 1)
                $.post('{{ route('home.section.best_sellers') }}', {
                    _token: '{{ csrf_token() }}'
                }, function(data) {
                    $('#section_best_sellers').html(data);
                    AIZ.plugins.slickCarousel();
                });
            @endif
        });
    </script>
@endsection