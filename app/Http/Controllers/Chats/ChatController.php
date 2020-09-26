<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\IChat;
use App\Repositories\Contracts\IMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected $chats;
    protected $messages;

    public function __construct(IMessage $messages, IChat $chats) {
        $this->messages = $messages;
        $this->chats = $chats;
    }

    // Send message to user
    public function sendMessage(Request $request) {
        //
    }

    // Get chats for user
    public function getUserChats() {
        //
    }

    // Get messages for chat
    public function getChatMessages($id) {
        //
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
