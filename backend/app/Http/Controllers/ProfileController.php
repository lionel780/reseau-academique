<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Affiche le profil de l'utilisateur connecté
     */
    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Met à jour le profil de l'utilisateur connecté
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'prenom' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|string|min:8|confirmed',
            'departement' => 'sometimes|nullable|string|max:255',
            'filiere' => 'sometimes|nullable|string|max:255',
            'niveau' => 'sometimes|nullable|string|max:255',
            'role' => 'sometimes|nullable|string|in:étudiant,enseignant,administrateur',
            'avatar' => 'sometimes|nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Gestion de l'upload d'avatar
        \Log::info('Fichiers reçus lors de la mise à jour du profil', ['files' => $request->allFiles()]);
        if ($request->hasFile('avatar')) {
            // Supprimer l'ancien avatar s'il existe
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Sauvegarder le nouveau fichier
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        // Gestion du mot de passe
        if (isset($validated['password'])) {
            // Vérifier le mot de passe actuel si fourni
            if ($request->has('current_password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return response()->json([
                        'message' => 'Le mot de passe actuel est incorrect.'
                    ], 422);
                }
            }
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Mise à jour de l'utilisateur
        $user->update($validated);

        return response()->json($user);
    }
} 