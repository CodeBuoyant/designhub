<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Repositories\Contracts\IChat;
use App\Repositories\Contracts\IMessage;
use App\Repositories\Eloquent\Criteria\WithTrashed;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected $chats;
    protected $messages;

    public function __construct(IChat $chats, IMessage $messages) {
        $this->chats = $chats;
        $this->messages = $messages;
    }

    // Send message to user
    public function sendMessage(Request $request) {
        // Validate request form user
        $this->validate($request, [
            'recipient' => ['required'],
            'body' => ['required'],
        ]);

        $recipient = $request->recipient;
        $user = auth()->user();
        $body = $request->body;

        // Check if there is an existing chat b/w auth user and recipient
        $chat = $user->getChatWithUser($recipient);

        if (!$chat) {
            // Passing an empty array because chat table only contains id and timestamps
            $chat = $this->chats->create([]);

            // Create participants
            $this->chats->createParticipants($chat->id, [$user->id, $recipient]);
        }

        // Add the message to the chat
        $message = $this->messages->create([
            'user_id' => $user->id,
            'chat_id' => $chat->id,
            'body' => $body,
            'last_read' => null
        ]);

        return new MessageResource($message);
    }

    // Get chats for user
    public function getUserChats() {
        return ChatResource::collection($this->chats->getUserChats());
    }

    // Get messages for chat
    public function getChatMessages($id) {
        return MessageResource::collection($this->messages->withCriteria([
            new WithTrashed()
        ])->findWhere('chat_id', $id));
    }

    // Mark chat as read
    public function markAsRead($id) {
        //
    }

    // Destroy message
    public function destroyMessage($id) {
        //
    }
}
