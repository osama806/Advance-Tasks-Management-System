<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Services\assetsService;
use App\Models\Role;
use App\Models\User;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    use ResponseTrait;

    /**
     * Create a new user in storage.
     * @param array $data
     * @return array
     */
    public function register(array $data)
    {
        try {
            $role = 'user';

            // check if email request contains @admin OR @manager
            if (strpos($data['email'], '@admin') !== false) {
                $role = 'admin';
            } elseif (strpos($data['email'], '@manager') !== false) {
                $role = 'manager';
            }

            $user = User::createOrFirst([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'password'  => bcrypt($data['password']),
            ]);

            Role::create([
                'name'      =>      $role,
                'user_id'   =>      $user->id
            ]);

            return ['status' => true, 'role'    =>  $role];
        } catch (Exception $e) {
            Log::error('Error registering user: ' . $e->getMessage());
            return ['status' => false, 'msg' => $e->getMessage(), 'code' => 500];
        }
    }

    /**
     * Check if user authorize or unAuthorize
     * @param array $data
     * @return array
     */
    public function login(array $data)
    {
        $credentials = [
            "email"         =>      $data['email'],
            "password"      =>      $data['password']
        ];
        $token = JWTAuth::attempt($credentials);
        if (!$token) {
            return ['status'    =>  false, 'msg'    =>  "username or password is incorrect", 'code' =>  401];
        }
        return ["status"    =>  true, "token"   =>      $token];
    }

    /**
     * Update user profile in storage
     * @param array $data
     * @param \App\Models\User $user
     * @return array
     */
    public function updateProfile(array $data, User $user)
    {
        if (empty($data['name']) && empty($data['password'])) {
            return ['status' => false, 'msg' => 'Not Found Data in Request!', 'code' => 404];
        }
        try {
            if (isset($data['name'])) {
                $user->name = $data['name'];
            }
            if (isset($data['password'])) {
                $user->password = bcrypt($data['password']);
            }
            $user->update();
            return ['status'    =>  true];
        } catch (Exception $e) {
            Log::error('Error update profile: ' . $e->getMessage());
            return ['status'    =>  false, 'msg'    =>  'Failed update profile for user. Try again', 'code' =>  500];
        }
    }

    /**
     * Delete user from storage.
     * @return array
     */
    public function deleteUser()
    {
        try {
            $user = User::find(Auth::id());
            if ($user) {
                $user->delete();
                return ['status' => true];
            }

            return [
                'status' => false,
                'msg' => 'User not found',
                'code' => 404,
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg' => $e->getMessage(),
                'code' => 500,
            ];
        }
    }

    /**
     * Retrive user after deleted
     * @param mixed $userId
     * @return array
     */
    public function restoreUser(array $data)
    {
        try {
            $user = User::withTrashed()->where('email', $data['email'])->first();
            if (!$user) {
                return [
                    'status' => false,
                    'msg' => 'User Not Found',
                    'code' => 404,
                ];
            }
            if ($user->deleted_at === null) {
                return [
                    'status' => false,
                    'msg' => "This user isn't deleted",
                    'code' => 400,
                ];
            }
            $user->restore();
            return ['status' => true,];
        } catch (Exception $e) {
            return [
                'status' => false,
                'msg' => $e->getMessage(),
                'code' => 500,
            ];
        }
    }

    /**
     * Create new comment to user
     * @param array $data
     * @param \App\Models\User $user
     * @return array
     */
    public function addComment(array $data, User $user)
    {
        try {
            $user->comments()->create([
                'content'       =>      $data['content']
            ]);

            return [
                'status'        =>      true
            ];
        } catch (\Throwable $th) {
            return [
                'status'        =>      false,
                'msg'           =>      $th->getMessage(),
                'code'          =>      500
            ];
        }
    }

    /**
     * Create new attachment to user
     * @param array $data
     * @param \App\Models\User $user
     * @return array
     */
    public function addAttach(array $data, User $user)
    {
        // Store the file using the assets service
        $assetsService = new AssetsService();
        $fileURL = $assetsService->storeImage($data['file']);

        try {
            $user->attachments()->create([
                'file_path'       =>        $fileURL,
            ]);

            return [
                'status'        =>      true
            ];
        } catch (\Throwable $th) {
            return [
                'status'        =>      false,
                'msg'           =>      $th->getMessage(),
                'code'          =>      500
            ];
        }
    }
}
