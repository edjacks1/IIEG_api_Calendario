<?php 

namespace App\Observers;
use App\Http\Controllers\Operation\EventController;
use Illuminate\Support\Facades\Log;

trait EventObserver{

    protected static function boot(){
        parent::boot();

        static::created(function ($event){
            $myRequest = new \Illuminate\Http\Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add(['notification_type' => 'create']);
            $myRequest->request->add(['role' => 'Admin']);
            $myRequest->request->add(['event_id' => $event->id]);
            $myRequest->request->add(['created_by' => $event->created_by]);

            $eventCtr= new EventController();
            return $eventCtr->sendNotification($myRequest);
            
        });

        static::updated(function ($event){
            $myRequest = new \Illuminate\Http\Request();
            $myRequest->setMethod('POST');
            $myRequest->request->add(['notification_type' => 'update']);
            $myRequest->request->add(['role' => 'participantes']);
            $myRequest->request->add(['event_id' => $event->id]);
            $myRequest->request->add(['created_by' => $event->created_by]);

            $eventCtr= new EventController();
            return $eventCtr->sendNotification($myRequest);
            
            
        });

        static::deleted(function ($event){
            
        });
    }

}