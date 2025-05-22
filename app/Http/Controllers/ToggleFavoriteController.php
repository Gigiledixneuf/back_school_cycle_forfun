<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;

class ToggleFavoriteController extends Controller
{
    public function __invoke(Request $request, $announcement_id)
    {
        try {
            // Récupération de l'utilisateur connecté
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'erreur' => 'Utilisateur non connecté'
                ],);
            }

            // Vérifie si l'annonce existe
            $announcement = Announcement::find($announcement_id);
            if (!$announcement) {
                return response()->json([
                    'erreur' => 'Annonce inexistante'
                ]);
            }

            // Vérifie si déjà en favoris
            $favorite = $user->favorites()->where('announcement_id', $announcement_id)->first();

            if ($favorite) {
                // Supprime le favori
                $favorite->delete();
                return response()->json([
                    'message' => 'Annonce retirée des favoris.',
                ]);
            }

            // Ajoute en favoris
            $user->favorites()->create([
                'announcement_id' => $announcement_id
            ]);

            return response()->json([
                'message' => 'Annonce ajoutée aux favoris.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage()
            ]);
        }
    }
}
