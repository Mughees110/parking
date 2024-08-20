<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getSecret(Request $request){
        if(empty($request->json('amount'))||empty($request->json('currency'))){
                return response()->json(['status'=>401,'message'=>'amount and currency is required']);
            }
        \Stripe\Stripe::setApiKey( 'sk_test_51OpEgCCeBWY8hNUKvq3E74zefTzcrB9Qt3xHMFfdhVQnuGkfZwNG3Z5VVGoxOpSertqCVPKx0c2FA7PcdQOCU2Gv00YH2z9j7v');

        $intent = \Stripe\PaymentIntent::create([
          'amount' => $request->json('amount'),
          'currency' => $request->json('currency'),
        ]);
        $client_secret = $intent->client_secret;
        return response()->json(['status'=>200,'data'=>$client_secret]);

    }
}
