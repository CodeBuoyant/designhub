<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Repositories\Contracts\IChat;
use App\Repositories\Contracts\IMessage;
use App\Repositories\Eloquent\Criteria\WithTrashed;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{
    /**
     * @var IChat
     */
    protected $chats;

    /**
     * @var IMessage
     */
    protected $messages;

    /**
     * ChatController constructor.
     *
     * @param IChat $chats
     * @param IMessage $messages
     */
    public function __construct(IChat $chats, IMessage $messages) {
        $this->chats = $chats;
        $this->messages = $messages;
    }

    /**
     * Send message to user
     *
     * @param Request $request
     * @return MessageResource
     * @throws ValidationException
     */
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

    /**
     * Get chats for user
     *
     * @return AnonymousResourceCollection
     */
    public function getUserChats() {
        return ChatResource::collection($this->chats->getUserChats());
    }

    /**
     * Get messages for chat
     *
     * @param $id
     * @return AnonymousResourceCollection
     */
    public function getChatMessages($id) {
        return MessageResource::collection($this->messages->withCriteria([
            new WithTrashed()
        ])->findWhere('chat_id', $id));
    }

    /**
     * Mark chat as read
     *
     * @param $id
     * @return JsonResponse
     */
    public function markAsRead($id) {
        $chat = $this->chats->find($id);
        $chat->markAsReadForUser(auth()->id());

        return response()->json(['message' => 'Success'], 200);
    }

    /**
     * Destroy message
     *
     * @param $id
     * @throws AuthorizationException
     */
    public function destroyMessage($id) {
        $message = $this->messages->find($id);
        $this->authorize('delete', $message);
        $message->delete();
    }
}
