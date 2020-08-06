<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Permission;

class PermissionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/permission",
     *     tags={"Permission"},
     *     summary="List permissions",
     *     operationId="List",
     *     description="Returns data of permissions",
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
        $permissions= Permission::all();

        if(!is_null($permissions))
        {
            return response()->json(['status'=>true, 'data' => $permissions]);
        }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'Data not found']);
        }
    }

  
    /**
     * @OA\Post(
     *     path="/permission",
     *     tags={"Permission"},
     *     summary="Store permission",
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
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name","slug"},
     *                 @OA\Property(
     *                     property="name",
     *                     description="permission name",
     *                     type="string",
     *                      ),   
     *                    @OA\Property(
     *                     property="slug",
     *                     description="slug name",
     *                     type="string",
     *                      ),                
     *                   @OA\Property(
     *                     property="description",
     *                     description="permission description",
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
                'name' => 'required',
                'slug' => 'required|unique:permission'
            ]);
    
            $permission = new Permission();
            $permission->name = $request->name;
            $permission->slug = $request->slug;
            $permission->description = ($request->description) ? $request->description : "";

            $permission->save();

    
            return response()->json([
                'status' => true,
                'message' => 'Permission Created Succesfully',
                'data' =>[ 'id' => $permission->permission_id]
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
     *     path="/permission/{id}",
     *     tags={"Permission"},
     *     summary="Show permission",
     *     operationId="Show",
     *     description="Returns data of permission",
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
     *          description="Permission id",
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
        $permission= Permission::find($id);

        if(!is_null($permission))
        {
            return response()->json(['status'=>true, 'data' => $permission]);
        }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'permission not found']);
        }
    }

    
    /**
     * @OA\Put(
     *     path="/permission/{id}",
     *     tags={"Permission"},
     *     summary="Update permission",
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
     *          description="Permission id",
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
     *                 required={"name","slug"},
     *                  @OA\Property(
     *                    property="name",
     *                    description="permission Name",
     *                    type="string",
     *                     ),   
     *                   @OA\Property(
     *                    property="slug",
     *                    description="slug name",
     *                    type="string",
     *                    ),                 
     *                  @OA\Property(
     *                    property="description",
     *                    description="permission description",
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
                'name' => 'required',
                'slug' => 'required|unique:permission,slug,'.$id.',permission_id',
            ]);

            $permission = Permission::where('permission_id', '=' , $id)->first();

            if(!is_null($permission))
            {
                $permission->name = $request->post('name');
                $permission->slug = $request->post('slug');
                $permission->description = ($request->post('description')) ? $request->post('description') : "";
                
                $permission->save();

                return response()->json([
                    'status' => true,
                    'message' => 'permission Updated Succesfully'
                ]);
            }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'permission not found']);
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
     *      path="/permission/{id}",
     *      operationId="deletepermission",
     *      tags={"Permission"},
     *      summary="Delete existing permission",
     *      description="Deletes a record and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="permission id",
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
            Permission::destroy($id);

            return response()->json([
                'status' => true,
                'message' => 'permission Deleted Succesfully'
            ]);
        } catch (\Throwable $th) {
            
            return response()->json([
                'status' => false,
                'message' => 'Error Ocurred',
                'data' => $th->getMessage()
            ],400);
        }
    }
}
