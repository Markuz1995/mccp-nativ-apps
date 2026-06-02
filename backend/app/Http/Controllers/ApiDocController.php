<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     title="MCCP API — Multi-Channel Content Processor",
 *     version="1.0.0",
 *     description="API REST para procesar mensajes con IA y distribuirlos a múltiples canales (Email, Slack, SMS).",
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
 *     description="Estado del servicio"
 * )
 *
 * @OA\Tag(
 *     name="Messages",
 *     description="Creación y consulta de mensajes"
 * )
 */
class ApiDocController extends Controller
{
    /**
     * @OA\Get(
     *     path="/health",
     *     tags={"Health"},
     *     summary="Health check del sistema",
     *     description="Verifica que el backend, la base de datos y el queue worker estén operativos.",
     *     operationId="healthCheck",
     *     @OA\Response(
     *         response=200,
     *         description="Sistema operativo",
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
     *         description="Error en el sistema"
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
