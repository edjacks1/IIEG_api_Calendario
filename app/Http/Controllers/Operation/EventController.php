<?php

namespace App\Http\Controllers\Operation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventGuest;
use App\Models\EventOrganizer;
use App\Models\EventResource;
use App\Models\Place;
use DateInterval;
use DateTime;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{

    function __construct() {
        $this->events = new Event;
    }

    public function getAvailablePlaces(Request $request){
        try{
            $this->validate($request, [
                'start_at'        => 'required|date_format:Y-m-d H:i:s',
                'end_at'          => 'required|date_format:Y-m-d H:i:s',
            ]);
            $places = Place::whereNotIn('id',$this->events->getNotAvaiblePlacesIDs($request->start_at, $request->end_at))->get();
            return response()->json(['status' => true, 'data' => $places]);
        }catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error Ocurred',
                'data' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/event/getEvents",
     *     tags={"Event"},
     *     summary="List Events",
     *     operationId="List Events",
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
     *                 required={"start_at","end_at"},
     * *                 @OA\Property(
     *                   property="start_at",
     *                   description="Event Start",
     *                   type="string",
     *                   ),
     * *                 @OA\Property(
     *                   property="end_at",
     *                   description="Event End",
     *                   type="string",
     *                   ),
     * *                 @OA\Property(
     *                   property="created_by",
     *                   description="Created User Id",
     *                   type="integer",
     *                   ),
     * *                 @OA\Property(
     *                   property="type",
     *                   description="Event Type Id",
     *                   type="integer",
     *                   ),
     * *                 @OA\Property(
     *                   property="guest",
     *                   description="Guest Id",
     *                   type="integer",
     *                   ),
     * *                 @OA\Property(
     *                   property="organizer",
     *                   description="Organizer Id",
     *                   type="integer",
     *                   ),
     * *                 @OA\Property(
     *                   property="resources_check",
     *                   description="Resources check",
     *                   type="integer",
     *                   ),
     *              )
     *            )
     *          ),
     *      security={
     *         {"authorization": {}}
     *     }
     * )
     */
    public function index(Request $request){
        try {
            $this->validate($request, [
                'start_at'        => 'required|date_format:Y-m-d H:i:s',
                'end_at'          => 'required|date_format:Y-m-d H:i:s',
            ]);

            $events = Event::with('tag')->whereBetween('start_at', [$request->start_at, $request->end_at]);

            if( !is_null($request->place) && sizeof($request->place) > 0){
                $events->whereIn('place_id', $request->place);
            }
            if( !is_null($request->organizer) && sizeof($request->organizer) > 0){
                $events->join('event_organizer', 'event_organizer.event_id', '=', 'event.id')->whereIn('event_organizer.user_id', $request->organizer);
            }
            if( !is_null($request->type) && sizeof($request->type) > 0 ){
                $events->whereIn('type', $request->type);
            }

            $events->get();

            if ($events->count() > 0) {
                return response()->json(['status' => true, 'data' => $events->get()]);
            } else {
                return response()->json(['status' => false, 'data' => [], 'message' => 'Data not found']);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error Ocurred',
                'data' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/event/{id}",
     *     tags={"Event"},
     *     summary="Show Event",
     *     operationId="Show",
     *     description="Returns data of a Event",
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
     *          description="Event id",
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
        // $event = Event::with(['place.organization','creator','guests.guest', 'organizers.organizer', 'resources.resource', 'type', 'tag'])->find($id);
        $event = Event::with(['place.organization','creator','guests.guest', 'organizers.organizer', 'resources.resource', 'tag'])->find($id);
        return response()->json(['status' => true, 'data' => $event]);
        if (!is_null($event)) {
            return response()->json(['status' => true, 'data' => $event]);
        } else {
            return response()->json(['status' => false, 'data' => [], 'message' => 'Events not found']);
        }
    }

    /**
     * @OA\Post(
     *     path="/event",
     *     tags={"Event"},
     *     summary="Create New Event",
     *     operationId="Create Event",
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
     *                 required={"name","description","place_id","start_at","end_at","created_by","type","assistants"},
     *                 @OA\Property(
     *                   property="name",
     *                   description="Event name",
     *                   type="string",
     *                   ),
     * *                 @OA\Property(
     *                   property="description",
     *                   description="Description name",
     *                   type="string",
     *                   ),
     * *                 @OA\Property(
     *                   property="place_id",
     *                   description="Place Id",
     *                   type="integer",
     *                   ),
     * *                 @OA\Property(
     *                   property="start_at",
     *                   description="Event Start",
     *                   type="string",
     *                   ),
     * *                 @OA\Property(
     *                   property="end_at",
     *                   description="Event End",
     *                   type="string",
     *                   ),
     * *                 @OA\Property(
     *                   property="created_by",
     *                   description="Created User Id",
     *                   type="integer",
     *                   ),
     * *                @OA\Property(
     *                   property="type",
     *                   description="Event Type Id",
     *                   type="integer",
     *                   ),
     *                  @OA\Property(
     *                   property="tag",
     *                   description="Event Tag Id",
     *                   type="integer",
     *                   ),
     *                  @OA\Property(
     *                    type="array",
     *                    property="organizers",
     *                    description="Organizers Array",
     *                      @OA\Items(
     *                      type="integer"
     *                      )
     *                  ),
     *                  @OA\Property(
     *                    type="array",
     *                    property="guests",
     *                    description="Guests Array",
     *                      @OA\Items(
     *                      type="integer"
     *                      )
     *                   ),
     *                  @OA\Property(
     *                    type="array",
     *                    property="resources",
     *                    description="Resources Array",
     *                      @OA\Items(
     *                      type="integer"
     *                   )
     *                 )
     *              )
     *            )
     *          ),
     *      security={
     *         {"authorization": {}}
     *     }
     * )
     */
    public function store(Request $request)
    {

        try {
            $this->validate($request, [
                'name'        => 'required|max:50',
                'description' => 'required|max:150',
                'place_id'    => 'required|integer',
                'start_at'    => 'required|date_format:Y-m-d H:i:s',
                'end_at'      => 'required|date_format:Y-m-d H:i:s',
                'type'        => 'required|integer',
                'tag'         => 'required|integer',
                'guests'      => 'required',
                'resources'   => 'required'
            ]);

            $input = $request->all();

            $notAvaiblePlaces = $this->events->getNotAvaiblePlacesIDs($request->start_at, $request->end_at);
                
            if(in_array($input['place_id'],(array)$notAvaiblePlaces)){
                return response()->json(['status' => false,'message' => "Place with ID: $request->place_id is not avaible." ]);
            }
            
            $event              = new Event();
            $event->name        = $input['name'];
            $event->description = $input['description'];
            $event->place_id    = $input['place_id'];
            $event->start_at    = $input['start_at'];
            $event->end_at      = $input['end_at'];
            $event->created_by  = $request->auth->id;
            $event->type        = $input['type'];
            $event->tag         = $input['tag'];
            $event->status      = 1;
            $event->save();

            ///Ciclos para registrar asistentes y recursos
            if (!is_null($event->id)) {
                $organizers = $request->post('organizers') ? $request->post('organizers') : [];
                foreach ($organizers as $organizerId) {
                    $organizer = new EventOrganizer();
                    $organizer->event_id = $event->id;
                    $organizer->user_id = $organizerId;
                    $organizer->save();
                }

                $guests = $request->post('guests');
                foreach ($guests as $guestId) {
                    $guest = new EventGuest();
                    $guest->event_id = $event->id;
                    $guest->guest_id = $guestId;
                    $guest->save();
                }

                $resources = $request->post('resources') ? $request->post('resources') : [];
                foreach ($resources as $resourceId) {
                    $resource = new EventResource();
                    $resource->event_id = $event->id;
                    $resource->resource_id = $resourceId;
                    $resource->save();
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Event Created Succesfully',
                'data' => ['id' => $event->id]
            ]);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => (isset($th->response)) ? $th->response : []
            ], 400);
        }
    }


    /**
     * @OA\Put(
     *     path="/event/{id}",
     *     tags={"Event"},
     *     summary="Update Event",
     *     operationId="Update Event",
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
     *        @OA\Parameter(
     *          name="id",
     *          description="Event id",
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
     *                 required={"name","description","place_id","start_at","end_at","created_by","type","assistants"},
     *                 @OA\Property(
     *                   property="name",
     *                   description="Event name",
     *                   type="string",
     *                   ),
     * *                 @OA\Property(
     *                   property="description",
     *                   description="Description name",
     *                   type="string",
     *                   ),
     * *                 @OA\Property(
     *                   property="place_id",
     *                   description="Place Id",
     *                   type="integer",
     *                   ),
     * *                 @OA\Property(
     *                   property="start_at",
     *                   description="Event Start",
     *                   type="string",
     *                   ),
     * *                 @OA\Property(
     *                   property="end_at",
     *                   description="Event End",
     *                   type="string",
     *                   ),
     * *                 @OA\Property(
     *                   property="created_by",
     *                   description="Created User Id",
     *                   type="integer",
     *                   ),
     * *                @OA\Property(
     *                   property="type",
     *                   description="Event Type Id",
     *                   type="integer",
     *                   ),
     * *                @OA\Property(
     *                   property="tag",
     *                   description="Event Tag Id",
     *                   type="integer",
     *                   ),
     *                  @OA\Property(
     *                    type="array",
     *                    property="organizers",
     *                    description="Organizers Array",
     *                      @OA\Items(
     *                      type="integer"
     *                      )
     *                  ),
     *                  @OA\Property(
     *                    type="array",
     *                    property="guests",
     *                    description="Guests Array",
     *                      @OA\Items(
     *                      type="integer"
     *                      )
     *                   ),
     *                  @OA\Property(
     *                    type="array",
     *                    property="resources",
     *                    description="Resources Array",
     *                      @OA\Items(
     *                      type="integer"
     *                   )
     *                 )
     *              )
     *            )
     *          ),
     *      security={
     *         {"authorization": {}}
     *     }
     * )
     */
    public function update($id, Request $request)
    {

        try {
            $this->validate($request, [
                'name' => 'required|max:50',
                'description' => 'required|max:150',
                'place_id' => 'required|integer',
                'start_at' => 'required|date_format:Y-m-d H:i:s',
                'end_at' => 'required|date_format:Y-m-d H:i:s',
                'created_by' => 'required|integer',
                'type' => 'required|integer',
                'tag' => 'required|integer',
                'guests' => 'required'
            ]);

            $event = Event::where('id', $id)->get();
            if (!is_null($event)) {
                $input = $request->all();

                $event->name = $input['name'];
                $event->description = $input['description'];
                $event->place_id = $input['place_id'];
                $event->start_at = $input['start_at'];
                $event->end_at = $input['end_at'];
                $event->created_by = $input['created_by'];
                $event->type = $input['type'];
                $event->tag = $input['tag'];
                $event->status = 1;
                $event->save();

                ///Ciclos para registrar asistentes y recursos
                $organizers = $request->post('organizers') ? $request->post('organizers') : [];
                EventOrganizer::where('event_id', $event->id)->delete();
                foreach ($organizers as $organizerId) {
                    $organizer = new EventOrganizer();
                    $organizer->event_id = $event->id;
                    $organizer->user_id = $organizerId;
                    $organizer->save();
                }

                $guests = $request->post('guests');
                EventGuest::where('event_id', $event->id)->delete();
                foreach ($guests as $guestId) {
                    $guest = new EventGuest();
                    $guest->event_id = $event->id;
                    $guest->guest_id = $guestId;
                    $guest->save();
                }

                $resources = $request->post('resources') ? $request->post('resources') : [];
                EventResource::where('event_id', $event->id)->delete();
                foreach ($resources as $resourceId) {
                    $resource = new EventResource();
                    $resource->event_id = $event->id;
                    $resource->resource_id = $resourceId;
                    $resource->save();
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Event Updated Succesfully',
                    'data' => ['id' => $event->id]
                ]);
            } else {
                return response()->json(['status' => false, 'data' => [], 'message' => 'Event not found']);
            }
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => $th->response
            ], 400);
        }
    }

    /**
     * @OA\Delete(
     *      path="/event/{id}",
     *      operationId="deleteEvent",
     *      tags={"Event"},
     *      summary="Delete existing Event",
     *      description="Deletes a record and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="Event id",
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
            Event::destroy($id);
            return response()->json(['status' => true,'message' => 'Event Deleted Succesfully']);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Error Ocurred',
                'data' => $th->getMessage()
            ], 400);
        }
    }


    /**
     * @OA\Post(
     *     path="/event/sendNotification",
     *     tags={"Event"},
     *     summary="Send Notification Event",
     *     operationId="sendnotificationevent",
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
     *                 required={"name","description","place_id","start_at","end_at","created_by","type","assistants"},
     *                 @OA\Property(
     *                   property="notification_type",
     *                   description="Notification type [create,update]",
     *                   type="string",
     *                   ),
     * *                 @OA\Property(
     *                   property="role",
     *                   description="Role name",
     *                   type="string",
     *                   ),
     * *                 @OA\Property(
     *                   property="event_id",
     *                   description="Event Id",
     *                   type="integer",
     *                   ),
     * *                 @OA\Property(
     *                   property="created_by",
     *                   description="Created User Id",
     *                   type="integer",
     *                   )
     *              )
     *            )
     *          ),
     *      security={
     *         {"authorization": {}}
     *     }
     * )
     */
    public function sendNotification(Request $request)
    {

        try {
            //      'notification_type' =>'update, create, checklist',
            //      'role' => 'participantes',
            $this->validate($request, [
                'notification_type' => 'required',
                'role' => 'required',
                'event_id' => 'required|integer',
                'created_by' => 'required'
            ]);

            $input = $request->all();

            $event = Event::find($input['event_id']);

            if (!is_null($event)) {
                $rememberChecklist = "No olvides consultar tu checklist de recursos antes de iniciar tu evento";
                $creator = DB::table('user')
                    ->join('user_has_role', 'user.id', '=', 'user_has_role.user_id')
                    ->join('role', 'role.role_id', '=', 'user_has_role.role_id')
                    ->join('event', 'event.created_by', '=', 'user.id')
                    ->join('place', 'place.id', '=', 'event.place_id')
                    ->select(
                        'user.email',
                        DB::raw('CONCAT(user.name," ",user.last_name) as full_name'),
                        'event.name',
                        'event.start_at',
                        'event.end_at',
                        'event.description',
                        'place.name AS place'
                    )
                    ->where('user.id', $input['created_by'])
                    ->where('event.id', $input['event_id']);

                $senders = DB::table('user')
                    ->join('user_has_role', 'user.id', '=', 'user_has_role.user_id')
                    ->join('role', 'role.role_id', '=', 'user_has_role.role_id')
                    ->leftJoin('event_guest', 'user.id', '=', 'event_guest.guest_id')
                    ->leftJoin('event_organizer', 'user.id', '=', 'event_organizer.user_id')
                    ->join('event', 'event.id', '=', 'event_guest.event_id')
                    ->join('place', 'place.id', '=', 'event.place_id')
                    ->select(
                        'user.email',
                        DB::raw('CONCAT(user.name," ",user.last_name) as full_name'),
                        'event.name',
                        'event.start_at',
                        'event.end_at',
                        'event.description',
                        'place.name AS place'
                    )
                    ->where('role.name', $input['role'])
                    ->where('event.id', $input['event_id'])
                    ->union($creator)
                    ->get();

                foreach ($senders as $sender) {

                    $details = [

                        'email' => $sender->email,
                        'title' => $sender->name,
                        'body' => ($input['notification_type'] == 'checklist') ? $rememberChecklist : $sender->description,
                        'start_at' => $sender->start_at,
                        'end_at' => $sender->end_at,
                        'place' => $sender->place,
                        'full_name' => $sender->full_name,
                        'notification_type' => $input['notification_type']
                    ];
                    Log::info('SendNotification', $details);
                    Mail::to($details['email'])->send(new \App\Mail\EventMail($details));
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Notification Send'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Event Not Found'
                ], 400);
            }
        } catch (\Throwable $th) {

            return response()->json(
                ['status' => false, 'message' => 'Error Ocurred', 'data' => $th->getMessage()],
                400
            );
        }
    }

    /**
     * @OA\Get(
     *     path="/event/getResources/{id}",
     *     tags={"Event"},
     *     summary="Show EventResource",
     *     operationId="Showeventresource",
     *     description="Returns Event resources",
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
     *          description="Event id",
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
    public function getResources($id)
    {
        try {
            $resources = DB::table('resource as r')
                ->join('event_resource as er', 'r.id', '=', 'er.resource_id')
                ->select('r.*')
                ->where('er.event_id', $id)->get();
            if ($resources->count() > 0) {
                return response()->json([
                    'status' => true,
                    'message' => 'Resources Found',
                    'data' => $resources
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Resource Not Found'
                ], 400);
            }
        } catch (\Throwable $th) {
            return response()->json(
                ['status' => false, 'message' => 'Error Ocurred', 'data' => $th->getMessage()],
                400
            );
        }
    }

    /**
     * @OA\Put(
     *     path="/event/checkResources/{id}",
     *     tags={"Event"},
     *     summary="Check resorces of an Event",
     *     operationId="checkeventresource",
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
     *          description="Event id",
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
     *                    property="resources",
     *                    description="Resources check Array",
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
    public function checkResources(Request $request, $id)
    {

        try {

            $this->validate($request, [
                'resources' => 'required'
            ]);

            $event = Event::where('id', '=', $id)->first();

            if (!is_null($event)) {
                $resources = $request->post('resources');
                if (count($resources) > 0)
                    $event->resources_check = 1;
                foreach ($resources as $resource) {
                    $eventresource = EventResource::where('event_id', $event->id)->where('resource_id', $resource)->first();
                    $eventresource->isOk = true;

                    $eventresource->save();
                }

                $event->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Resources Checked Succesfully'
                ]);
            } else {
                return response()->json(['status' => false, 'data' => [], 'message' => 'Event not found'], 400);
            }
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Error Ocurred',
                'data' => $th->getMessage()
            ], 400);
        }
    }



    public function checkListNotification()
    {
        try {
            $today = new DateTime('now');
            $today = $today->format('Y-m-d H:i:s');
            $plus15 = new DateTime('now');
            $plus15->add(new DateInterval('PT15M'));

            $events = Event::where('start_at', '=', $plus15)->get();

            foreach ($events as $event) {
                $myRequest = new \Illuminate\Http\Request();
                $myRequest->setMethod('POST');
                $myRequest->request->add(['notification_type' => 'checklist']);
                $myRequest->request->add(['role' => 'Admin']);
                $myRequest->request->add(['event_id' => $event->id]);
                $myRequest->request->add(['created_by' => $event->created_by]);

                $this->sendNotification($myRequest);
            }
        } catch (\Throwable $th) {
            Log::alert('Error al consultar eventos a notificar' . $th->getMessage());
        }
    }

    public function checkStartEvent()
    {
        try {
            $today = new DateTime('now');
            $today = $today->format('Y-m-d 00:00:00');

            $today2 = new DateTime('now');
            $today2 = $today2->format('Y-m-d 23:59:59');


            $today3 = new DateTime('now');
            // $plus15->add(new DateInterval('PT15M'));

            $events = Event::whereBetween('start_at', [$today, $today2])->where('status', 1)->get();

            foreach ($events as $event) {

                $delay = new DateTime($event->start_at);
                $delay->add(new DateInterval('PT15M'));

                if ($delay == $today3) {
                    $event->status = 3; // Retraso en evento
                    $event->save();
                }
            }
        } catch (\Throwable $th) {
            Log::alert('Error al consultar eventos a notificar' . $th->getMessage());
        }
    }
}
