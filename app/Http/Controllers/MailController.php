<?php

namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

use App\Mail\ContactMail;
 
class MailController extends Controller
{

    public function send(Request $request, $layout)
    {
        switch($layout){
            case "contact-mail-site":
                $r = $this->sendMailFromHotSite($request->all());
            break;   
        }

        return $r;
    }

    /**
     * @return Response
     */
    private function sendMailFromHotSite($data){
    
        Mail::to(['dandrade.dev@gmail.com','rosanneklerx@gmail.com'])->send(new ContactMail($data));
        
        if(count(Mail::failures()) > 0){
            return response()->json(['success'=>true, 'msg'=>'Failed to send'], 500);
        }else{
            return response()->json(['success'=>true, 'msg'=>'Mail sent!']);
        }
        
    }


}