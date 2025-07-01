<?php

namespace App\Providers;

use App\Models\FirebaseUser;
use App\Services\FirestoreService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Session;

class FirebaseUserProvider implements UserProvider
{
    private FirestoreService $firestore;

    public function __construct(FirestoreService $firestore)
    {
        $this->firestore = $firestore;
    }

    public function retrieveById($identifier)
    {
        $uid = Session::get('uid');
        $email = Session::get('email');
        $role = Session::get('role');

        if ($uid && $email && $uid === $identifier) {
            return new FirebaseUser($uid, $email, $role);
        }

        return null;
    }

    public function retrieveByToken($identifier, $token)
    {
        return null; // Not implemented for Firebase
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        // Not implemented for Firebase
    }

    public function retrieveByCredentials(array $credentials)
    {
        // Not used in our Firebase implementation
        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // Firebase handles credential validation
        return true;
    }
}