<?php 
namespace App\Http\Controllers;
use User, Input, Session, Hash, Redirect, Exception, Config, URL, Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use App\Http\Controllers\Controller;
use Tokenly\TokenpassClient\Facade\Tokenpass;

class AccountController extends Controller {
	
	public function login()
	{
		if(Auth::user()){
			//already logged in, redirect to home
			return Redirect::route('account.home');
		}
		//else redirect to oauth provider
		return Socialite::redirect();
	}
	
	public function home()
	{
		$user = Auth::user();
		if(!$user){
			return Redirect::route('account.login');
		}
		return view('account.home', array('user' => $user));
	}
	
    public function handleTokenpassCallback(Request $request)
    {
        try {
            // check for an error returned from Tokenly Accounts
            $error_description = Tokenpass::checkForError($request);
            if ($error_description) {
                return view('account.authorization-failed', ['error_msg' => $error_description]);
            }
			
            // retrieve the user from Tokenly Accounts
            $oauth_user = Socialite::user();
            
            
            // get all the properties from the oAuth user object
            $tokenly_uuid       = $oauth_user->id;
            $oauth_token        = $oauth_user->token;
            $username           = $oauth_user->user['username'];
            $name               = $oauth_user->user['name'];
            $email              = $oauth_user->user['email'];
            
            // find an existing user based on the credentials provided
            $existing_user = User::where('tokenly_uuid', $tokenly_uuid)->first();

            // if an existing user wasn't found, we might need to find a user to merge into
            $mergable_user = ($existing_user ? null : User::where('username', $username)->orWhere('email', $email)->where('tokenly_uuid', null)->first());
            $used_user = false;
            
            if ($existing_user) {
                // update the user
                $existing_user->update(['oauth_token' => $oauth_token, 'name' => $name, 'email' => $email, 
										'tokenly_uuid' => $tokenly_uuid, 'username' => $username ]);

                $used_user = $existing_user;
                
            } else if ($mergable_user) {
                // an existing user was found with a matching username
                //  migrate it to the tokenly accounts control

                if ($mergable_user['tokenly_uuid']) {
                    throw new Exception("Can't merge a user already associated with a different tokenpass account", 1);
                }

                // update if needed
                $mergable_user->update(['name' => $name, 'email' => $email, 'oauth_token' => $oauth_token,
										'username' => $username, 'tokenly_uuid' => $tokenly_uuid]);

                $used_user = $mergable_user;

            } else {
                // no user was found - create a new user based on the information we received
                $create_data = ['tokenly_uuid' => $tokenly_uuid, 'oauth_token' => $oauth_token, 'name' => $name, 'username' => $username, 'email' => $email ];
                $new_user = User::create($create_data);
                
                $used_user = $new_user;
            }
            
            Auth::login($used_user);
            return redirect('/account/login');

        } catch (Exception $e) {
            // some unexpected error happened
            return view('account.authorization-failed', ['error_msg' => 'Failed to authenticate this user.']);
        }
    }	
	
	
    public function logout() {
        Auth::logout();
        return Redirect::to('/');
    }
    
}
