<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * BaseController provides common API response methods.
 * Other API controllers can extend this class to use standardized responses.
 */
class BaseController extends Controller
{
    /**
     * Send a successful JSON response.
     *
     * @param mixed $data    The data to return in the response
     * @param string $message  A message describing the response
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($data, $message)
    {
        return response()->json([
            'success' => true,  // Indicates success
            'data' => $data,    // Response data
            'message' => $message // Informational message
        ], 200); // HTTP status code 200 OK
    }

    /**
     * Send an error JSON response.
     *
     * @param string $message  The error message
     * @param array $errors    Optional array of detailed errors
     * @param int $code        HTTP status code (default 401 Unauthorized)
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($message, $errors = [], $code = 401)
    {
        return response()->json([
            'success' => false, // Indicates failure
            'message' => $message, // Error message
            'errors' => $errors   // Optional detailed errors
        ], $code); // HTTP status code
    }
}
