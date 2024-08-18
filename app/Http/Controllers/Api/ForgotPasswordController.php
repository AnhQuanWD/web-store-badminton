<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/password/forgot",
     *      operationId="sendResetLinkEmail",
     *      tags={"Authentication"},
     *      summary="Request password reset link",
     *      description="Send password reset link to user email",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email"},
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Reset link sent",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid email",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string")
     *          )
     *      )
     * )
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid email'], 400);
        }

        $status = Password::sendResetLink($request->only('email'));

        if ($status == Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Reset link sent'], 200);
        } else {
            return response()->json(['error' => 'Failed to send reset link'], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/password/reset",
     *      operationId="resetPassword",
     *      tags={"Authentication"},
     *      summary="Reset password",
     *      description="Reset password using token",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"token","email","password","password_confirmation"},
     *              @OA\Property(property="token", type="string", example="abcdef"),
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="newpassword"),
     *              @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Password reset successful",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string")
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid input",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Password reset failed",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string")
     *          )
     *      )
     * )
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid input'], 400);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successful'], 200);
        } else {
            return response()->json(['error' => 'Password reset failed'], 500);
        }
    }
}
