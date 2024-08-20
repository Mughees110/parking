<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Space;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function bookSpace(Request $request){
    	if(empty($request->json('userId'))||empty($request->json('spaceId'))){
    		return response()->json(['status'=>401,'message'=>'userId and spaceId are required']);
    	}
    	try {
            $exists=Booking::where('spaceId',$request->json('spaceId'))->exists();
            if($exists==true){
            	return response()->json(['status'=>401,'message'=>'already booked']);
            }
            
            DB::beginTransaction();
            
            $booking=new Booking;
            $booking->spaceId=$request->json('spaceId');
            $booking->userId=$request->json('userId');

            $booking->save();
            DB::commit();

            return response()->json(['status'=>200,'data'=>$booking,'message'=>'Stored successfully']);

        } catch (\Exception $e) {
            Log::error('Booking store failed: ' . $e->getMessage());

            DB::rollBack();
            
            return response()->json([
                'message' => 'Booking store failed'.$e->getMessage(),
            ], 422);
        }
    }
    public function getBookings(Request $request){
    	$bookings = Booking::all();
    	foreach ($bookings as $key => $value) {
    		$value->setAttribute('user',User::find($value->userId));
    		$value->setAttribute('space',Space::find($value->spaceId));
    	}
    	return response()->json(['status'=>200,'data'=>$bookings]);
    }
    public function getMyBookings(Request $request){
    	$bookings = Booking::where('userId',$request->json('userId'))->get();
    	foreach ($bookings as $key => $value) {
    		$value->setAttribute('user',User::find($value->userId));
    		$value->setAttribute('space',Space::find($value->spaceId));
    	}
    	return response()->json(['status'=>200,'data'=>$bookings]);
    }
}
