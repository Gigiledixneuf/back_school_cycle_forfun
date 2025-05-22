<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ToggleFavoriteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    //création et récupération d'un chat pour une annonce
    Route::post('/announcements/{announcement}/chats', [ChatController::class, 'getOrCreateChat']);

    //Fermer un chat
    Route::post('/chats/{chat}/close', [ChatController::class, 'closeChat']);

    //Envoyer un message dans un chat
    Route::post('/chats/{chat}/messages', [MessageController::class, 'sendMessage']);

    //Récupérer les massages d'unchat
    Route::get('/chats/{chat}/messages', [MessageController::class, 'getMessages']);

    //Lister tous les chats d'un utilisateur connecté
    Route::get('/my-chats', [ChatController::class, 'myChats']);

    // Ajouter ou retirer une annonce aux favoris
    Route::post('/favorites/{announcement}',ToggleFavoriteController::class);
});

Route::apiResource('/announcements', AnnouncementController::class);
Route::apiResource('/category', CategoryController::class);

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

require __DIR__.'/auth.php';
