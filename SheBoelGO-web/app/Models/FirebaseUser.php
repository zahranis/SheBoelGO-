<?php
// File: app/Models/FirebaseUser.php
namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;

class FirebaseUser implements Authenticatable
{
    public $uid;
    public $email;
    public $role;

    public function __construct($uid, $email, $role = 'user')
    {
        $this->uid = $uid;
        $this->email = $email;
        $this->role = $role;
    }

    public function getAuthIdentifierName()
    {
        return 'uid';
    }

    public function getAuthIdentifier()
    {
        return $this->uid;
    }

    public function getAuthPassword()
    {
        return null; // Firebase handles password
    }

    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        // Not implemented
    }

    public function getRememberTokenName()
    {
        return null;
    }
}

// File: app/Providers/FirebaseUserProvider.php
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

        if ($uid && $email) {
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
        return null; // Not used in our case
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return true; // Firebase handles validation
    }
}

// File: app/Providers/AuthServiceProvider.php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Services\FirestoreService;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot()
    {
        $this->registerPolicies();

        Auth::provider('firebase', function ($app, array $config) {
            return new FirebaseUserProvider($app->make(FirestoreService::class));
        });
    }
}