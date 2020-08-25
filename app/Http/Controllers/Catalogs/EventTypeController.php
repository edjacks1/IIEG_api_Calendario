<?php

namespace App\Http\Controllers\Catalogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventType;

class EventTypeController extends Controller
{
     /**
     * @OA\Get(
     *     path="/eventType",
     *     tags={"EventType"},
     *     summary="List event type",
     *     operationId="listeventType",
     *     description="Returns data of event types",
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
            $types= EventType::all();

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
     *     path="/eventType/{id}",
     *     tags={"EventType"},
     *     summary="Show event type",
     *     operationId="showeventType",
     *     description="Returns data of event type",
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
     *          description="Event type id",
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
        $event= EventType::select(['name','description'])->find($id);
        if(!is_null($event))
        {
            return response()->json(['status'=>true, 'data' => $event]);
        }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'Event type not found']);
        }
    }

    /**
     * @OA\Post(
     *     path="/eventType",
     *     tags={"EventType"},
     *     summary="Store Event type",
     *     operationId="storeeventType",
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
     *                   description="Nombre de el tipo de evento",
     *                   type="string",
     *                    ), 
     *                 @OA\Property(
     *                   property="description",
     *                   description="Descripción del tipo de evento",
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
                'name' => 'required|max:45|unique:event_type',
                'description' => 'required|max:150'
            ]);
    
            $input = $request->all();

            $event = new EventType();
            $event->name = $input['name'];
            $event->description = $input['description'];
            $event->save();

    
            return response()->json([
                'status' => true,
                'message' => 'Event Type Created Succesfully',
                'data' =>[ 'id' => $event->id ]
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
     *     path="/eventType/{id}",
     *     tags={"EventType"},
     *     summary="Update event type",
     *     operationId="updateeventType",
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
     *          description="Event type id",
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
     *                     description="Nombre de el tipo de evento",
     *                     type="string",
     *                      ), 
     *                     @OA\Property(
     *                     property="description",
     *                     description="Descripcion del tipo de evento",
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
                'name' => 'required|unique:event_type,name,'.$id,
                'description' => 'required|max:150'
            ]);

            $event = EventType::where('id', '=' , $id)->first();

            if(!is_null($event))
            {
                $event->name = $request->post('name');
                $event->description = $request->post('description');
                
                $event->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Event Type Updated Succesfully'
                ]);
            }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'Event Type not found']);
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
     *      path="/eventType/{id}",
     *      operationId="deleteeventType",
     *      tags={"EventType"},
     *      summary="Delete existing event type",
     *      description="Deletes a record and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="event type id",
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
     *          description="Event Type Not Found"
     *      ),
     *      security={
     *         {"authorization": {}}
     *     }
     * )
     */

    public function destroy($id)
    {
        try {
            EventType::destroy($id);
            return response()->json(['status' => true,'message' => 'Event type Deleted Succesfully']);
        } catch (\Throwable $th) {
            
            return response()->json([
                'status' => false,
                'message' => 'Error Ocurred',
                'data' => $th->getMessage()
            ],400);
        }
        
    }

}
