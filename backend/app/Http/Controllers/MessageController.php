<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MessageController extends Controller
{
    // Récupérer les messages d'un groupe
    public function groupConversation($groupId)
    {
        $messages = Message::where('group_id', $groupId)
            ->orderBy('created_at')
            ->get()
            ->map(function($msg) {
                $msgArr = $msg->toArray();
                $msgArr['sender_name'] = $msg->sender ? ($msg->sender->nom ?? $msg->sender->name) : null;
                return $msgArr;
            });
        return response()->json($messages);
    }

    // Enregistrer un nouveau message (privé ou groupe)
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            // Un des deux doit être présent
            'receiver_id' => 'nullable|exists:users,id',
            'group_id' => 'nullable|exists:groupes,id',
        ]);

        if (!$request->receiver_id && !$request->group_id) {
            return response()->json(['error' => 'receiver_id ou group_id requis'], 422);
        }

        $message = Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $request->receiver_id,
            'group_id' => $request->group_id,
            'content' => $request->content,
        ]);

        // Optionnel: charger les relations pour la réponse
        $message->load('sender', 'receiver', 'group');

        // Notifier Socket.IO
        try {
            $room = null;
            if ($request->receiver_id) {
                $room = 'user.' . $request->receiver_id;
            } elseif ($request->group_id) {
                $room = 'group.' . $request->group_id;
            }
            if ($room) {
                Http::post('http://localhost:4001/notify-message', [
                    'room' => $room,
                    'message' => $message->toArray(),
                ]);
            }
        } catch (\Exception $e) {
            // Log::error('Socket.IO notification failed: ' . $e->getMessage());
        }

        return response()->json($message, 201);
    }

    /**
     * Retourne les contacts récents (utilisateurs et groupes) de l'utilisateur connecté
     */
    public function recentContacts(Request $request)
    {
        $userId = $request->user()->id;
        // Récupérer les conversations privées
        $privateContacts = Message::where(function($q) use ($userId) {
                $q->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
            })
            ->whereNotNull('receiver_id')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(function($msg) use ($userId) {
                // L'autre utilisateur dans la conversation
                return $msg->sender_id == $userId ? $msg->receiver_id : $msg->sender_id;
            })
            ->map(function($msgs, $otherUserId) {
                $lastMsg = $msgs->sortByDesc('created_at')->first();
                $user = $lastMsg->sender_id == $otherUserId ? $lastMsg->sender : $lastMsg->receiver;
                return [
                    'id' => $user->id,
                    'nom' => $user->nom ?? $user->name,
                    'prenom' => $user->prenom ?? '',
                    'email' => $user->email,
                    'last_message' => $lastMsg->content,
                    'last_date' => $lastMsg->created_at,
                    '_type' => 'user',
                ];
            })->values();

        // Récupérer les groupes où l'utilisateur a envoyé un message
        $groupContacts = Message::where('sender_id', $userId)
            ->whereNotNull('group_id')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('group_id')
            ->map(function($msgs, $groupId) {
                $lastMsg = $msgs->sortByDesc('created_at')->first();
                $group = $lastMsg->group;
                return [
                    'id' => $group->id,
                    'nom' => $group->nom,
                    'last_message' => $lastMsg->content,
                    'last_date' => $lastMsg->created_at,
                    '_type' => 'group',
                ];
            })->values();

        // Fusionner et trier par date du dernier message
        $allContacts = $privateContacts->concat($groupContacts)->sortByDesc('last_date')->values();

        return response()->json($allContacts);
    }

    /**
     * Récupérer les messages d'une conversation privée
     */
    public function conversation($userId, Request $request)
    {
        $currentUserId = $request->user()->id;
        
        $messages = Message::where(function($q) use ($currentUserId, $userId) {
                $q->where('sender_id', $currentUserId)
                  ->where('receiver_id', $userId);
            })
            ->orWhere(function($q) use ($currentUserId, $userId) {
                $q->where('sender_id', $userId)
                  ->where('receiver_id', $currentUserId);
            })
            ->whereNotNull('receiver_id')
            ->whereNull('group_id')
            ->orderBy('created_at')
            ->get()
            ->map(function($msg) {
                $msgArr = $msg->toArray();
                $msgArr['sender_name'] = $msg->sender ? ($msg->sender->nom ?? $msg->sender->name) : null;
                return $msgArr;
            });
            
        return response()->json($messages);
    }
} 