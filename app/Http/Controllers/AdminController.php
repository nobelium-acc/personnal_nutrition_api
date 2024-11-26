<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use App\Models\Utilisateur;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/admin/users/get-list",
     *     tags={"Admin"},
     *     security={{"BearerToken":{}}},
     *     summary="Get users list",
     *     description="Get the app users list",
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     * )
    */
    public function get_users_list(Request $request)
    {

        $users = Utilisateur::where('role', '#', UserRoleEnum::Admin);

        return response()->json([
            'success' => true,
            'message' => 'This process is ok',
            'data' => $users,
        ], 200);
    }


    /**
     * @OA\Post(
     *     path="/api/admin/users/delete/{identifier}",
     *     tags={"Admin"},
     *     security={{"BearerToken":{}}},
     *     summary="Remove user from the system",
     *     description="Delete a user",
     *     @OA\Parameter(
     *         name="identifier",
     *         in="path",
     *         required=true,
     *         description="The identifier of the user to delete (ID or slug)",
     *         @OA\Schema(
     *             type="string",
     *             example="123"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User deleted successfully"),
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     * )
    */
    public function delete_user(Request $request, $identifier)
    {
        try {
            $user = Utilisateur::where('id', $identifier)->first();
            $user->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'This process is ok',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }

    }
}
