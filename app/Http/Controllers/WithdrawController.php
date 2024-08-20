<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Withdraw;
use App\Models\User;
class WithdrawController extends Controller
{
    public function withdrawRequest(Request $request){
    	if(empty($request->json('userId'))||empty($request->json('amount'))){
    		return response()->json(['status'=>401,'message'=>'userId and amount are required']);
    	}
    	$user=User::find($request->json('userId'));
    	if(!$user){
    		return response()->json(['status'=>401,'message'=>'user not found']);
    	}
    	if($user->balance<$request->json('amount')){
    		return response()->json(['status'=>401,'message'=>'amount not enough to withdraw']);
    	}
    	$with=new Withdraw;
    	$with->amount=$request->json('amount');
    	$with->userId=$request->json('userId');
    	$with->status="pending";
    	$with->save();
    	return response()->json(['status'=>200,'message'=>'added successfully']);

    }
    public function getWithdrawRequests(Request $request){
    	$withs=Withdraw::all();
    	foreach ($withs as $key => $value) {
    		$value->setAttribute('user',User::find($value->userId));
    	}
    	return response()->json(['status'=>200,'data'=>$withs]);
    }
    public function getMyWithdrawRequests(Request $request){
    	$withs=Withdraw::where('userId',$request->json('userId'))->get();
    	return response()->json(['status'=>200,'data'=>$withs]);
    }
    public function changeWithdrawStatus(Request $request){
    	if(empty($request->json('withdrawId'))||empty($request->json('status'))){
    		return response()->json(['status'=>401,'message'=>'withdrawId and status are required']);
    	}
    	$with=Withdraw::find($request->json('withdrawId'));
    	if(!$with){
    		return response()->json(['status'=>401,'message'=>'request not found']);
    	}
    	$with->status=$request->json('status');
    	$with->save();
    	return response()->json(['status'=>200,'data'=>$with,'message'=>'Status updated successfully']);
    }
}
