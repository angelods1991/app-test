<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use App\Models\RoleModel;
use App\Models\UsersModel;
use App\Models\MenuModel;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->users = new User();
        $this->role = new RoleModel();
        $this->userModel = new UsersModel();
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function doLogin(Request $request)
    {

        $rules = array(
            'email' => 'required|email',
            'password' => 'required|alphaNum|min:3'
        );

        $validator = Validator::make(Input::all(), $rules);


        if ($validator->fails()) {

            return Redirect::to('login')->with('errors', $validator->errors()->all())
                ->withInput(Input::except('password'));
        } else {

            $userdata = array(
                'email' => Input::get('email'),
                'password' => Input::get('password')
            );

            $remember = (Input::has('remember')) ? true : false;
            $minutes = 30;

            if (Auth::attempt($userdata,$remember)) {
                $uid = Auth::user()->id;
                $query_response = $this->userModel->getDataByID($uid);
                session(['logged_in' => true,'role_name' => $query_response->role_name]);
                $this->setCookie($remember,$minutes);
                $this->setMenuSession($request);

                return redirect()->intended('/dashboard');

            } else {

                return redirect()->intended("/login")
                    ->withInput()
                    ->with('errors', ['Account does not exist!']);
            }

        }
    }

    private function setMenuSession($request)
    {
        $user_id = $request->user()->id;
        $user_count = $this->userModel->checkRole($user_id);

        if($user_count==1){
            $user = $this->userModel->getDataByID($user_id);
            $role_id = $user->role_id;
            $menus = $this->role->getActiveMenus($role_id);

            foreach ($menus as $key => $value) {
                $active_menu = str_replace(" ","",strtolower($value->name));

                session([$active_menu => 'active']);
                $array_container[] = $active_menu;

                $url_container[] = url('/').$value->link;

                $this->activeMethods($active_menu,$value->menu_id);
            }

            session(['links' => $url_container]);
        }
    }

    private function activeMethods($active_menu,$menu_id)
    {
        $MenuModel = new MenuModel();

        $response = $MenuModel->getMenuPermissions($menu_id);
        foreach ($response as $key => $value) {
            $permission = str_replace(" ","",strtolower($value->name));

            session([$active_menu.$permission => $permission]);

        }
    }

    private function setCookie($remember,$minutes)
    {
        if($remember==true){
          Cookie::queue('email',Input::get('email'),$minutes);
          Cookie::queue('remember',$remember,$minutes);
        }else{
          Cookie::queue(Cookie::forget('email'));
          Cookie::queue(Cookie::forget('remember'));
        }
    }

    public function doLogout(Request $request)
    {
        $request->session()->flush();
        Auth::logout();
        redirect("/login");
    }
}
