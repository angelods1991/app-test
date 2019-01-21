<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CurrencyRateModel;
use App\Models\RateModel;

class CurrencyRateController extends Controller
{
    protected $access_key = "1eefe393f49e770e0e86cf5d45a15e28";
    protected $base = "USD";
    protected $timestamp = '';

    public function __construct()
    {
        $this->CurrencyModel = new CurrencyRateModel();
        $this->RateModel = new RateModel();
        $this->timestamp = date('Y-m-d H:i:s');
        $this->access_code = md5($this->access_key);
    }

    public function reloadCurrencyRate(Request $request)
    {
        if($request->isMethod('get'))
        {
            if($request->input('access_code')==$this->access_code)
            {
                $codes = $this->getCurrencyCode();
                $response = $this->getLatestCurrencyCode($codes);

                if(!isset($response['result']))
                {
                    $decoded = json_decode($response);
                    $edcoin_rate = $this->getEDCoinCurrencyRate();

                    if($decoded->success!=false)
                    {
                        $countries = $this->CurrencyModel->getCountries();
                        foreach ($countries as $value)
                        {
                            $country_id = $value->id;
                            $country_name = $value->country_name;
                            $currency_code = $value->currency_code;
                            $currency_rate = $decoded->rates->$currency_code;
                            $edcoin_value = $currency_rate * $edcoin_rate;

                            $count_currency = $this->CurrencyModel->countCurrencyRate($country_id);
                            if($count_currency>0)
                            {
                                $id = $this->CurrencyModel->getCurrencyRateID($country_id);
                                $this->CurrencyModel->modify($id,$country_id,
                                $currency_rate,$edcoin_value,$this->timestamp);
                            }
                            else
                            {
                                $id = $this->CurrencyModel->create($country_id,
                                $currency_rate,$edcoin_value,$this->timestamp);
                            }

                            $this->CurrencyModel->createLogs($id,$country_name,$currency_code,$currency_rate,$edcoin_value,$this->timestamp);
                        }

                        $data['result'] = 'success';
                        $data['message'] = 'Currency rate was successfully registered';
                    }
                    else
                    {
                        $data['result'] = 'fail';
                        $data['type'] = $decoded->error->type;
                        $data['message'] = $decoded->error->info;
                    }
                }
                else
                {
                    $data['result'] = $response['result'];
                    $data['message'] = $response['message'];
                }
            }
            else
            {
                $data['result'] = 'fail';
                $data['message'] = 'Invalid Access Code!';
            }
        }
        else
        {
            $data['result'] = 'fail';
            $data['message'] = 'Invalid Method!';
        }

        return $data;
    }

    private function getLatestCurrencyCode($currency_code)
    {
        $activity = 'latest';
        $parameters = 'access_key='.$this->access_key.'&base='.$this->base.'&symbols='.$currency_code;

        $response = $this->getCurrencyRate($activity,$parameters);

        if($response==false)
        {
            $data['result'] = 'fail';
            $data['message'] = 'Connection Lost!';
        }
        else
        {
            $data = $response;
        }

        return $data;
    }

    private function parseAPIConversionResponse()
    {
        generateCurrencyRate($currency_code);
    }

    private function generateCurrencyRate($currency_code)
    {
        $activity = 'convert';
        $edcoin_rate = $this->getEDCoinCurrencyRate();

        $parameters = 'access_key='.$this->access_key.'&from='.$this->base.
        '&to='.$currency_code.'&amount='.$edcoin_rate;

        $response = $this->getCurrencyRate($activity,$parameters);

        if($response==false)
        {
            $data['result'] = 'fail';
            $data['message'] = 'Connection Lost!';
        }
        else
        {
            $data = $response;
        }

        return $data;
    }

    private function getEDCoinCurrencyRate()
    {
        $count = $this->RateModel->countEdcoinRate();
        if($count>0)
        {
            $info = $this->RateModel->getLatestRate();
            $edcoin_rate = round($info->rate,2);
        }
        else
        {
            $edcoin_rate = 0.00;
        }

        return $edcoin_rate;
    }

    private function getCurrencyCode()
    {
        $code_container = array();
        $currency_code = '';
        $iter = 1;

        $count = $this->CurrencyModel->countCountries();

        if($count>0)
        {
            $countries = $this->CurrencyModel->getCountries();
            foreach ($countries as $value)
            {

                if($iter<$count)
                {
                  $concat = ',';
                }
                else
                {
                  $concat = '';
                }

                if(!in_array($value->currency_code,$code_container)):
                  $currency_code .= $value->currency_code.$concat;
                  $code_container[] = $value->currency_code;
                endif;

                $iter++;
            }
        }
        else
        {
            $currency_code = false;
        }

        return $currency_code;
    }

    private function getCurrencyRate($activity,$parameters)
    {
        $init = curl_init();

        curl_setopt_array($init, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => 'http://data.fixer.io/api/'.$activity.'?'.$parameters,
        ));

        $response = curl_exec($init);

        curl_close($init);

        return $response;
    }
}
