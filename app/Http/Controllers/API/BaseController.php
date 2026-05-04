<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *    title="Laravel 12 REST API Documentation",
 *    version="1.0.0",
 *    description="API endpoints for Product and User management system",
 *    @OA\Contact(
 *        email="admin@example.com"
 *    )
 * )
 * 
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Main API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class BaseController extends Controller
{
    public function sendResponse($data, $message)
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message
        ], 200);
    }

    public function sendError($message, $errors = [], $code = 404)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
}