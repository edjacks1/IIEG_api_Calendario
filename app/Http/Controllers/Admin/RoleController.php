<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RolePermission;

class RoleController extends Controller
{
     /**
     * @OA\Get(
     *     path="/role",
     *     tags={"Role"},
     *     summary="List Roles",
     *     operationId="List",
     *     description="Returns data of roles",
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
    public function index()
    {
        $roles= Role::with('permissions.permission')->get();

        if(!is_null($roles))
        {
            return response()->json(['status'=>true, 'data' => $roles]);
        }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'Data not found']);
        }
    }

  
    /**
     * @OA\Post(
     *     path="/role",
     *     tags={"Role"},
     *     summary="Store Role",
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
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
    * *                 @OA\Property(
    *                     property="name",
    *                     description="role name",
    *                     type="string",
    *                      ),                
    *                   @OA\Property(
    *                     property="description",
    *                     description="role description",
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
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required'
            ]);
    
            $role = new Role();
            $role->name = $request->name;
            $role->description = ($request->description) ? $request->description : "";

            $role->save();

    
            return response()->json([
                'status' => true,
                'message' => 'Role Created Succesfully',
                'data' =>[ 'id' => $role->role_id]
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
     * @OA\Get(
     *     path="/role/{id}",
     *     tags={"Role"},
     *     summary="Show Role",
     *     operationId="Show",
     *     description="Returns data of role",
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
     *          description="Role id",
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
        $role= Role::find($id);

        if(!is_null($role))
        {
            return response()->json(['status'=>true, 'data' => $role]);
        }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'Role not found']);
        }
    }

    
    /**
     * @OA\Put(
     *     path="/role/{id}",
     *     tags={"Role"},
     *     summary="Update Role",
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
     *          description="Role id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                  @OA\Property(
     *                    property="name",
     *                    description="Role Name",
     *                    type="string",
     *                     ),                
     *                  @OA\Property(
     *                    property="description",
     *                    description="Role description",
     *                    type="string",
     *                     )
     *             )
     *         )
     *     ),
     *      security={
     *         {"authorization": {}}
     *     }
     * )
     */
    public function update(Request $request, $id)
    {
        try {

            $this->validate($request, [
                'name' => 'required'
            ]);

            $role = Role::where('role_id', '=' , $id)->first();

            if(!is_null($role))
            {
                $role->name = $request->post('name');
                $role->description = ($request->post('description')) ? $request->post('description') : "";
                
                $role->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Role Updated Succesfully'
                ]);
            }
            else{
                return response()->json(['status'=>false, 'data' => [], 'message' => 'Role not found']);
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
     * @OA\Delete(
     *      path="/role/{id}",
     *      operationId="deleteRole",
     *      tags={"Role"},
     *      summary="Delete existing Role",
     *      description="Deletes a record and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="Role id",
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
            Role::destroy($id);
            return response()->json(['status' => true,'message' => 'Role Deleted Succesfully']);
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
     *     path="/role/assignPermission/{id}",
     *     tags={"Role"},
     *     summary="Assing Permission to Role",
     *     operationId="Update Permissions",
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
     *          description="Role id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                  @OA\Property(
     *                    type="array",
     *                    property="permissions",
     *                    description="Permissions Array",
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
    public function assignPermission(Request $request, $id){
        
        try {

            $this->validate($request, [
                'permissions' => 'required'
            ]);

            $role = Role::where('role_id', '=' , $id)->first();

            if(!is_null($role))
            {
                $permissions=$request->post('permissions');
                RolePermission::where('role_id',$role->role_id)->delete();
                
                foreach($permissions as $permission)
                {
                    $rolepermission= new RolePermission();
                    $rolepermission->role_id=$role->role_id;
                    $rolepermission->permission_id=$permission;

                    $rolepermission->save();
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Role Updated Succesfully'
                ]);
                
            }
            else {
                return response()->json(['status'=>false, 'data' => [], 'message' => 'Role not found']);
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
     *     path="/role/getPermission/{id}",
     *     tags={"Role"},
     *     summary="Show Role Permission",
     *     operationId="Show Permission",
     *     description="Returns permissions of role",
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
     *          description="Role id",
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
    public function getPermission($id)
    {
        $role= RolePermission::where('role_id',$id)->with('permission')->get();

        if(!is_null($role))
        {
            return response()->json(['status'=>true, 'data' => $role]);
        }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'Data not found']);
        }
    }
}
