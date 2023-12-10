<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Session;
use App\Http\Controllers\CustomerPackageController;
use Auth;
use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use Illuminate\Support\Facades\Log;

class PaytmController extends Controller
{
    public function paytmPayment($order_id, $amount, $user, $phone, $email)
    {        
        $payment = PaytmWallet::with('receive');

        $payment->prepare([
            'order' => $order_id . '_payttm1',
            'user' => $user,
            'mobile_number' => $phone,
            'email' => $email,
            'amount' => $amount,
            'callback_url' => route('paytm.callback'),
        ]);
        return $payment->receive();
    }


    /**
     * Obtain the payment information.
     *
     * @return Object
     */
    public function paytmCallback(Request $request)
    {
        try {            
            $transaction = PaytmWallet::with('receive');
            $response = $transaction->response();               
            if ($transaction->isSuccessful()) {
                $order_data = $transaction->getOrderId();
                $order_id = $this->extractOrderid($order_data);                
                // if ($request->session()->has('payment_type')) {
                //     $paymentType = $request->session()->get('payment_type');
                //     if ($paymentType == 'cart_payment') {
                        $checkoutController = new CheckoutController;
                        return $checkoutController->checkout_done($order_id, json_encode($response));
                    // } elseif ($paymentType == 'wallet_payment') {
                    //     $walletController = new WalletController;
                    //     return $walletController->wallet_payment_done($request->session()->get('payment_data'), json_encode($response));
                    // } elseif ($paymentType == 'customer_package_payment') {
                    //     $customer_package_controller = new CustomerPackageController;
                    //     return $customer_package_controller->purchase_payment_done($request->session()->get('payment_data'), json_encode($response));
                    // }
                // }
            } elseif ($transaction->isFailed()) {
                return view('frontend.paytm.paytmfail', compact('response'));
            } elseif ($transaction->isOpen()) {
                return view('frontend.paytm.paytmfail');
            }

            $transaction->getResponseMessage();
            $transaction->getOrderId();
            $transaction->getTransactionId();
        } catch (\Exception $e) {
            Log::error("File:" . $e->getFile() . " Line: " . $e->getLine() . " Message: " . $e->getMessage());
            flash(translate('Payment failed'))->error();
            return redirect()->route('home');
        }
    }

    public function extractOrderid($orderString){        
        $pattern = '/\d+/'; // This pattern matches one or more digits
        if (preg_match($pattern, $orderString, $matches)) {
            $extractedNumber = $matches[0];
            return $extractedNumber;
        } else {
            return $orderString;
        }
    }
}
