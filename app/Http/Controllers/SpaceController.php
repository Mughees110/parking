<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Space;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class SpaceController extends Controller
{
    public function index(Request $request)
    {
        $spaces=Space::all();
        foreach ($spaces as $key => $value) {
        	$value->setAttribute('user',User::find($value->userId));
        }
        
        return response()->json(['status'=>200,'data'=>$spaces]);
    }
    public function index2(Request $request)
    {
        $spaces=Space::where('userId',$request->json('userId'))->get();
        
        return response()->json(['status'=>200,'data'=>$spaces]);
    }
    public function store(Request $request)
    {
        
        try {
            
            
            DB::beginTransaction();
            
            $space=new Space;
            $space->latitude=$request->json('latitude');
            $space->longitude=$request->json('longitude');
            $story->userId=$request->json('userId');
            $story->address=$request->json('address');
            $story->time=$request->json('time');
            $story->date=$request->json('date');
            $story->save();
            DB::commit();

            return response()->json(['status'=>200,'data'=>$space,'message'=>'Stored successfully']);

        } catch (\Exception $e) {
            Log::error('Space store failed: ' . $e->getMessage());

            DB::rollBack();
            
            return response()->json([
                'message' => 'Space store failed'.$e->getMessage(),
            ], 422);
        }
    }
    public function update(Request $request, string $id)
    {
        try {
            if(empty($id)){
                return response()->json(['status'=>401,'message'=>'id required']);
            }
            $space=Space::find($id);
            if(!$space){
                return response()->json(['status'=>401,'message'=>'space not exists']);
            }
            DB::beginTransaction();
            if($request->json('latitude')){
                $space->latitude=$request->json('latitude');
            
            }
            if($request->json('longitude')){
            	$space->longitude=$request->json('longitude');
            }
            if($request->json('userId')){
                $space->userId=$request->json('userId');
            }
            if($request->json('address')){
                $space->address=$request->json('address');
            }
            if($request->json('date')){
                $space->date=$request->json('date');
            }
            if($request->json('time')){
                $space->time=$request->json('time');
            }
            $space->save();
            DB::commit();

            return response()->json(['status'=>200,'data'=>$space,'message'=>'Updated successfully']);

        } catch (\Exception $e) {
            Log::error('Space update failed: ' . $e->getMessage());

            DB::rollBack();
            
            return response()->json([
                'message' => 'Space update failed'.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            if(empty($id)){
                return response()->json(['status'=>401,'message'=>'id required']);
            }
            $space=Space::find($id);
            if(!$space){
                return response()->json(['status'=>401,'message'=>'story not exists']);
            }
              
            $space->delete();
          
            return response()->json(['status'=>200,'message'=>'Deleted successfully']);

        } catch (\Exception $e) {
            Log::error('Space delete failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Space delete failed'.$e->getMessage(),
            ], 422);
        }
    }
}

