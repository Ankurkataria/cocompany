<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\OrderDetail;
use App\Models\Product;
use App\Review;
use Auth;
use DB;

class PurchaseHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::where('user_id', Auth::user()->id)->orderBy('code', 'desc')->paginate(9);
        return view('frontend.user.purchase_history', compact('orders'));
    }

    public function digital_index()
    {
        $orders = DB::table('orders')
                        ->orderBy('code', 'desc')
                        ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                        ->join('products', 'order_details.product_id', '=', 'products.id')
                        ->where('orders.user_id', Auth::user()->id)
                        ->where('products.digital', '1')
                        ->where('order_details.payment_status', 'paid')
                        ->select('order_details.id')
                        ->paginate(15);
        return view('frontend.user.digital_purchase_history', compact('orders'));
    }

    public function purchase_history_details(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->delivery_viewed = 1;
        $order->payment_status_viewed = 1;
        $order->save();
        return view('frontend.user.order_details_customer', compact('order'));
    }
    
        public function customer_write_review_purchase_details(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->delivery_viewed = 1;
        $order->payment_status_viewed = 1;
        $order->save();
        return view('frontend.user.customer_write_review', compact('order'));
    }

    public function customer_write_review_store(Request $request)
    {
        try
        {  
            $review = new Review;

           $totalProduct = count( $_POST['productId']);
           $Product = $_POST['productId'];
           $totalRating = $_POST['totalRating'];
           $comments = $_POST['comments'];
            
            $finalData = [];
            for($i=0; $i<$totalProduct; $i++)
            {
                if(!empty($totalRating[$i]))
                {
                    $rowData['rating'] = $totalRating[$i];
                    $rowData['comment'] = $comments[$i];
                    $rowData['product_id'] = $Product[$i];
                    $rowData['order_code'] = $_POST['orderCode'];
                    $rowData['user_id'] = Auth::user()->id;
                    array_push($finalData,$rowData);
                }
                
            }
            
            Review::insert($finalData);
            
            $getAllProductRatings = DB::table('reviews')
            ->selectRaw('(SUM(rating) /count(id)) as rating, product_id')
            ->whereIn('product_id', $Product)
            ->groupBy('product_id')
            ->get();
            
            if(!empty($getAllProductRatings))
            {
                foreach($getAllProductRatings as $reviewProduct)
                {
                   $updateProductRating =[
                        'rating' => $reviewProduct->rating,
                   ];

                   $updateProductRatingCondition = [
                    'id' => $reviewProduct->product_id,
                   ];

                   $isProductRatingUpdated = Product::where($updateProductRatingCondition)->update($updateProductRating);
                }

            }

            //update order is_reviewed column on star giving by user

            Order::where('code', $_POST['orderCode'])->update(['is_reviewed' => '1']);


            
            flash(translate('Rating saved successfully'))->success();
    	    return back();
            
        }
        catch (\Exception $e) {
            flash(translate('Failed to save rating'))->error();
            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
