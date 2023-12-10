@extends('frontend.layouts.app')

@section('content')

    <section class="pt-5 mb-4">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 mx-auto">
                    <div class="row aiz-steps arrow-divider">
                        <div class="col active">
                            <div class="text-center text-primary">
                                <i class="la-3x mb-2 las la-shopping-cart"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('1. My Cart') }}</h3>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-center">
                                <i class="la-3x mb-2 opacity-50 las la-map"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('2. Shipping info') }}
                                </h3>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-center">
                                <i class="la-3x mb-2 opacity-50 las la-truck"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('3. Delivery info') }}
                                </h3>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-center">
                                <i class="la-3x mb-2 opacity-50 las la-credit-card"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('4. Payment') }}</h3>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-center">
                                <i class="la-3x mb-2 opacity-50 las la-check-circle"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('5. Confirmation') }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-4" id="cart-summary">
        <div class="container">
            @if ($carts && count($carts) > 0)
                <div class="row">
                    <div class="col-xxl-8 col-xl-10 mx-auto">
                        <div class="shadow-sm bg-white p-3 p-lg-4 rounded text-left">
                            <div class="mb-4">
                                <div class="row gutters-5 d-none d-lg-flex border-bottom mb-3 pb-3">
                                    <div class="col-md-5 fw-600">{{ translate('Product') }}</div>
                                    <div class="col fw-600">{{ translate('Price') }}</div>
                                    <div class="col fw-600">{{ translate('Tax') }}</div>
                                    <div class="col fw-600">{{ translate('Quantity') }}</div>
                                    <div class="col fw-600">{{ translate('Total') }}</div>
                                    <div class="col-auto fw-600">{{ translate('Remove') }}</div>
                                </div>
                                <ul class="list-group list-group-flush">
                                    {{-- /**for flash deals*/ --}}
                                    @php
                                        $total = 0;
                                        $deal_3_product = [];
                                        // Calculate the total discounted price for deal_3
                                        $discountedPrice3 = 0;
                                        $deal_2_product = [];
                                        // Calculate the total discounted price for deal 2
                                        $discountedPrice = 0;
                                        $flash_deal_status = false;
                                    @endphp
                                    {{-- /**for flash deals*/ --}}
                                    @foreach ($carts as $key => $cartproducts)
                                        @php
                                            $flashdeal_product = \App\FlashDealProduct::select('flash_deal_id')
                                                ->where('product_id', $cartproducts['product_id'])
                                                ->first();
                                            if (isset($flashdeal_product['flash_deal_id'])) {
                                                if ($flashdeal_product['flash_deal_id'] == 4) {
                                                    $deal_2_product[$key]['product_id'] = $cartproducts['product_id'];
                                                    $deal_2_product[$key]['price'] = $cartproducts['price'];
                                                    $deal_2_product[$key]['quantity'] = $cartproducts['quantity'];
                                                } elseif ($flashdeal_product['flash_deal_id'] == 3) {
                                                    $deal_3_product[$key]['product_id'] = $cartproducts['product_id'];
                                                    $deal_3_product[$key]['price'] = $cartproducts['price'];
                                                    $deal_3_product[$key]['quantity'] = $cartproducts['quantity'];
                                                }
                                            }
                                        @endphp
                                    @endforeach
                                    @php
                                        /**70% deal on 2 product */
                                        $totalProducts = count($deal_2_product);
                                        
                                        /**599 deal on 3 products */
                                        $totalProducts_3 = count($deal_3_product);
                                        
                                        if ($totalProducts > 0) {
                                            $avoidOffer = false;
                                            /**offer for buying two product on flash deal*/
                                            usort($deal_2_product, function ($a, $b) {
                                                return $a['price'] - $b['price'];
                                            });
                                            foreach ($deal_2_product as $v) {
                                                $quantity = $v['quantity'];
                                                if ($quantity > 1) {
                                                    $avoidOffer = true;
                                                    break;
                                                }
                                            }
                                        
                                            // Calculate the number of products eligible for the discount
                                            $discountedProductsCount = min($totalProducts, floor($totalProducts / 2) * 2);
                                            if ($avoidOffer == false) {
                                                for ($i = 0; $i < $totalProducts; $i++) {
                                                    if ($i <= $discountedProductsCount - 1) {
                                                        // Apply 70% discount to all but the highest-priced product
                                                        $discountedPrice += $deal_2_product[$i]['price'] * 0.3;
                                                    } else {
                                                        // Include the highest-priced product at its regular price
                                                        $discountedPrice += $deal_2_product[$i]['price'];
                                                    }
                                                }
                                                $flash_deal_status = true;
                                            } else {
                                                foreach ($deal_2_product as $k => $v) {
                                                    $discountedPrice += $v['price'] * $v['quantity'];
                                                }
                                            }
                                        }
                                        // ==============================================
                                        if ($totalProducts_3 > 0) {
                                            $avoidOffer = false;
                                            usort($deal_3_product, function ($a, $b) {
                                                return $b['price'] - $a['price'];
                                            });
                                            foreach ($deal_3_product as $v) {
                                                $quantity = $v['quantity'];
                                                if ($quantity > 1) {
                                                    $avoidOffer = true;
                                                    break;
                                                }
                                            }
                                            if ($avoidOffer == false) {
                                                // Calculate the number of sets of three products
                                                $setsOfThree = intdiv($totalProducts_3, 3);
                                                // Calculate the price for the sets of three products
                                                $discountedPrice3 += $setsOfThree * 599;
                                        
                                                // Calculate the remaining products
                                                $remainingProducts = $totalProducts_3 % 3;
                                        
                                                // Include the remaining products at their regular prices
                                                for ($i = 0; $i < $remainingProducts; $i++) {
                                                    $discountedPrice3 += $deal_3_product[$i]['price'];
                                                }
                                                $flash_deal_status = true;
                                            } else {
                                                foreach ($deal_3_product as $k => $v) {
                                                    $prc = $v['price'] * $v['quantity'];
                                                    $discountedPrice3 += $prc;
                                                }
                                            }
                                        }
                                    @endphp
                                    {{-- end flash deal --}}

                                    @foreach ($carts as $key => $cartItem)
                                        @php
                                            $product = \App\Product::find($cartItem['product_id']);
                                            $product_stock = $product->stocks->where('variant', $cartItem['variation'])->first();
                                            $total = $total + $cartItem['price'] * $cartItem['quantity'] + $cartItem['tax'];
                                            $product_name_with_choice = $product->getTranslation('name');
                                            if ($cartItem['variation'] != null) {
                                                $product_name_with_choice = $product->getTranslation('name') . ' - ' . $cartItem['variation'];
                                            }
                                        @endphp
                                        <li class="list-group-item px-0 px-lg-3">
                                            <div class="row gutters-5">
                                                <div class="col-lg-5 d-flex">
                                                    <span class="mr-2 ml-0">
                                                        <img src="{{ uploaded_asset($product->thumbnail_img) }}"
                                                            class="img-fit size-60px rounded"
                                                            alt="{{ $product->getTranslation('name') }}">
                                                    </span>
                                                    <span class="fs-14 opacity-60">{{ $product_name_with_choice }}
                                                        @if ($flash_deal_status)
                                                            <br>
                                                            <mark>#Flash Deal</mark>
                                                        @endif
                                                    </span>
                                                </div>

                                                <div class="col-lg col-4 order-1 order-lg-0 my-3 my-lg-0">
                                                    <span
                                                        class="opacity-60 fs-12 d-block d-lg-none">{{ translate('Price') }}</span>
                                                    <span
                                                        class="fw-600 fs-16">{{ single_price($cartItem['price']) }}</span>
                                                </div>
                                                <div class="col-lg col-4 order-2 order-lg-0 my-3 my-lg-0">
                                                    <span
                                                        class="opacity-60 fs-12 d-block d-lg-none">{{ translate('Tax') }}</span>
                                                    <span class="fw-600 fs-16">{{ single_price($cartItem['tax']) }}</span>
                                                </div>

                                                <div class="col-lg col-6 order-4 order-lg-0">
                                                    @if ($cartItem['digital'] != 1)
                                                        <div
                                                            class="row no-gutters align-items-center aiz-plus-minus mr-2 ml-0">
                                                            <button
                                                                class="btn col-auto btn-icon btn-sm btn-circle btn-light"
                                                                type="button" data-type="minus"
                                                                data-field="quantity[{{ $cartItem['id'] }}]">
                                                                <i class="las la-minus"></i>
                                                            </button>
                                                            <input type="number" name="quantity[{{ $cartItem['id'] }}]"
                                                                class="col border-0 text-center flex-grow-1 fs-16 input-number"
                                                                placeholder="1" value="{{ $cartItem['quantity'] }}"
                                                                min="{{ $product->min_qty }}"
                                                                max="{{ $product_stock->qty }}"
                                                                onchange="updateQuantity({{ $cartItem['id'] }}, this)">
                                                            <button
                                                                class="btn col-auto btn-icon btn-sm btn-circle btn-light"
                                                                type="button" data-type="plus"
                                                                data-field="quantity[{{ $cartItem['id'] }}]">
                                                                <i class="las la-plus"></i>
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-lg col-4 order-3 order-lg-0 my-3 my-lg-0">
                                                    <span
                                                        class="opacity-60 fs-12 d-block d-lg-none">{{ translate('Total') }}</span>
                                                    <span
                                                        class="fw-600 fs-16 text-primary">{{ single_price(($cartItem['price'] + $cartItem['tax']) * $cartItem['quantity']) }}</span>
                                                </div>
                                                <div class="col-lg-auto col-6 order-5 order-lg-0 text-right">
                                                    <a href="javascript:void(0)"
                                                        onclick="removeFromCartView(event, {{ $cartItem['id'] }})"
                                                        class="btn btn-icon btn-sm btn-soft-primary btn-circle">
                                                        <i class="las la-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="px-3 py-2 mb-4 border-top d-flex justify-content-between">
                                <span class="opacity-60 fs-15">{{ translate('Subtotal') }}</span>
                                @if ($flash_deal_status)
                                    @php
                                        $deal_total = floatval($discountedPrice) + floatval($discountedPrice3);
                                    @endphp
                                    <span class="fw-600 fs-17">{{ single_price($deal_total) }}</span>
                                @else
                                    <span class="fw-600 fs-17">{{ single_price($total) }}</span>
                                @endif
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-6 text-center text-md-left order-1 order-md-0">
                                    <a href="{{ route('home') }}" class="btn btn-link">
                                        <i class="las la-arrow-left"></i>
                                        {{ translate('Return to shop') }}
                                    </a>
                                </div>
                                <div class="col-md-6 text-center text-md-right">
                                    @if (Auth::check())
                                        <a href="{{ route('checkout.shipping_info') }}" class="btn btn-primary fw-600">
                                            {{ translate('Continue to Shipping') }}
                                        </a>
                                    @else
                                        <button class="btn btn-primary fw-600"
                                            onclick="showCheckoutModal()">{{ translate('Continue to Shipping') }}</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-xl-8 mx-auto">
                        <div class="shadow-sm bg-white p-4 rounded">
                            <div class="text-center p-3">
                                <i class="las la-frown la-3x opacity-60 mb-3"></i>
                                <h3 class="h4 fw-700">{{ translate('Your Cart is empty') }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

@endsection

@section('modal')
    <div class="modal fade" id="login-modal">
        <div class="modal-dialog modal-dialog-zoom">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-600">{{ translate('Login') }}</h6>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-3">
                        <form class="form-default" role="form" action="{{ route('cart.login.submit') }}"
                            method="POST">
                            @csrf
                            <div class="form-group">
                                @if (
                                    \App\Addon::where('unique_identifier', 'otp_system')->first() != null &&
                                        \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                    <input type="text"
                                        class="form-control h-auto form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                        value="{{ old('email') }}" placeholder="{{ translate('Email Or Phone') }}"
                                        name="email" id="email">
                                @else
                                    <input type="email"
                                        class="form-control h-auto form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}"
                                        value="{{ old('email') }}" placeholder="{{ translate('Email') }}"
                                        name="email">
                                @endif
                                @if (
                                    \App\Addon::where('unique_identifier', 'otp_system')->first() != null &&
                                        \App\Addon::where('unique_identifier', 'otp_system')->first()->activated)
                                    <span class="opacity-60">{{ translate('Use country code before number') }}</span>
                                @endif
                            </div>

                            <div class="form-group">
                                <input type="password" name="password" class="form-control h-auto form-control-lg"
                                    placeholder="{{ translate('Password') }}">
                            </div>

                            <div class="row mb-2">
                                <div class="col-6">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <span class=opacity-60>{{ translate('Remember Me') }}</span>
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                                <div class="col-6 text-right">
                                    <a href="{{ route('password.request') }}"
                                        class="text-reset opacity-60 fs-14">{{ translate('Forgot password?') }}</a>
                                </div>
                            </div>

                            <div class="mb-5">
                                <button type="submit"
                                    class="btn btn-primary btn-block fw-600">{{ translate('Login') }}</button>
                            </div>
                        </form>

                    </div>
                    <div class="text-center mb-3">
                        <p class="text-muted mb-0">{{ translate('Dont have an account?') }}</p>
                        <a href="{{ route('user.registration') }}">{{ translate('Register Now') }}</a>
                    </div>
                    @if (get_setting('google_login') == 1 || get_setting('facebook_login') == 1 || get_setting('twitter_login') == 1)
                        <div class="separator mb-3">
                            <span class="bg-white px-3 opacity-60">{{ translate('Or Login With') }}</span>
                        </div>
                        <ul class="list-inline social colored text-center mb-3">
                            @if (get_setting('facebook_login') == 1)
                                <li class="list-inline-item">
                                    <a href="{{ route('social.login', ['provider' => 'facebook']) }}" class="facebook">
                                        <i class="lab la-facebook-f"></i>
                                    </a>
                                </li>
                            @endif
                            @if (get_setting('google_login') == 1)
                                <li class="list-inline-item">
                                    <a href="{{ route('social.login', ['provider' => 'google']) }}" class="google">
                                        <i class="lab la-google"></i>
                                    </a>
                                </li>
                            @endif
                            @if (get_setting('twitter_login') == 1)
                                <li class="list-inline-item">
                                    <a href="{{ route('social.login', ['provider' => 'twitter']) }}" class="twitter">
                                        <i class="lab la-twitter"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function removeFromCartView(e, key) {
            e.preventDefault();
            removeFromCart(key);
        }

        function updateQuantity(key, element) {
            $.post('{{ route('cart.updateQuantity') }}', {
                _token: AIZ.data.csrf,
                id: key,
                quantity: element.value
            }, function(data) {
                updateNavCart(data.nav_cart_view, data.cart_count);
                $('#cart-summary').html(data.cart_view);
            });
        }

        function showCheckoutModal() {
            $('#login-modal').modal();
        }
    </script>
@endsection
