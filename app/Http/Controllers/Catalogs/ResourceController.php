<?php

namespace App\Http\Controllers\Catalogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resource;

class ResourceController extends Controller
{
        /**
     * @OA\Get(
     *     path="/resource",
     *     tags={"Resource"},
     *     summary="List resource",
     *     operationId="List",
     *     description="Returns data of resources",
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
            $resources= Resource::all();

            if(!is_null($resources))
            {
                return response()->json(['status'=>true, 'data' => $resources]);
            }
            else{
                return response()->json(['status'=>false, 'data' => [], 'message' => 'Data not found']);
            }
         
    }

    /**
     * @OA\Get(
     *     path="/resource/{id}",
     *     tags={"Resource"},
     *     summary="Show resource",
     *     operationId="Show",
     *     description="Returns data of resource",
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
     *          description="resource id",
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
        $resource= Resource::select(['name','email'])->find($id);
        if(!is_null($resource))
        {
            return response()->json(['status'=>true, 'data' => $resource]);
        }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'Resource not found']);
        }
    }

    /**
     * @OA\Post(
     *     path="/resource",
     *     tags={"Resource"},
     *     summary="Store resource",
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
     *          required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                  required={"name","description","owner","patrimonial_id","type_id","remark"},
     * *                 @OA\Property(
     *                     property="name",
     *                     description="Nombre de el recurso",
     *                     type="string",
     *                      ), 
     *                     @OA\Property(
     *                     property="description",
     *                     description="Descripcion del recurso",
     *                     type="string",
     *                      ),
     *                  @OA\Property(
     *                     property="owner",
     *                     description="Dueño",
     *                     type="integer"
     *                 ),
     *                  @OA\Property(
     *                     property="patrimonial_id",
     *                     description="Id patrimonio",
     *                     type="string"
     *                 ),
     *                  @OA\Property(
     *                     property="type_id",
     *                     description="Tipo de recurso",
     *                     type="integer"
     *                 ),
     *                  @OA\Property(
     *                     property="remark",
     *                     description="Remark",
     *                     type="string"
     *                 )
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
                'name' => 'required|max:45|unique:resource',
                'description' => 'required',
                'owner'=> 'required',
                'patrimonial_id' =>'string|max:45|required',
                'type_id'=>'integer|required',
                'remark' => 'string|required'
            ]);
    
            $input = $request->all();

            $resource = new Resource();
            $resource->name = $input['name'];
            $resource->description = $input['description'];
            $resource->owner = $input['owner'];
            $resource->patrimonial_id = $input['patrimonial_id'];
            $resource->type_id = $input['type_id'];
            $resource->remark = $input['remark'];
            $resource->status=1;

            $resource->save();

    
            return response()->json([
                'status' => true,
                'message' => 'Resource Created Succesfully',
                'data' =>[ 'id' => $resource->id ]
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
     *     path="/resource/{id}",
     *     tags={"Resource"},
     *     summary="Update resource",
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
     *          description="resource id",
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
     *                 required={"name","description","owner","patrimonial_id","type_id","remark"}, 
     * *                 @OA\Property(
     *                     property="name",
     *                     description="Nombre de el recurso",
     *                     type="string",
     *                      ), 
     *                     @OA\Property(
     *                     property="description",
     *                     description="Descripcion del recurso",
     *                     type="string",
     *                      ),
     *                  @OA\Property(
     *                     property="owner",
     *                     description="Dueño",
     *                     type="integer"
     *                 ),
     *                  @OA\Property(
     *                     property="patrimonial_id",
     *                     description="Id patrimonio",
     *                     type="string"
     *                 ),
     *                  @OA\Property(
     *                     property="type_id",
     *                     description="Tipo de recurso",
     *                     type="integer"
     *                 ),
     *                  @OA\Property(
     *                     property="remark",
     *                     description="Remark",
     *                     type="string"
     *                 )
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
                'name' => 'required|unique:resource,name,'.$id,
                'description' => 'required',
                'owner'=> 'required',
                'patrimonial_id' =>'string|max:45|required',
                'type_id'=>'integer|required',
                'remark' => 'string|required'
            ]);

            $resource = Resource::where('id', '=' , $id)->first();

            if(!is_null($resource))
            {
                $resource->name = $request->post('name');
                $resource->description = $request->post('description');
                $resource->owner = $request->post('owner');
                $resource->patrimonial_id = $request->post('patrimonial_id');
                $resource->type_id = $request->post('type_id');
                $resource->remark = $request->post('remark');
                
                $resource->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Resource Updated Succesfully'
                ]);
            }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'Resource not found']);
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
     *      path="/resource/{id}",
     *      operationId="deleteresource",
     *      tags={"Resource"},
     *      summary="Delete existing resource",
     *      description="Deletes a record and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="resource id",
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
            $resource=Resource::find($id);

            $resource->status=0;
            $resource->save();

            return response()->json([
                'status' => true,
                'message' => 'Resource Deleted Succesfully'
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
