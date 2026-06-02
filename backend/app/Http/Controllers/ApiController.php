<?php

namespace App\Http\Controllers;

use App\Actions\CreateMessageAction;
use App\DTOs\MessageDTO;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class ApiController extends Controller
{
    public function __construct(
        private readonly CreateMessageAction $createMessageAction,
    ) {}

    public function store(StoreMessageRequest $request): JsonResponse
    {
        $dto = new MessageDTO(
            title: $request->input('title'),
            content: $request->input('content'),
            channels: $request->input('channels'),
        );

        try {
            $message = $this->createMessageAction->execute($dto);
        } catch (RuntimeException $e) {
            return response()->json([
                'message' => 'AI processing failed',
                'error'   => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Message created successfully',
            'data'    => $message->load('deliveryLogs'),
        ], 201);
    }

    public function index(): JsonResponse
    {
        $messages = Message::with('deliveryLogs')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $messages]);
    }
}
