<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\RateModel;

class ShowMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $edcoin_rate = $this->edcoinData();

        if(is_null(Session::get('logged_in')) || is_null(Session::get('links')))
        {
            Auth::logout();
            Session::flush();

            return redirect()->intended('/login');
        }

        \View::share('edcoin_rate',$edcoin_rate);
        return $next($request);
    }

    private function edcoinData()
    {
        $this->RateModel = new RateModel();
        $count = $this->RateModel->countEdcoinRate();

        if($count>0)
        {
            $response = $this->RateModel->getLatestRate();
            $data = array(
              'rate' => number_format(round($response->rate,2),2),
              'created_by' => $this->getUserName($response->created_by),
              'created_date' => $response->created_date,
              'updated_by' => ($response->updated_by==NULL?'None':$this->getUserName($response->updated_by)),
              'updated_date' => ($response->updated_date==NULL?'None':$response->updated_date)
            );
        }
        else
        {
            $data = array(
              'rate' => '0.00',
              'created_by' => 'None',
              'created_date' => 'None',
              'updated_by' => 'None',
              'updated_date' => 'None',
            );
        }

        return $data;
    }

    private function getUserName($user_id)
    {
        $this->RateModel = new RateModel();
        $name = $this->RateModel->getUserName($user_id);

        return $name;
    }
}
