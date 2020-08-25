<?php

namespace App\Http\Controllers\Catalogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Place;

class PlaceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/place",
     *     tags={"Place"},
     *     summary="List place",
     *     operationId="List",
     *     description="Returns data of places",
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
        $places= Place::with('organization')->get();

        if(!is_null($places))
        {
            return response()->json(['status'=>true, 'data' => $places]);
        }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'Data not found']);
        }    
    }

    /**
     * @OA\Get(
     *     path="/place/{id}",
     *     tags={"Place"},
     *     summary="Show place",
     *     operationId="Show",
     *     description="Returns data of place",
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
     *          description="place id",
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
        $place= Place::select(['name','email'])->find($id);
        if(!is_null($place))
        {
            return response()->json(['status'=>true, 'data' => $place]);
        }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'Place not found']);
        }
    }

    /**
     * @OA\Post(
     *     path="/place",
     *     tags={"Place"},
     *     summary="Store place",
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
     *                 required={"name","description"},
     * *                 @OA\Property(
     *                     property="name",
     *                     description="Nombre de el lugar",
     *                     type="string",
     *                      ), 
     *                     @OA\Property(
     *                     property="description",
     *                     description="Descripcion del lugar",
     *                     type="string",
     *                      ),
     *                  @OA\Property(
     *                     property="x",
     *                     description="Longitud de posición",
     *                     type="number"
     *                 ),
     *                  @OA\Property(
     *                     property="y",
     *                     description="Latitud de posición",
     *                     type="number"
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
                'name'            => 'required|max:50|unique:place',
                'description'     => 'max:150|required',
                'x'               => 'numeric',
                'y'               => 'numeric',
                'organization_id' => 'required|integer'
            ]);
    
            $input = $request->all();

            $place                   = new Place();
            $place->name             = $input['name'];
            $place->description      = $input['description'];
            $place->x                = $input['x'];
            $place->y                = $input['y'];
            $place->organization_id  = $input['organization_id'];

            $place->save();

    
            return response()->json([
                'status' => true,
                'message' => 'Place Created Succesfully',
                'data' =>[ 'id' => $place->id ]
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
     *     path="/place/{id}",
     *     tags={"Place"},
     *     summary="Update place",
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
     *          description="place id",
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
     *                     description="Nombre de el lugar",
     *                     type="string",
     *                      ), 
     *                     @OA\Property(
     *                     property="description",
     *                     description="Descripcion del lugar",
     *                     type="string",
     *                      ),
     *                  @OA\Property(
     *                     property="x",
     *                     description="Longitud de posición",
     *                     type="number"
     *                 ),
     *                  @OA\Property(
     *                     property="y",
     *                     description="Latitud de posición",
     *                     type="number"
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
                'name'            => 'required|max:50|unique:place,name,'.$id,
                'description'     => 'max:150|required',
                'x'               => 'numeric',
                'y'               => 'numeric',
                'organization_id' => 'required|integer'
            ]);

            $place = Place::where('id', '=' , $id)->first();

            if(!is_null($place))
            {
                $place->name             = $request->post('name');
                $place->description      = $request->post('description');
                $place->x                = $request->post('x');
                $place->y                = $request->post('y');
                $place->organization_id  = $request->post('organization_id');
                $place->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Place Updated Succesfully'
                ]);
            }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'place not found']);
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
     *      path="/place/{id}",
     *      operationId="deleteplace",
     *      tags={"Place"},
     *      summary="Delete existing place",
     *      description="Deletes a record and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="place id",
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

    public function destroy($id){
        try {
            Place::destroy($id);
            
            return response()->json(['status' => true,'message' => 'Place Deleted Succesfully']);
        } catch (\Throwable $th) {
            
            return response()->json([
                'status' => false,
                'message' => 'Error Ocurred',
                'data' => $th->getMessage()
            ],400);
        }
        
    }
}
