<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string'
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content
        ]);

        return response()->json([
            'message' => 'Message envoyé avec succès',
            'data' => $message->load('sender', 'receiver')
        ]);
    }

    public function getConversations()
    {
        $userId = Auth::id();
        
        // Récupérer les utilisateurs avec qui l'utilisateur courant a échangé des messages
        $conversations = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->get()
            ->map(function ($message) use ($userId) {
                $otherUserId = $message->sender_id == $userId ? $message->receiver_id : $message->sender_id;
                return $otherUserId;
            })
            ->unique()
            ->values();

        $users = User::whereIn('id', $conversations)->get()
            ->map(function ($user) use ($userId) {
                // Récupérer le dernier message pour chaque conversation
                $lastMessage = Message::where(function ($query) use ($userId, $user) {
                    $query->where('sender_id', $userId)
                        ->where('receiver_id', $user->id)
                        ->orWhere(function ($query) use ($userId, $user) {
                            $query->where('sender_id', $user->id)
                                ->where('receiver_id', $userId);
                        });
                })
                ->latest()
                ->first();

                return [
                    'user' => $user,
                    'last_message' => $lastMessage,
                    'unread_count' => Message::where('sender_id', $user->id)
                        ->where('receiver_id', $userId)
                        ->whereNull('read_at')
                        ->count()
                ];
            });

        return response()->json($users);
    }

    public function getMessages($userId)
    {
        $messages = Message::where(function ($query) use ($userId) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $userId)
                ->orWhere(function ($query) use ($userId) {
                    $query->where('sender_id', $userId)
                        ->where('receiver_id', Auth::id());
                });
        })
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'asc')
        ->get();

        // Marquer les messages comme lus
        Message::where('sender_id', $userId)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json($messages);
    }

    public function markAsRead($messageId)
    {
        $message = Message::findOrFail($messageId);
        
        if ($message->receiver_id !== Auth::id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $message->update(['read_at' => now()]);
        return response()->json(['message' => 'Message marqué comme lu']);
    }
}
