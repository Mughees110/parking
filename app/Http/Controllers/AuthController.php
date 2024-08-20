<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Company;
use App\Models\Referal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Mail;
class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            if(empty($request->json('name'))||empty($request->json('email'))||empty($request->json('password'))||empty($request->json('role'))){
                return response()->json([
                    'message' => 'All fields required',
                ], 422);
            }
            DB::beginTransaction();

            $user=new User;
            $user->name=$request->json('name');
            $user->email=$request->json('email');
            $user->password=Hash::make($request->json('password'));
            $user->role=$request->json('role');
            $user->code=time();
            $user->save();
            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return response()->json(['token' => $token, 'user' => $user]);

        } catch (\Exception $e) {
            Log::error('User registration failed: ' . $e->getMessage());

            DB::rollBack();
            
            return response()->json([
                'message' => 'User registration failed'.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            if(empty($request->json('email'))||empty($request->json('password'))){
                return response()->json([
                    'message' => 'All fields required',
                ], 422);
            }
            $user = User::where('email', $request->json('email'))->first();
            if (!$user || !Hash::check($request->json('password'), $user->password)) {
                return response()->json([
                    'message' => 'The credentials are invalid',
                ], 422);
            }
            if(!empty($user->companyId)){
                $user->setAttribute('company',Company::find($user->companyId));
            }
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json(['token' => $token, 'user' => $user]);

        } catch (\Exception $e) {
            // Log the error
            Log::error('User Login failed: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'message' => 'User login failed'.$e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    

    /**
     * Logout the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function forgotPassword(ForgotPassword $request){
        try {

            $validatedData = $request->validated();
            $email=$validatedData['email'];
            $exists=User::where('email',$validatedData['email'])->exists();
            if($exists==false){
                return response()->json([
                    'message' => 'Email does not belong to any user',
                ], 422);
            }
            $otp=rand(1111,8888);
            Mail::send('mail',['otp'=>$opt], function($message) use($email){
                     $message->to($email)->subject('BUS');
                     $message->from('bloodfor@blood-for-life.com');
                    });
            
            return response()->json(['otp' => $otp]);

        } catch (\Exception $e) {
            // Log the error
            Log::error('Forgot Password Failed: ' . $e->getMessage());
            // Return a JSON response with an error message
            return response()->json([
                'message' => 'Unable to send email to your email address '.$e->getMessage(),
            ], 422);
        }
    }
    public function changePassword(ChangePassword $request){
        try {
            DB::beginTransaction();

            $validatedData = $request->validated();
            $exists=User::where('email',$validatedData['email'])->exists();
            if($exists==false){
                return response()->json([
                    'message' => 'Email does not belong to any user',
                ], 422);
            }
            $user=User::where('email',$validatedData['email'])->first();
            $user->password=Hash::make($validatedData['password']);
            $user->save();

            DB::commit();
            return response()->json(['message' => 'Password changed Successfully']);

        } catch (\Exception $e) {
            // Log the error
            Log::error('Change Password Failed: ' . $e->getMessage());
            // Return a JSON response with an error message
            DB::rollBack();
            return response()->json([
                'message' => 'Unable to change password'.$e->getMessage(),
            ], 422);
        }
    }
    public function referal(Request $request){
        if(empty($request->json('userId'))||empty($request->json('code'))){
            return response()->json(['status'=>401,'message'=>'userId code are required']);
        }
        $user=User::where('code',$request->json('code'))->first();
        if(!$user){
            return response()->json(['status'=>401,'message'=>'code does not belong to any user']);
        }
        $exists=Referal::where('userId',$request->json('userId'))->where('referedById',$user->id)->exists();
        if($exists==true){
            return response()->json(['status'=>401,'message'=>'code already used by this user']);
        }
        DB::beginTransaction();
        $referal=new Referal;
        $referal->userId=$request->json('userId');
        $referal->referedById=$user->id;
        $referal->save();
        $user->balance=$user->balance+10;
        $user->save();
        DB::commit();
        return response()->json(['status'=>200,'message'=>'success']);
    }
    
}
