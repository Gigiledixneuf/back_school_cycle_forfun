<?php

namespace App\Http\Controllers;

use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use App\Models\Photo;
use App\Models\User;
use App\Notifications\NewAnnouncementNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class AnnouncementController extends Controller
{
    //function pour voir toutes les annonces disponible
    public function index()
    {
        return AnnouncementResource::collection(
            Announcement::where('is_completed', false)
                ->where('is_cancelled', false)
                ->orderBy('created_at', 'desc')
                ->paginate(10)
        );
    }


    public function show($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            return new AnnouncementResource($announcement);
        } catch (\Exception $exception) {
            return response()->json([
                'Message' => 'Une erreur est survenue',
                'Erreur' => $exception->getMessage()
            ]);
        }
    }


    //function pour la creation d'une annonce
    public function store(Request $request)
    {
        //on recupere le user connecter
        $user = Auth::user();

        try {
            if ($user->role !== 'tutor') {
                return response()->json([
                    "Message" => "Vous n'avez pas le droit de creer une annonce"
                ]);
            }
            $request->validate([
                'title' => 'required|string|min:5|max:500',
                'description' => 'required|string|max:1000',
                'category_id' => 'required|exists:categories,id',
                'operation_type' => 'required|string|in:don,sale,exchange',
                'state' => 'required|string|in:new,good,damaged,like new',
                'price' => 'nullable|numeric',
                'is_completed' => 'nullable|boolean',
                'is_cancelled' => 'nullable|boolean',
                'exchange_location_address' => 'string|max:255',
                'exchange_location_lng' => 'numeric',
                'exchange_location_lat' => 'numeric',
                'photos.*' => 'required|image|mimes:jpg,png,gif|max:2040'
            ]);

            $announcement = Announcement::create([
                'title' => $request['title'],
                'description' => $request['description'],
                'category_id' => $request['category_id'],
                'operation_type' => $request['operation_type'],
                'state'=>$request['state'],
                'price' => $request['price'],
                'is_completed' => $request['is_completed'] ?? false,
                'is_cancelled' => $request['is_cancelled'] ?? false,
                'exchange_location_address' => $request['exchange_location_address'],
                'exchange_location_lng' => $request['exchange_location_lng'],
                'exchange_location_lat' => $request['exchange_location_lat'],
                'created_by' => $user->id

            ]);

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $image) {
                    $path = $image->store('announcements', 'public');

                    Photo::create([
                        'announcement_id' => $announcement->id,
                        'url' => $path,
                    ]);
                }
            }

            $announcement->load(['category', 'user', 'favorites', 'photos']);

            $users = User::whereHas('preferences', function ($query) use ($announcement) {
                $query->where('categories.id', $announcement->category_id);
            })->get();


            // verification si il n'a trouvé aucun utilisateur
            if ($users->count() !== 0) {
                //Envoi des mail aux utilisateurs
                Notification::send($users, new NewAnnouncementNotification($announcement));
            }

            return new AnnouncementResource($announcement);
        } catch (\Exception $exception) {
            return response()->json([
                'Message' => "Une erreur est survenue lors de la creation de l'annonce ",
                'Erreur' => $exception->getMessage()
            ]);
        }
    }


    //function pour mettre à une annonce
    public function update(Request $request, Announcement $announcement)
    {
        $user = Auth::user();
        try {

            //on verifie si l'utilisateur connecter est l'auteur de l'article
            if ($user->id !== $announcement->created_by) {
                return response()->json([
                    'Message' => "Vous n'avez pas le droit de modifier cette annonce"
                ], 403);
            } else {
                $validated = $request->validate([
                    'title' => 'required|string|min:5|max:500',
                    'description' => 'required|string|max:1000',
                    'operation_type' => 'required|string|in:don,sale,exchange',
                    'state' => 'required|string|in:new,good,damaged,like new',
                    'price' => 'nullable|numeric',
                    'is_completed' => 'nullable|boolean',
                    'is_cancelled' => 'nullable|boolean',
                    'exchange_location_address' => 'string|max:255',
                    'exchange_location_lng' => 'numeric',
                    'exchange_location_lat' => 'numeric',
                    'category_id' => 'required|exists:categories,id'
                ]);

                $announcement->update($validated);
                return new AnnouncementResource($announcement);
            }
        } catch (\Exception $exception) {

            return response()->json([
                'Message' => "Une erreur est survenue lors de la mise à jour de l'annonce ",
                'Erreur' => $exception->getMessage()
            ]);
        }
    }

    //funnction pour supprimer une annonce
    public function destroy(Announcement $announcement)
    {
        $user = Auth::user();

        try {

            if ($user->id !== $announcement->created_by) {
                return response()->json([
                    'Message' => "Vous n'avez pas le droit de supprimer cette annonce",
                ], 403);
            } else {
                $announcement->delete();
                return response()->json([
                    'Message' => "Annonce supprimer"
                ]);
            }
        } catch (\Exception $exception) {
            return response()->json([
                'Message' => "Une erreur est survenue lors de la suppression",
                'Erreur' => $exception->getMessage()
            ], 500);
        }
    }
}
