<?php
// File: app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use App\Services\FirestoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\InvalidPassword;
use Kreait\Firebase\Exception\Auth\UserNotFound;

class AuthController extends Controller
{
    private FirebaseAuth $auth;
    private FirestoreService $firestore;

    public function __construct(FirestoreService $firestore)
    {
        $firebase = (new Factory)->withServiceAccount(config('firebase.credentials.file'));
        $this->auth = $firebase->createAuth();
        $this->firestore = $firestore;
    }

    public function show($mode = 'login')
    {
        $isLogin = $mode !== 'register';
        return view('auth.login', compact('isLogin'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $email = $request->email;
        $password = $request->password;
        $isLogin = $request->isLogin == '1';

        try {
            if ($isLogin) {
                $signInResult = $this->auth->signInWithEmailAndPassword($email, $password);
                $uid = $signInResult->firebaseUserId();
            } else {
                $user = $this->auth->createUserWithEmailAndPassword($email, $password);
                $uid = $user->uid;
                $this->firestore->setRole($uid, 'user');
            }

            $role = $this->firestore->getRole($uid) ?? 'user';

            Session::put([
                'firebase_user' => [
                    'uid' => $uid,
                    'email' => $email,
                    'role' => $role,
                    'authenticated' => true
                ]
            ]);

            return redirect()->route($role === 'admin' ? 'main_admin' : 'main');

        } catch (InvalidPassword|UserNotFound $e) {
            return back()->withInput()->with('error', 'Email atau password salah');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function logout()
    {
        Session::forget('firebase_user');
        return redirect()->route('login');
    }
}