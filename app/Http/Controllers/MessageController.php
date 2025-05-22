<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    
    public function sendMessage(Request $request, Chat $chat)
    {
        $request -> validate([
            'content' => 'required|string',
        ]);
        //On en empêche l'utilisateur de créer un message dans une conversation fermée
        if($chat->is_closed || ($chat->close_to && now()->isAfter($chat->close_to))){
            return response() -> json(['error' => 'Cette conversation est fermée'], 403);
        }

       //On vérifie si l'utilisateur a bien le droit d'envoyer un message
       if (Auth::id() !== $chat -> created_by && Auth::id() !== $chat -> announcement->created_by){
            return response()-> json(['error' => "vous n'avez pas accès à cette conversation"], 403);
       }
        //On vérifie si l'utilisateur a bien le droit d'envoyer un message
        $receiver = (Auth::id() === $chat -> created_by && $chat->announcement)
            ? $chat -> announcement->created_by: $chat->created_by;
       
        $message = $chat->messages()->create([
            'conversation' => $chat ->id,
            'sender' => Auth::id(),
            'receiver' => $receiver,
            'content' => $request -> content,
        ]);
        

        return response() -> json($message, 201);
    }

    public function getMessages(Chat $chat)
    {

        if (Auth::id() !== $chat -> created_by && Auth::id() !== $chat -> announcement->created_by){
            return response()-> json(['error' => "Vous n'avez pas accès à cette conversation"], 403);
        }

        $messages = $chat -> messages()->with(['sender', 'receiver'])->latest()->get();

        return response() -> json($messages);
    }
    
    
}
    
    
    

