<div class="card border-0 shadow-sm rounded">
    <div class="card-header">
        <h3 class="fs-16 fw-600 mb-0">{{ translate('Summary') }}</h3>
        <div class="text-right">
            <span class="badge badge-inline badge-primary">
                {{ count($carts) }}
                {{ translate('Items') }}
            </span>
        </div>
    </div>

    <div class="card-body">
        @if (
            \App\Addon::where('unique_identifier', 'club_point')->first() != null &&
                \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
            @php
                $total_point = 0;
            @endphp
            @foreach ($carts as $key => $cartItem)
                @php
                    $product = \App\Product::find($cartItem['product_id']);
                    $total_point += $product->earn_point * $cartItem['quantity'];
                @endphp
            @endforeach

            <div class="rounded px-2 mb-2 bg-soft-primary border-soft-primary border">
                {{ translate('Total Club point') }}:
                <span class="fw-700 float-right">{{ $total_point }}</span>
            </div>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th class="product-name">{{ translate('Product') }}</th>
                    <th class="product-total text-right">{{ translate('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $subtotal = 0;
                    $tax = 0;
                    $shipping = 0;
                    $product_shipping_cost = 0;
                    $shipping_region = $shipping_info['city'];
                @endphp
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
                    $totalProducts = count($deal_2_product);
                    $totalProducts_3 = count($deal_3_product);
                    if ($totalProducts > 0) {
                        $avoidOffer = false;
                        /**offer for buying two product on flash deal*/
                        usort($deal_2_product, function ($a, $b) {
                            return $a['price'] - $b['price'];
                        });
                        foreach ($deal_3_product as $v) {
                            $quantity = $v['quantity'];
                            if ($quantity > 1) {
                                $avoidOffer = true;
                                break;
                            }
                        }
                        // dd($avoidOffer);
                    
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
                        // dd($discountedPrice);
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
                                $discountedPrice3 += $v[$k]['price'] * $v[$k]['quantity'];
                            }
                        }
                    }
                @endphp
                {{-- end flash deal --}}

                @foreach ($carts as $key => $cartItem)
                    @php
                        $product = \App\Product::find($cartItem['product_id']);
                        $subtotal += $cartItem['price'] * $cartItem['quantity'];
                        $tax += $cartItem['tax'] * $cartItem['quantity'];
                        $product_shipping_cost = $cartItem['shipping_cost'];
                        
                        $shipping += $product_shipping_cost;
                        
                        $product_name_with_choice = $product->getTranslation('name');
                        if ($cartItem['variant'] != null) {
                            $product_name_with_choice = $product->getTranslation('name') . ' - ' . $cartItem['variant'];
                        }
                    @endphp
                    <tr class="cart_item">
                        <td class="product-name">
                            {{ $product_name_with_choice }}
                            <strong class="product-quantity">
                                × {{ $cartItem['quantity'] }}
                            </strong>
                        </td>
                        <td class="product-total text-right">
                            <span
                                class="pl-4 pr-0">{{ single_price($cartItem['price'] * $cartItem['quantity']) }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="table">

            <tfoot>
                <tr class="cart-subtotal">
                    <th>{{ translate('Subtotal') }}</th>
                    <td class="text-right">
                        <span class="fw-600">{{ single_price($subtotal) }}</span>
                    </td>
                </tr>

                <tr class="cart-shipping">
                    <th>{{ translate('Tax') }}</th>
                    <td class="text-right">
                        <span class="font-italic">{{ single_price($tax) }}</span>
                    </td>
                </tr>

                <tr class="cart-shipping">
                    <th>{{ translate('Total Shipping') }}</th>
                    <td class="text-right">
                        <span class="font-italic">{{ single_price($shipping) }}</span>
                    </td>
                </tr>

                @if (Session::has('club_point'))
                    <tr class="cart-shipping">
                        <th>{{ translate('Redeem point') }}</th>
                        <td class="text-right">
                            <span class="font-italic">{{ single_price(Session::get('club_point')) }}</span>
                        </td>
                    </tr>
                @endif

                @if ($carts->sum('discount') > 0)
                    <tr class="cart-shipping">
                        <th>{{ translate('Coupon Discount') }}</th>
                        <td class="text-right">
                            <span class="font-italic"
                                id="shipping_charges">{{ single_price($carts->sum('discount')) }}</span>
                        </td>
                    </tr>
                @endif

                @php
                    $total = $subtotal + $tax + $shipping;
                    if ($flash_deal_status) {
                        $deal_total = floatval($discountedPrice) + floatval($discountedPrice3);
                        $total = $deal_total + $tax + $shipping;
                    }
                    if (Session::has('club_point')) {
                        $total -= Session::get('club_point');
                    }
                    if ($carts->sum('discount') > 0) {
                        $total -= $carts->sum('discount');
                    }
                @endphp
                @if ($flash_deal_status)
                    <tr class="cart-total">
                        <th><mark>#flash deal</mark></th>
                    </tr>
                @endif

                <tr class="cart-total">
                    <th><span class="strong-600">{{ translate('Total') }}</span></th>
                    <td class="text-right">
                        <strong><span>{{ single_price($total) }}</span></strong>
                    </td>
                </tr>
            </tfoot>
        </table>

        @if (
            \App\Addon::where('unique_identifier', 'club_point')->first() != null &&
                \App\Addon::where('unique_identifier', 'club_point')->first()->activated)
            @if (Session::has('club_point'))
                <div class="mt-3">
                    <form class="" action="{{ route('checkout.remove_club_point') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="input-group">
                            <div class="form-control">{{ Session::get('club_point') }}</div>
                            <div class="input-group-append">
                                <button type="submit"
                                    class="btn btn-primary">{{ translate('Remove Redeem Point') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            @else
                {{-- @if (Auth::user()->point_balance > 0)
                    <div class="mt-3">
                        <p>
                            {{translate('Your club point is')}}:
                            @if (isset(Auth::user()->point_balance))
                                {{ Auth::user()->point_balance }}
                            @endif
                        </p>
                        <form class="" action="{{ route('checkout.apply_club_point') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <input type="text" class="form-control" name="point" placeholder="{{translate('Enter club point here')}}" required>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">{{translate('Redeem')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif --}}
            @endif
        @endif

        @if (Auth::check() && get_setting('coupon_system') == 1)
            @if ($carts[0]['discount'] > 0)
                <div class="mt-3">
                    <form class="" id="remove-coupon-form" action="{{ route('checkout.remove_coupon_code') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="owner_id" value="{{ $carts[0]['owner_id'] }}">
                        <div class="input-group">
                            <div class="form-control">{{ $carts[0]['coupon_code'] }}</div>
                            <div class="input-group-append">
                                <button type="button" id="coupon-remove"
                                    class="btn btn-primary">{{ translate('Change Coupon') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            @else
                <div class="mt-3">
                    <form class="" id="apply-coupon-form" action="{{ route('checkout.apply_coupon_code') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="owner_id" value="{{ $carts[0]['owner_id'] }}">
                        <div class="input-group">
                            <input type="text" class="form-control" name="code"
                                placeholder="{{ translate('Have coupon code? Enter here') }}" required>
                            <div class="input-group-append">
                                <button type="button" id="coupon-apply"
                                    class="btn btn-primary">{{ translate('Apply') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        @endif

    </div>
</div>
