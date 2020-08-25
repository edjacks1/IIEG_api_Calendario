<?php

namespace App\Http\Controllers\Catalogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventTag;

class EventTagController extends Controller
{
     /**
     * @OA\Get(
     *     path="/eventTag",
     *     tags={"EventTag"},
     *     summary="List event tags",
     *     operationId="listeventTag",
     *     description="Returns data of event tags",
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
        $types= EventTag::all();

        if(!is_null($types)){
            return response()->json(['status'=>true, 'data' => $types ]);
        }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'Data not found']);
        }     
    }

    /**
     * @OA\Get(
     *     path="/eventTag/{id}",
     *     tags={"EventTag"},
     *     summary="Show event tag",
     *     operationId="showeventTag",
     *     description="Returns data of Event tag",
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
     *          description="Event Tag id",
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
        $type= EventTag::select(['name','color'])->find($id);
        if(!is_null($type))
        {
            return response()->json(['status'=>true, 'data' => $type]);
        }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'Event Tag not found']);
        }
    }

    /**
     * @OA\Post(
     *     path="/eventTag",
     *     tags={"EventTag"},
     *     summary="Store Event Tag",
     *     operationId="storeeventTag",
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
     *                   description="Nombre de la etiqueta del evento",
     *                   type="string",
     *                    ), 
     *                 @OA\Property(
     *                   property="color",
     *                   description="Color en Hexadecimal asignado a la etiqueta",
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
                'name' => 'required|max:50|unique:event_tag',
                'color' => 'required'
            ]);
    
            $input = $request->all();

            $type = new EventTag();
            $type->name = $input['name'];
            $type->color = $input['color'];
            $type->save();

    
            return response()->json([
                'status' => true,
                'message' => 'Event Tag Created Succesfully',
                'data' =>[ 'id' => $type->id ]
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
     *     path="/eventTag/{id}",
     *     tags={"EventTag"},
     *     summary="Update event tag",
     *     operationId="updateeventTag",
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
     *          description="Event tag id",
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
     *                     description="Nombre de la etiqueta del evento",
     *                     type="string",
     *                      ), 
     *                     @OA\Property(
     *                     property="color",
     *                     description="Color en Hexadecimal asignado a la etiqueta",
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
                'name' => 'required|unique:event_tag,name,'.$id,
                'color' => 'required'
            ]);

            $type = EventTag::where('id', '=' , $id)->first();

            if(!is_null($type))
            {
                $type->name = $request->post('name');
                $type->color = $request->post('color');
                
                $type->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Event Tag Updated Succesfully'
                ]);
            }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'Event Tag not found']);
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
     *      path="/eventTag/{id}",
     *      operationId="deleteeventTag",
     *      tags={"EventTag"},
     *      summary="Delete existing event tag",
     *      description="Deletes a record and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="Event tag id",
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
     *          description="Event Tag Not Found"
     *      ),
     *      security={
     *         {"authorization": {}}
     *     }
     * )
     */

    public function destroy($id)
    {
        try {
            EventTag::destroy($id);
            return response()->json(['status' => true,'message' => 'Event tag Deleted Succesfully']);
        } catch (\Throwable $th) {
            
            return response()->json([
                'status' => false,
                'message' => 'Error Ocurred',
                'data' => $th->getMessage()
            ],400);
        }
        
    }

}
