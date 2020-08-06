<?php

namespace App\Http\Controllers\Catalogs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ResourceType;

class ResourceTypeController extends Controller
{
     /**
     * @OA\Get(
     *     path="/resourceType",
     *     tags={"ResourceType"},
     *     summary="List resource type",
     *     operationId="listResourceType",
     *     description="Returns data of resource types",
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
            $types= ResourceType::all();

            if(!is_null($types))
            {
                return response()->json(['status'=>true, 'data' => $types]);
            }
            else{
                return response()->json(['status'=>false, 'data' => [], 'message' => 'Data not found']);
            }
         
    }

    /**
     * @OA\Get(
     *     path="/resourceType/{id}",
     *     tags={"ResourceType"},
     *     summary="Show resource type",
     *     operationId="showResourceType",
     *     description="Returns data of resource type",
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
     *          description="resourceType id",
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
        $resource= ResourceType::select(['name','description'])->find($id);
        if(!is_null($resource))
        {
            return response()->json(['status'=>true, 'data' => $resource]);
        }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'Resource type not found']);
        }
    }

    /**
     * @OA\Post(
     *     path="/resourceType",
     *     tags={"ResourceType"},
     *     summary="Store resource type",
     *     operationId="storeResourceType",
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
     *                 required={"name","description"},
     * *               @OA\Property(
     *                   property="name",
     *                   description="Nombre de el tipo de recurso",
     *                   type="string",
     *                    ), 
     *                 @OA\Property(
     *                   property="description",
     *                   description="Descripcion del tipo de recurso",
     *                   type="string",
     *                   )
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
                'name' => 'required|max:45|unique:resource_type',
                'description' => 'required|max:150'
            ]);
    
            $input = $request->all();

            $resource = new ResourceType();
            $resource->name = $input['name'];
            $resource->description = $input['description'];
            $resource->status=1;

            $resource->save();

    
            return response()->json([
                'status' => true,
                'message' => 'Resource Type Created Succesfully',
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
     *     path="/resourceType/{id}",
     *     tags={"ResourceType"},
     *     summary="Update resource type",
     *     operationId="updateResourceType",
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
     *          description="Resource type id",
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
     *                 required={"name","description"}, 
     * *                 @OA\Property(
     *                     property="name",
     *                     description="Nombre de el tipo de recurso",
     *                     type="string",
     *                      ), 
     *                     @OA\Property(
     *                     property="description",
     *                     description="Descripcion del tipo de recurso",
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

    public function update($id,Request $request)
    {
        try {

            $this->validate($request, [
                'name' => 'required|max:45|unique:resource_type,name,'.$id,
                'description' => 'required|max:150'
            ]);

            $resource = ResourceType::where('id', '=' , $id)->first();

            if(!is_null($resource))
            {
                $resource->name = $request->post('name');
                $resource->description = $request->post('description');
                
                $resource->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Resource Type Updated Succesfully'
                ]);
            }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'Resource Type not found']);
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
     *      path="/resourceType/{id}",
     *      operationId="deleteResourceType",
     *      tags={"ResourceType"},
     *      summary="Delete existing resource type",
     *      description="Deletes a record and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="resource type id",
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
     *          description="Resource Type Not Found"
     *      ),
     *      security={
     *         {"authorization": {}}
     *     }
     * )
     */

    public function destroy($id)
    {
        try {
            $resource=ResourceType::find($id);

            $resource->status=0;
            $resource->save();

            return response()->json([
                'status' => true,
                'message' => 'Resource Type Deleted Succesfully'
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
