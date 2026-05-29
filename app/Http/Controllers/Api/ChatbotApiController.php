<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ChatbotController;
use Illuminate\Http\Request;

class ChatbotApiController extends Controller
{
    public function chat(Request $request, ChatbotController $chatbot)
    {
        return $chatbot->chat($request);
    }
}
