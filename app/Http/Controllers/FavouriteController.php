<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Space;
use App\Models\Favourite;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class FavouriteController extends Controller
{
    public function makeFavourite(Request $request){
    	if(empty($request->json('userId'))||empty($request->json('spaceId'))){
    		return response()->json(['status'=>401,'message'=>'userId and spaceId are required']);
    	}
    	try {
            
            $existingF=Favourite::where('userId',$request->json('userId'))->where('spaceId',$request->json('spaceId'))->exists();
            if($existingF==true){
            	$fav=Favourite::where('userId',$request->json('userId'))->where('spaceId',$request->json('spaceId'))->first();
            	$fav->delete();
            	return response()->json(['status'=>200,'message'=>'Removed from Favourite list']);
            }
            DB::beginTransaction();
            
            $favourite=new Favourite;
            $favourite->spaceId=$request->json('spaceId');
            $favourite->userId=$request->json('userId');

            $favourite->save();
            DB::commit();

            return response()->json(['status'=>200,'message'=>'Added to favourite list successfully']);

        } catch (\Exception $e) {
            Log::error('Favourite store failed: ' . $e->getMessage());

            DB::rollBack();
            
            return response()->json([
                'message' => 'Favourite store failed'.$e->getMessage(),
            ], 422);
        }
    }
    public function getMyFavouriteSpaces(Request $request){
    	$favs=Favourite::where('userId',$request->json('userId'))->get();
    	foreach ($favs as $key => $value) {
    		$value->setAttribute('space',Space::find($value->spaceId));
    	}
    	return response()->json(['status'=>200,'data'=>$favs]);
    }
}
