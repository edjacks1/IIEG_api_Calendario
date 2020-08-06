<?php

namespace App\Http\Controllers\Catalogs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\OrganizationPlace;

class OrganizationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/organization",
     *     tags={"Organization"},
     *     summary="List organization",
     *     operationId="List",
     *     description="Returns data of organizations",
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
            $organizations= Organization::with('places.place')->get();

            if(!is_null($organizations))
            {
                return response()->json(['status'=>true, 'data' => $organizations]);
            }
            else{
                return response()->json(['status'=>false, 'data' => [], 'message' => 'Data not found']);
            }
         
    }

    /**
     * @OA\Get(
     *     path="/organization/{id}",
     *     tags={"Organization"},
     *     summary="Show organization",
     *     operationId="Show",
     *     description="Returns data of organization",
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
     *          description="organization id",
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
        $organization= organization::select(['name','email'])->find($id);
        if(!is_null($organization))
        {
            return response()->json(['status'=>true, 'data' => $organization]);
        }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'organization not found']);
        }
    }

    /**
     * @OA\Post(
     *     path="/organization",
     *     tags={"Organization"},
     *     summary="Store organization",
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
     *                 required={"name","abbreviation","email"},
     * *                 @OA\Property(
     *                     property="name",
     *                     description="Nombre de la organización",
     *                     type="string",
     *                      ), 
     *                     @OA\Property(
     *                     property="abbreviation",
     *                     description="Abreviatura",
     *                     type="string",
     *                      ),
     *                    @OA\Property(
     *                     property="phone",
     *                     description="Telefono",
     *                     type="string",
     *                      ), 
     *                   @OA\Property(
     *                     property="email",
     *                     description="Email",
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
                'name' => 'required',
                'abbreviation' => 'required',
                'phone' => 'numeric',
                'email' => 'email|required|unique:organization',
                'x'=> 'numeric',
                'y' =>'numeric'
            ]);
    
            $input = $request->all();

            $organization = new organization();
            $organization->name = $input['name'];
            $organization->abbreviation = $input['abbreviation'];
            $organization->phone = $input['phone'];
            $organization->email = $input['email'];
            $organization->x = $input['x'];
            $organization->y = $input['y'];
            $organization->status=1;

            $organization->save();

    
            return response()->json([
                'status' => true,
                'message' => 'Organization Created Succesfully',
                'data' =>[ 'id' => $organization->id ]
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
     *     path="/organization/{id}",
     *     tags={"Organization"},
     *     summary="Update organization",
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
     *          description="organization id",
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
     *                 required={"name","abbreviation","email"},
     * *                 @OA\Property(
     *                     property="name",
     *                     description="Nombre de la organización",
     *                     type="string",
     *                      ), 
     *                     @OA\Property(
     *                     property="abbreviation",
     *                     description="Abreviatura",
     *                     type="string",
     *                      ),
     *                    @OA\Property(
     *                     property="phone",
     *                     description="Telefono",
     *                     type="string",
     *                      ), 
     *                   @OA\Property(
     *                     property="email",
     *                     description="Email",
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
                'name' => 'required',
                'abbreviation' => 'required',
                'phone' => 'numeric',
                'email' => 'required|email|unique:organization,email,'.$id,
                'x'=> 'numeric',
                'y' =>'numeric'
            ]);

            $organization = Organization::where('id', '=' , $id)->first();

            if(!is_null($organization))
            {
                $organization->name = $request->post('name');
                $organization->abbreviation = $request->post('abbreviation');
                $organization->phone = $request->post('phone');
                $organization->email = $request->post('email');
                $organization->x = $request->post('x');
                $organization->y = $request->post('y');
                
                $organization->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Organization Updated Succesfully'
                ]);
            }
        else{
            return response()->json(['status'=>false, 'data' => [], 'message' => 'organization not found']);
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
     *      path="/organization/{id}",
     *      operationId="deleteorganization",
     *      tags={"Organization"},
     *      summary="Delete existing organization",
     *      description="Deletes a record and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="organization id",
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
            $organization=Organization::find($id);

            $organization->status=0;
            $organization->save();

            return response()->json([
                'status' => true,
                'message' => 'Organization Deleted Succesfully'
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
     *     path="/organization/assignPlace/{id}",
     *     tags={"Organization"},
     *     summary="Assing Place to Organization",
     *     operationId="Update Organization",
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
     *          description="Organization id",
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
     *                    property="places",
     *                    description="Places Array",
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
    public function assignPlace(Request $request, $id){
        
        try {

            $this->validate($request, [
                'places' => 'required'
            ]);

            $organization = Organization::where('id', $id)->first();

            if(!is_null($organization))
            {
                $places=$request->post('places');
                OrganizationPlace::where('organization_id',$organization->id)->delete();
                
                foreach($places as $place)
                {
                    $organizationplace= new OrganizationPlace();
                    $organizationplace->organization_id=$organization->id;
                    $organizationplace->place_id=$place;

                    $organizationplace->save();
                }

                return response()->json([
                    'status' => true,'message' => 'Organization Updated Succesfully'
                    ]);
                
            }
            else {
                return response()->json(['status'=>false, 'data' => [], 'message' => 'Organization not found']);
            }
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,'message' => 'Error Ocurred','data' => $th->getMessage()
                ],400);
        }

    }
}
