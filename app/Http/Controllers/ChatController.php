<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ChatController extends Controller
{
    use AuthorizesRequests;

    public function getOrCreateChat(Request $request, Announcement $announcement)
    {
       

        $created_by = Auth::id();
        
        // On empêche la création d'un chat si l'utilisateur est le même que celui qui a posté l'annonce
        if ($created_by == $announcement->created_by) {
            return response()->json(['error' => 'Vous ne pouvez pas créer un chat avec vous-même'], 403);
        }

        // Recherche d'un chat existant 
        $chat = Chat::where('posted_by', $announcement->id)
                    ->where('is_closed', false)
                    ->where('created_by', $created_by)
                    ->first();
        
        // S'il n'existe pas, on le crée
        if (!$chat) {
            $chat = Chat::create([
                'created_by' => $created_by,
                'posted_by' => $announcement->id,
                'is_closed' => false,
            ]);

            // Retourner un code HTTP 201 pour indiquer une création
            return response()->json($chat, 201);
        }

        $chat->load(['announcement', 'messages']);

        // Retourner un code HTTP 200 si le chat existe déjà
        return response()->json($chat, 200);
    }

    public function closeChat(Chat $chat)
    {
        // Vérifiez que l'utilisateur est autorisé à fermer le chat
        if (Auth::id() !== $chat->created_by) {
            return response()->json(['error' => 'Non autorisé à clôturer ce chat'], 403);
        }
    

    
        // Fermez tous les autres chats liés à cette annonce
        Chat::where('posted_by', $chat->posted_by)
            ->where('id', '!=', $chat->id)
            ->update([
                'is_closed' => true,
                'closed_at' => now(),
            ]);
    
        // Mettez à jour le chat actuel pour qu'il soit fermé dans 30 jours
        $chat->update([
            'close_to' => now()->addDays(30), // Définit la date de fermeture à 30 jours
        ]);
    
        return response()->json(['message' => 'Les autres chats ont été fermés et celui-ci sera fermé dans 30 jours'], 200);
    }

    public function mychats()
    {
        $created_by = Auth::id();

        
        // On charge les messages et l'annonce associée
        $chat = Chat::with(['announcement', 'messages'])
            ->where(function ($query) use ($created_by) {
                $query->where('created_by', $created_by)
                      ->orWhereHas('announcement', function ($q) use ($created_by) {
                          $q->where('created_by', $created_by);
                      });
            })
            ->latest()
            ->distinct()
            ->get();
        return response()->json($chat);
    }
}
