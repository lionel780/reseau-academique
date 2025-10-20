<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Departement;
use App\Models\Filiere;
use App\Models\Groupe;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Récupère les statistiques du dashboard
     */
    public function stats()
    {
        $stats = [
            'users' => User::count(),
            'departements' => Departement::count(),
            'filieres' => Filiere::count(),
            'groupes' => Groupe::count(),
        ];

        return response()->json($stats);
    }

    /**
     * Liste tous les utilisateurs
     */
    public function users(Request $request)
    {
        $query = User::with(['groupes']);
        if ($request->has('departement')) {
            $query->where('role', 'etudiant')->where('departement', $request->departement);
        }
        if ($request->has('filiere')) {
            $query->where('role', 'etudiant')->where('filiere', $request->filiere);
        }
        $users = $query->get();
        return response()->json($users);
    }

    /**
     * Crée un nouvel utilisateur
     */
    public function createUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:administrateur,enseignant,etudiant',
            'departement' => 'nullable|string|max:255',
            'filiere' => 'nullable|string|max:255',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        
        $user = User::create($validated);
        
        return response()->json($user, 201);
    }

    /**
     * Met à jour un utilisateur
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'prenom' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'role' => 'sometimes|required|in:administrateur,enseignant,etudiant',
            'departement' => 'nullable|string|max:255',
            'filiere' => 'nullable|string|max:255',
        ]);

        $user->update($validated);
        
        return response()->json($user);
    }

    /**
     * Supprime un utilisateur
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        return response()->json(['message' => 'Utilisateur supprimé']);
    }

    /**
     * Liste tous les départements
     */
    public function departements()
    {
        $departements = Departement::with('filieres')->get();
        return response()->json($departements);
    }

    /**
     * Crée un département
     */
    public function createDepartement(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $departement = Departement::create($validated);
        
        return response()->json($departement, 201);
    }

    /**
     * Liste toutes les filières
     */
    public function filieres()
    {
        $filieres = Filiere::with('departement')->get();
        return response()->json($filieres);
    }

    /**
     * Crée une filière
     */
    public function createFiliere(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'departement_id' => 'required|exists:departements,id',
            'description' => 'nullable|string',
        ]);

        $filiere = Filiere::create($validated);
        
        return response()->json($filiere, 201);
    }

    /**
     * Liste tous les groupes
     */
    public function groupes()
    {
        $groupes = Groupe::with(['filiere.departement', 'users'])->get();
        return response()->json($groupes);
    }

    /**
     * Crée un groupe
     */
    public function createGroupe(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'filiere_id' => 'required|exists:filieres,id',
            'description' => 'nullable|string',
        ]);

        $groupe = Groupe::create($validated);
        
        return response()->json($groupe, 201);
    }

    /**
     * Supprime un groupe
     */
    public function deleteGroupe($id)
    {
        $groupe = Groupe::findOrFail($id);
        $groupe->delete();
        
        return response()->json(['message' => 'Groupe supprimé']);
    }

    /**
     * Affecte des étudiants à un groupe
     */
    public function affecterEtudiants(Request $request, $groupeId)
    {
        $groupe = Groupe::findOrFail($groupeId);
        
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $groupe->users()->sync($validated['user_ids']);
        
        return response()->json(['message' => 'Étudiants affectés au groupe']);
    }

    // Retourne tous les utilisateurs étudiants et enseignants
    public function allUsers()
    {
        return response()->json(\App\Models\User::whereIn('role', ['etudiant', 'enseignant'])->get());
    }

    // Retourne tous les groupes
    public function allGroupes()
    {
        return response()->json(\App\Models\Groupe::all());
    }
}
