<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     title="MCCP API — Multi-Channel Content Processor",
 *     version="1.0.0",
 *     description="API REST to process messages with AI and distribute them to multiple channels (Email, Slack, SMS).",
 *     @OA\Contact(
 *         email="dev@mccp.local"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="API Server"
 * )
 *
 * @OA\Tag(
 *     name="Health",
 *     description="Service status"
 * )
 *
 * @OA\Tag(
 *     name="Messages",
 *     description="Message creation and retrieval"
 * )
 */
class ApiDocController extends Controller
{
    /**
     * @OA\Get(
     *     path="/health",
     *     tags={"Health"},
 *     summary="System health check",
 *     description="Verifies that the backend, database, and queue worker are operational.",
     *     operationId="healthCheck",
     *     @OA\Response(
     *         response=200,
     *         description="System operational",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="ok"),
     *             @OA\Property(property="service", type="string", example="MCCP API"),
     *             @OA\Property(property="version", type="string", example="1.0.0"),
     *             @OA\Property(property="php", type="string", example="8.4.x"),
     *             @OA\Property(property="laravel", type="string", example="12.x"),
     *             @OA\Property(property="database", type="string", example="connected"),
     *             @OA\Property(property="timestamp", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="System error"
     *     )
     * )
     */
    public function health(): JsonResponse
    {
        try {
            \DB::connection()->getPdo();
            $dbStatus = 'connected';
        } catch (\Exception $e) {
            $dbStatus = 'error: ' . $e->getMessage();
        }

        return response()->json([
            'status'    => 'ok',
            'service'   => 'MCCP API',
            'version'   => '1.0.0',
            'php'       => PHP_VERSION,
            'laravel'   => app()->version(),
            'database'  => $dbStatus,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
