<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Validator;
use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request) {
        $this->request = $request;
    }

    /**
     * Create a new token.
     * 
     * @param  \App\User   $user
     * @return string
     */
    protected function jwt(User $user,$permission) {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued. 
            'exp' => time() + 60*60, // Expiration time
            'data'=> $permission
        ];
        
        // As you can see we are passing `JWT_SECRET` as the second parameter that will 
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    } 

   /**
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"Auth"},
     *     summary="Login de usuario",
     *     operationId="Login",
     *      @OA\Response(
     *         response=200,
     *         description="Success Request"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     ),
     *     @OA\RequestBody(
     *         description="Input data format",
     *         required=true, 
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                  required={"email","password"},
     *                 @OA\Property(
     *                     property="email",
     *                     description="usuario del sistema",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="contraseña de usuario",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function authenticate(User $user) {
        try {
            $this->validate($this->request, [
                'email'     => 'required|email',
                'password'  => 'required'
            ]);
    
            // Find the user by email
            $user = User::where('email', $this->request->input('email'))->first();
            if (!$user) {
                return response()->json(['status'=>false, 'message' => 'Email does not exist.','data'=>[]], 400);
            }
    
            // Verify the password and generate the token
            if (Hash::check($this->request->input('password'), $user->password)) {
    
                $refresh_token = Str::random(100);
                $user->remember_token = $refresh_token;
                $user->save();
           
                $permission = DB::table('permission')
                    ->select(
                        'permission.slug as slug'
                    )
                    ->leftJoin('role_has_permission', 'role_has_permission.permission_id','=','permission.permission_id')
                    ->leftJoin('user_has_role','user_has_role.role_id','=','role_has_permission.role_id')
                    ->where('user_has_role.user_id','=',$user->id)
                    ->get()->pluck('slug');
    
                return response()->json(['status'=>true, 'message'=>'Login Success',
                'data'=> [
                    'token' => $this->jwt($user,$permission),
                    'refresh_token'=>$refresh_token,
                    'user_id' => $user->id,
                    'permissions'=>$permission]
                ], 200);
            }
    
            // Bad Request response
    
            return response()->json([ 'status'=>false,'message' => 'Email or password is wrong.', 'data'=> []], 400);
        } catch (\Throwable $th) {

            return response()->json([ 'status'=>false,'message' => $th->getMessage()], 400);
        }
        
    }

    /**
     * @OA\Post(
     *     path="/auth/validateToken",
     *     tags={"Auth"},
     *     summary="Token de usuario",
     *     operationId="ValidateToken",
     *      @OA\Response(
     *         response=200,
     *         description="Success Request"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     ),
     *     @OA\RequestBody(
     *         description="Input data format",
     *         required=true, 
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                  required={"token"},
     *                 @OA\Property(
     *                     property="token",
     *                     description="Token de usuario",
     *                     type="string",
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function validateToken(Request $request){

        try {
            $token= $request->post('token');
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);

            return response()->json(['status'=>true, 'message' => 'Token is Valid']);
        } catch(ExpiredException $e) {

            return response()->json(['status'=>false,'message' => 'Provided token is expired.'], 400);
        } catch(Exception $e) {

            return response()->json(['status'=>false,'message' => 'An error while decoding token.'], 400);

        }
    }
    
    /**
     * @OA\Post(
     *     path="/auth/refreshToken",
     *     tags={"Auth"},
     *     summary="Refrescar el token",
     *     operationId="RefreshToken",
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     ),
     *     @OA\RequestBody(
     *         description="Input data format",
     *         required=true, 
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="refresh_token",
     *                     description="Token para refrescar",
     *                     type="string",
     *                 )
     *             )
     *         )
     *     )
     * )
     */


    public function refreshToken(Request $request){
        try {

            $this->validate($this->request, [
                'refresh_token'     => 'required'
            ]);
            $refresh_token=$request->post('refresh_token');

            $user = User::where('remember_token',$refresh_token)->first();
            if(!is_null($user))
            {
                $permission = DB::table('permission')
                    ->select(
                        'permission.slug as slug'
                    )
                    ->leftJoin('role_has_permission', 'role_has_permission.permission_id','=','permission.permission_id')
                    ->leftJoin('user_has_role','user_has_role.role_id','=','role_has_permission.role_id')
                    ->where('user_has_role.user_id','=',$user->id)
                    ->get()->pluck('slug');

                return response()->json([
                    'status'=>true,
                    'message'=>'Token refresh success',
                    'token' => $this->jwt($user,$permission),
                    'refresh_token'=>$refresh_token,
                    'user_id' => $user->id,
                ], 200);  
            }
            else{
                return response()->json(['status'=>false,'message' => 'Invalid Refresh Token'], 400);
            }
            

        } catch (\Throwable $th) {
            return response()->json(['status'=>false,'message' => $th->getMessage()], 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/auth/logout",
     *     tags={"Auth"},
     *     summary="Logout",
     *     operationId="Logout",
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     ),
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="refresh_token",
     *                     description="Token para refrescar",
     *                     type="string",
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function logout(Request $request){
        try {

            $this->validate($this->request, [
                'refresh_token'     => 'required'
            ]);
            $refresh_token=$request->post('refresh_token');

            $user = User::where('remember_token',$refresh_token)->first();
            if(!is_null($user))
            {

                $user->remember_token=null;
                $user->save();

                return response()->json([
                    'status'=>true,
                    'message'=>'Logout Success'
                ], 200);  
            }
            else{
                return response()->json([
                    'status'=>false,
                    'message' => 'Invalid Refresh Token'
                    ], 400);
            }
            

        } catch (\Throwable $th) {
            return response()->json([
            'status'=>false,
            'message' => $th->getMessage()
            ], 400);

        }
    }

    public function pruebasUtf8(){
        $stringVar = 'VEHÍCULO';

        $stringlower = strtolower($stringVar);

        return response()->json(['stringVar'=>$stringVar, 'stringLower'=>$stringlower],200,['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
