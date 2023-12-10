
<style>
    .la-star{
        font-size : 25px !important;
        align-content : center;
    }

    .starRatingParentDiv .highlight, .starRatingParentDiv .selected {
    color: #F4B30A;
}
</style>
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{ translate('Order id')}}: {{ $order->code }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>

@php
    $status = $order->orderDetails->first()->delivery_status;
    $refund_request_addon = \App\Addon::where('unique_identifier', 'refund_request')->first();
@endphp

<div class="modal-body gry-bg px-3 pt-3">
    <div class="row">
        <div class="col-lg-12">
            <div class="card mt-4">
                <div class="card-header">
                  <b class="fs-15">{{ translate('Order Details') }}</b>
                </div>
                <div class="card-body pb-0 table-responsive">
                    <form action="{{ route('customer.write_review_store') }}" method="post" id="customerRatingForm">
                        @csrf
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ translate('Product')}}</th>
                                    <th>{{ translate('Star')}}</th>
                                    <th>{{ translate('Comments')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderDetails as $key => $orderDetail)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>
                                            @if ($orderDetail->product != null)
                                                <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank">{{ $orderDetail->product->getTranslation('name') }}</a>
                                            @else
                                                <strong>{{  translate('Product Unavailable') }}</strong>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="hidden" name="totalRating[]" id="totalRating_{{$orderDetail->product->id}}" value="" />
                                            <input type="hidden" name="productId[]" value="{{$orderDetail->product->id}}" />
                                            <input type="hidden" name="orderCode" value="{{ $order->code }}" />
                                            
                                            <div class="rating rating-sm mt-1 starRatingParentDiv">
                                            @for ($i = 1; $i <= 5; $i ++) 
                                            
                                                <i class = "las la-star" aria-hidden="true" id="st_{{$orderDetail->product->id}}" onmouseover="highlightStar(this, {{$orderDetail->product->id}})" onclick="addRating(this, {{$orderDetail->product->id}})" title="Click on star to slect"></i>

                                            @endfor
                                                
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group row">
                                                <div class="col-md-10">
                                                    <textarea class="form-control" placeholder="{{ translate('Your Comments')}}" name="comments[]"></textarea>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="form-group mb-0 text-right">
                                    <button type="submit" class="btn btn-primary" id="storeCustomerRating" disabled>Submit Review</button>
                                </div>
                    </form>    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function highlightStar(obj, id) 
    {
        removeHighlight(id);
        $('.starRatingParentDiv #st_' + id ).each(function(index) {
            $(this).addClass("highlight");
            if (index == $('.starRatingParentDiv #st_' + id).index(obj)) {
                return false;
            }
        });
    };

    function removeHighlight(id) {
        $('.starRatingParentDiv #st_' + id).removeClass('selected');
        $('.starRatingParentDiv #st_' + id).removeClass('highlight');
    }
    function addRating(obj, id) {
       
        $('.starRatingParentDiv #st_' + id).each(function(index) {
            $(this).removeClass('highlight');
            $(this).addClass('selected');
            $("#storeCustomerRating").removeAttr('disabled');
            $('#totalRating_' + id).val((index + 1));

            if (index == $('.starRatingParentDiv #st_' + id).index(obj)) {
                return false;
            }
        });
        // $.ajax({
        //     url: "add-rating-ajax.php",
        //     data: 'id=' + id + '&rating=' + $('#tutorial-' + id + ' #rating').val(),
        //     type: "POST",
        //     success: function() {
        //         $(obj).parent().find("#loader-icon").hide();
        //     }
        // });
    }
 </script>
