<?php

namespace OlaHub\DesignerCorner\commonData\Helpers;

use Illuminate\Support\Facades\DB;


class LogHelper {

    /*
     * Set log for any function in user project
     * @param string
     *
     */
    
    
    
    function setLogSessionData($logSession){
        
        if(is_array($logSession) && count($logSession) > 0){
            
            $data = app('session')->get('log_session') ? app('session')->get('log_session') : [];
            foreach ($logSession as $key => $value){
                if($key == 'response'){
                    $data[$key] = json_encode($value);
                } else {
                    $data[$key] = $value;
                }
            }
            
            app('session')->put('log_session', $data);
            
        }
        
    }
    
    
    function setActionsData($actionData){
        if(is_array($actionData) && count($actionData) > 0){
            
            $actionData['action_time'] = date("H:i:s");
            
            app('session')->push('action_session', $actionData);
            
        }
    }




    function saveLogSessionData(){
        
        $logData = app('session')->get('log_session');
        
        if(is_array($logData) && count($logData) > 0){
            
           if(!array_key_exists('user_id', $logData)){
               return;
           } 
           
           $logData['actions'] = app("session")->get("action_session") ? app("session")->get("action_session") : [];
           
           app('session')->put('action_session', []);
           app('session')->put('log_session', []);
           
           DB::connection('mongoLog')->collection($logData['user_id'])->insert($logData);
           
        }
        
    }
    
    
}
