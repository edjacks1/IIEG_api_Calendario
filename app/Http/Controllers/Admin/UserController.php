<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /**
     * @OA\Get(
     *     path="/user",
     *     tags={"User"},
     *     summary="List users",
     *     operationId="List",
     *     description="Returns data of users",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *     security={
     *         {"authorization": {}}
     *     }
     * )
     */
    public function index(Request $request)
    {
            $users= User::with(['organization','roles.role'])->get();
            if(!is_null($users))
            {
                return response()->json(['status'=>true, 'data' => $users]);
            }
            else{
                return response()->json(['status'=>false, 'data' => [], 'message' => 'Data not found']);
            }
         
    }

    /**
     * @OA\Get(
     *     path="/user/{id}",
     *     tags={"User"},
     *     summary="Show User",
     *     operationId="Show",
     *     description="Returns data of user",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *     @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     security={
     *         {"authorization": {}}
     *     }
     * )
     */
    public function show($id)
    {
        $user= User::select(['name','email'])->find($id);
        if(!is_null($user))
        {
            return response()->json(['status'=>true, 'data' => $user]);
        }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'User not found']);
        }
    }

    /**
     * @OA\Post(
     *     path="/user",
     *     tags={"User"},
     *     summary="Store User",
     *     operationId="Store",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *     @OA\RequestBody(
     *         description="Input data format",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                  required={"name","last_name","email","organization_id","password"},
     * *                 @OA\Property(
     *                     property="name",
     *                     description="nombre de usuario",
     *                     type="string",
     *                      ), 
     *                     @OA\Property(
     *                     property="second_name",
     *                     description="segundo nombre",
     *                     type="string",
     *                      ),
     *                     @OA\Property(
     *                     property="last_name",
     *                     description="Apellido paterno",
     *                     type="string",
     *                      ), 
     *                     @OA\Property(
     *                     property="maternal_surname",
     *                     description="Apellido materno",
     *                     type="string",
     *                      ),                              
     *                      @OA\Property(
     *                     property="email",
     *                     description="usuario del sistema",
     *                     type="string",
     *                      ),
     *                     @OA\Property(
     *                     property="phone",
     *                     description="Teléfono",
     *                     type="string",
     *                      ), 
     *                    @OA\Property(
     *                     property="password",
     *                     description="contraseña de usuario",
     *                     type="string"
     *                    ),
     *                     @OA\Property(
     *                     property="organization_id",
     *                     description="id Organización",
     *                     type="integer",
     *                    ) 
     *             )
     *         )
     *     ),
     *      security={
     *         {"authorization": {}}
     *     }
     * )
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required|max:45',
                'last_name' => 'required|max:45',
                'second_name' => 'string|max:45',
                'maternal_surname' => 'string|max:45',
                'email' => 'required|email|unique:user',
                'organization_id'=> 'required',
                'password' => [
                    'required',
                    'string',
                    'min:8',             // must be at least 10 characters in length
                    'regex:/[a-z]/',      // must contain at least one lowercase letter
                    'regex:/[A-Z]/',      // must contain at least one uppercase letter
                    'regex:/[0-9]/',      // must contain at least one digit
                    'regex:/[@$!%*#?&]/', // must contain a special character
                ]
            ]);
    
            $input = $request->all();
            $input['password'] = Hash::make($request->password);

            $user = new User();
            $user->name = $input['name'];
            $user->email = $input['email'];
            $user->password = $input['password'];
            $user->last_name = $input['last_name'];
            $user->second_name = $input['second_name'];
            $user->maternal_surname = $input['maternal_surname'];
            $user->phone = $input['phone'];
            $user->organization_id = $input['organization_id'];

            $user->save();

    
            return response()->json([
                'status' => true,
                'message' => 'User Created Succesfully',
                'data' =>[ 'id' => $user->id ]
            ]);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => $th->response
            ],400);
        }

        
    }

    /**
     * @OA\Put(
     *     path="/user/{id}",
     *     tags={"User"},
     *     summary="Update User",
     *     operationId="Update",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\RequestBody(
     *         description="Input data format",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name","last_name","email","organization_id"},
     *                   @OA\Property(
     *                     property="name",
     *                     description="nombre de usuario",
     *                     type="string",
     *                      ), 
     *                     @OA\Property(
     *                     property="second_name",
     *                     description="segundo nombre",
     *                     type="string",
     *                      ),
     *                     @OA\Property(
     *                     property="last_name",
     *                     description="Apellido paterno",
     *                     type="string",
     *                      ), 
     *                     @OA\Property(
     *                     property="maternal_surname",
     *                     description="Apellido materno",
     *                     type="string",
     *                      ),                              
     *                      @OA\Property(
     *                     property="email",
     *                     description="usuario del sistema",
     *                     type="string",
     *                      ),
     *                     @OA\Property(
     *                     property="phone",
     *                     description="Teléfono",
     *                     type="string",
     *                      ), 
     *                    @OA\Property(
     *                     property="password",
     *                     description="contraseña de usuario",
     *                     type="string"
     *                    ),
     *                     @OA\Property(
     *                     property="organization_id",
     *                     description="id Organización",
     *                     type="integer",
     *                    ) 
     *             )
     *         )
     *     ),
     *      security={
     *         {"authorization": {}}
     *     }
     * )
     */

    public function update($id,Request $request)
    {
        try {

            $this->validate($request, [
                'name' => 'required|max:45',
                'last_name' => 'required|max:45',
                'second_name' => 'string|max:45',
                'maternal_surname' => 'string|max:45',
                'email' => 'required|email|unique:user,email,'.$id,
                'last_name' => 'required',
                'organization_id'=> 'required',
                'password' => [
                    'string',
                    'min:8',             // must be at least 10 characters in length
                    'regex:/[a-z]/',      // must contain at least one lowercase letter
                    'regex:/[A-Z]/',      // must contain at least one uppercase letter
                    'regex:/[0-9]/',      // must contain at least one digit
                    'regex:/[@$!%*#?&]/', // must contain a special character
                ]
            ]);

            $user = User::where('id', '=' , $id)->first();

            if(!is_null($user))
            {
                $user->name = $request->post('name');
                $user->email = $request->post('email');
                $user->last_name = $request->post('last_name');
                $user->second_name = $request->post('second_name');
                $user->maternal_surname = $request->post('maternal_surname');
                $user->phone = $request->post('phone');
                $user->organization_id = $request->post('organization_id');
                if($request->post('password')!="")
                    $user->password = Hash::make($request->post('password'));

                $user->save();

                return response()->json([
                    'status' => true,
                    'message' => 'User Updated Succesfully'
                ]);
            }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'User not found']);
        }
        } catch (\Throwable $th) {
            
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => $th->response
            ],400);
        }

    }

    /**
     * @OA\Delete(
     *      path="/user/{id}",
     *      operationId="deleteUser",
     *      tags={"User"},
     *      summary="Delete existing User",
     *      description="Deletes a record and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      ),
     *      security={
     *         {"authorization": {}}
     *     }
     * )
     */

    public function destroy($id)
    {
        try {
            User::destroy($id);

            return response()->json([
                'status' => true,
                'message' => 'User Deleted Succesfully'
            ]);
        } catch (\Throwable $th) {
            
            return response()->json([
                'status' => false,
                'message' => 'Error Ocurred',
                'data' => $th->getMessage()
            ],400);
        }
        
    }

    /**
     * @OA\Put(
     *     path="/user/assignRole/{id}",
     *     tags={"User"},
     *     summary="Assing Role to User",
     *     operationId="Update Role",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\RequestBody(
     *         description="Input data format",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                  @OA\Property(
     *                    type="array",
     *                    property="roles",
     *                    description="Role Array",
     *                      @OA\Items(
     *                      type="number"
     *                    )
     *                 )
     *             )
     *         )
     *     ),
     *      security={
     *         {"authorization": {}}
     *     }
     * )
     */
    public function assignRole(Request $request, $id){
        
        try {

            $this->validate($request, [
                'roles' => 'required'
            ]);

            $user = User::where('id', $id)->first();

            if(!is_null($user))
            {
                $roles=$request->post('roles');
                
                UserRole::where('user_id',$user->id)->delete();

                foreach($roles as $role)
                {
                    $userrole= new UserRole();
                    $userrole->user_id=$user->id;
                    $userrole->role_id=$role;

                    $userrole->save();
                }

                return response()->json([
                    'status' => true,
                    'message' => 'User Updated Succesfully'
                ]);
                
            }
            else {
                return response()->json(['status'=>false, 'data' => [], 'message' => 'User not found']);
            }
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Error Ocurred',
                'data' => $th->getMessage()
            ],400);
        }

    }


    /**
     * @OA\Get(
     *     path="/user/getRole/{id}",
     *     tags={"User"},
     *     summary="Show User Roles",
     *     operationId="Show Role",
     *     description="Returns role of user",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *     @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     security={
     *         {"authorization": {}}
     *     }
     * )
     */
    public function getRole($id)
    {
        $role= UserRole::where('user_id',$id)->with('role')->get();

        if(!is_null($role))
        {
            return response()->json(['status'=>true, 'data' => $role]);
        }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'Data not found']);
        }
    }

    /**
     * @OA\Post(
     *     path="/user/checkEmail",
     *     tags={"User"},
     *     summary="Check Email available",
     *     operationId="Check Email",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *     @OA\RequestBody(
     *         description="Input data format",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email"},
     *                 @OA\Property(
    *                     property="email",
    *                     description="email de usuario",
    *                     type="string",
    *                      ), 
*                     @OA\Property(
    *                     property="id",
    *                     description="id usuario",
    *                     type="string",
    *                      )
     *             )
     *         )
     *     ),
     *      security={
     *         {"authorization": {}}
     *     }
     * )
     */
    public function checkEmail(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'email|required'
            ]);


           $email=$request->post('email');
           $id=$request->post('id');

           $user=User::where('email',$email);
           
           if(!is_null($id))
                $user->where('id','!=',$id);
            
           $user->get();
            
           if($user->count()>0)
           {
             return response()->json(['status'=>true, 'message'=>'Email is already taken']);
           }
           else{
            return response()->json(['status'=>true, 'message'=>'Email available']);
           }

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error Ocurred',
                'data' => $th->getMessage()
            ],400);
        }

    }
}
