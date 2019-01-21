<?php

namespace App\Http\Controllers\ApiController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ConversionModel;
use App\Models\TransactionModel;
use App\Models\RateModel;
use App\Models\CurrencyRateModel;
use Illuminate\Support\Facades\Validator;

class ConversionController extends Controller
{
    protected $access_code = "`_`3Dm@rK`4dm1n`ConV3rs10n`_`";
    protected $access_key = "1eefe393f49e770e0e86cf5d45a15e28";
    protected $base = "USD";
    protected $timestamp = '';
    protected $md5combKey = "";

    public function __construct()
    {
        $this->date = strtotime(date("Y-m-d"));
        $this->encrypt = md5($this->access_code);
        $this->md5combKey = md5('ED2E*edmark#Se'.$this->date);
        $this->ConversionModel = new ConversionModel();
        $this->TransactionModel = new TransactionModel();
        $this->CurrencyModel = new CurrencyRateModel();
        $this->RateModel = new RateModel();
    }

    public function convert(Request $request)
    {
        $data = array();

        if($request->isMethod('post'))
        {
            if($this->encrypt==$request->input('access_code'))
            {
                $validate = Validator::make($request->all(),[
                  'rate' => 'required',
                  'custid' => 'required',
                  'coinamount' => 'required'
                ]);

                if(!$validate->fails())
                {
                    $rate = $request->input('rate');
                    $wallet_code = $request->input('wallet_code');
                    // $country_code = $request->input('country_code');
                    $custid = $request->input('custid');
                    $coinamount = $request->input('coinamount');

                    $validate_wallet_code = $this->ConversionModel->checkWalletCode($wallet_code);

                    if($validate_wallet_code==1)
                    {
                        $wallet_data = $this->ConversionModel->getWalletCodeDistributor($wallet_code);
                        $purchaser_id = $wallet_data->purchaser_id;

                        $edcoin_vailable_token = $this->ConversionModel->getWalletToken($purchaser_id);

                        $validation_response = $this->validateEDCoinValue($purchaser_id,$coinamount,$edcoin_vailable_token);

                        $data = $validation_response;
                        if($data['status'] == "success"):
                            // $currency_data = $this->CurrencyModel->getCurrencyData($country_code);
                            $response = $this->convertEDCoinRateFromCIS($data,$custid,$coinamount,$rate);
                            $data = $response;
                        endif;
                    }
                    else
                    {
                          $data['status'] = 'fail';
                          $data['message'] = 'Invalid Wallet Code';
                    }

                }
                else
                {
                    $data['status'] = 'fail';
                    $data['message'] = $validate->errors()->first();
                }
            }
            else
            {
                $data['status'] = 'fail';
                $data['message'] = 'Invalid Access Code';
            }

        }
        else
        {
            $data['status'] = 'fail';
            $data['message'] = 'Invalid Method';
        }

        echo json_encode($data);
    }

    private function validateEDCoinValue($purchaser_id,$edcoin_value,$edcoin_vailable_token)
    {
        $data = '';

        if($edcoin_vailable_token>=$edcoin_value)
        {
            $data = [
                'status' => 'success',
                'message' => 'Your EDCoin balance is enough to convert to EDPoint',
                'purchaser_id' => $purchaser_id
            ];
        }
        else
        {
            $data = [
                'status' => 'fail',
                'message' => 'You are trying to convert an amount that exceeds your available EDC Balance: ',
                'edcoin_available_balance' => number_format($edcoin_vailable_token,2) . " EDC"
            ];
        }

        return $data;
    }

    private function insertTransactionLogs($transaction_no,$purchaser_id,$amount,$edp_amount,$description)
    {
        $this->TransactionModel->create($transaction_no,$purchaser_id,$amount,$edp_amount,$description);

        return $transaction_no;
    }

    private function generateTransactionNo()
    {
        $init_no = "00000000000";
        $flag = false;

        $count_logs = $this->TransactionModel->countTransactionNo();
        $set_no = substr($init_no,0,strlen($init_no)-strlen($count_logs+1));
        $transaction_no = $set_no.($count_logs+1);

        do
        {
            $count = $this->TransactionModel->checkTransactionNo($transaction_no);
            if($count==0)
            {
                $flag=true;
            }
            else
            {
                $transaction_no = ($transaction_no+1);
            }
        }
        while($flag==false);

        return $transaction_no;
    }

    public function checkTotalEDPoint(Request $request)
    {
        if($request->isMethod('post'))
        {
            if($this->encrypt==$request->input('access_code'))
            {

              $validate = Validator::make($request->all(),[
                'company_code' => 'required',
                'custid' => 'required',
                'coinamount' => 'required'
              ]);

              if(!$validate->fails())
              {
                // $apiName = "Solucis:apiNotificationAmount";
                $custid = $request->input('custid');
                $coinamount = $request->input('coinamount');
                $company_code = $request->input('company_code');

                // $post = array(
                //               'page' => $apiName,
                //               'custid' => $custid,
                //               'coinamount' =>$coinamount,
                //               'vc' =>$this->md5combKey,
                //               'date' => $this->date
                //               );

                // $data = $this->connectToCisApi($post);
                //
                // if($data->status == "success"):
                $currency_data = $this->CurrencyModel->getCurrencyData($company_code);
                $currency_code = $currency_data->currency_code;

                $currency_response = $this->generateCurrencyRate($currency_code);

                if($currency_response->status=='success')
                {
                    $currency_rate = $currency_response->result;
                    $total_point = $currency_rate * $coinamount;

                    $data = array(
                        'status'=>'success',
                        'custid'=>$custid,
                        'EDCoin'=>$coinamount,
                        'total_point'=>number_format(round($total_point,2),2)
                    );
                }
                else
                {
                    $data['status'] = 'fail';
                    $data['message'] = $currency_response->error->info;
                }

                // endif;
              }
              else
              {
                  $data['status'] = 'fail';
                  $data['message'] = $validate->errors()->first();
              }
            }
            else
            {
                $data['status'] = 'fail';
                $data['message'] = 'Invalid Access Code';
            }

        }
        else
        {
            $data['status'] = 'fail';
            $data['message'] = 'Invalid Method';
        }

        echo json_encode($data);
    }

    public function checkCoinRate(Request $request)
    {
        if($request->isMethod('post'))
        {
            if($this->encrypt==$request->input('access_code'))
            {
                $validate = Validator::make($request->all(),[
                  'company_code' => 'required'
                ]);

                if(!$validate->fails())
                {
                    $company_code = $request->input('company_code');

                    $currency_data = $this->CurrencyModel->getCurrencyData($company_code);

                    $currency_code = $currency_data->currency_code;

                    $currency_response = $this->generateCurrencyRate($currency_code);
                    if($currency_response->success==true)
                    {
                      $data = array(
                          'status'=> 'success',
                          'companycode'=>$company_code,
                          'rate'=>number_format(round($currency_response->result,2),2)
                      );
                    }
                    else
                    {
                        $data['status'] = 'fail';
                        $data['message'] = $currency_response->error->info;
                    }
                }
                else
                {
                    $data['status'] = 'fail';
                    $data['message'] = $validate->errors()->first();
                }
            }
            else
            {
                $data['status'] = 'fail';
                $data['message'] = 'Invalid Access Code';
            }
        }
        else
        {
            $data['status'] = 'fail';
            $data['message'] = 'Invalid Method';
        }

        echo json_encode($data);
    }

    private function getEdaNumberToCIS($custid)
    {
        $apiName = "Solucis:apiNotificationAmount";

        $post = [
          'page' => $apiName,
          'custid' => $custid,
          'vc' =>$this->md5combKey,
          'date' => $this->date,
          'coinamount' => '1'
        ];

        $cis_response = $this->connectToCisApi($post);

        if($cis_response->status=="success")
        {
            $eda_number = '('.$cis_response->eda.')';
        }
        else
        {
            $eda_number = '';
        }

        return $eda_number;
    }

    private function convertEDCoinRateFromCIS($data_array,$custid,$coinamount,$currency_rate)
    {
          $contributor_number = $this->getEdaNumberToCIS($custid);
          $apiName = "Solucis:apiAppCoinConvertToEP";
          $transaction_no = $this->generateTransactionNo();

          $description = "Converted to EP " . $contributor_number;
          // $currency_code = $currency_data->currency_code;

          // $currency_response = $this->generateCurrencyRate($currency_code);
          //
          // if($currency_response->success==true)
          // {
          //     $currency_rate = $currency_response->result;
              $purchaser_id = $data_array['purchaser_id'];
              $to_currency = $currency_rate * $coinamount;

              $post = [
                'page' => $apiName,
                'custid' => $custid,
                'vc' =>$this->md5combKey,
                'date' => $this->date,
                'coinamount' => $coinamount,
                'refno' => "EDCTEST10-".$transaction_no,
                'totalpoint' => $to_currency,
                'rate' => $currency_rate
              ];

              $data = $this->connectToCisApi($post);

              if($data->status == "success"):

                  $reference_no = $this->insertTransactionLogs($transaction_no,$purchaser_id,$coinamount,$data->received_amount,$description);
                  $this->ConversionModel->deductWalletAvailable($purchaser_id,$coinamount);
                  $this->referralBonus($purchaser_id,$coinamount);

                  $data = array(
                      'status'=>$data->status,
                      'reference_no'=>$reference_no,
                      'coin_amount'=>$data->coin_amount,
                      'received_amount'=> number_format(round($to_currency,2),2)
                  );

              endif;
          // }
          // else
          // {
          //     $data = $currency_response;
          // }

        return $data;
    }

    private function referralBonus($purchaser_id,$referral_bonus)
    {
        $response = $this->ConversionModel->getReferralBonus($purchaser_id);
        if($response->referral_bonus>0):
            $deduct_amount = $response->referral_bonus - $referral_bonus;
            $deducted_referral_bonus = ($deduct_amount<0? 0 : $deduct_amount);

            $this->ConversionModel->updateReferralBonus($purchaser_id,$deducted_referral_bonus);
        endif;
    }

    private function connectToCisApi($post)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"http://183.81.166.237/ed2e/index.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post);

        $server_output = curl_exec($ch);

        curl_close ($ch);

        if ($server_output != false) {
            $decoded = json_decode($server_output);
            $data = $decoded;
        } else {
            $decoded = json_decode($server_output);
            $data = $decoded;
        }

        if($data==null):
            $data['status'] = 'fail';
            $data['message'] = 'Connection lost.';
        endif;

        return (object) $data;
    }

    public function getTransactionLogs(Request $request)
    {
        $data = array();

        if($request->isMethod('post'))
        {
            if($this->encrypt==$request->input('access_code'))
            {
                $wallet_code = $request->input('wallet_code');

                $check_wallet_code = $this->ConversionModel->checkWalletCode($wallet_code);
                if($check_wallet_code==1)
                {
                    $wallet_data = $this->ConversionModel->getWalletCodeDistributor($wallet_code);
                    $purchaser_id = $wallet_data->purchaser_id;
                    $response = $this->TransactionModel->getTransactionRecordByID($purchaser_id);

                    if(count($response)>0)
                    {
                        $data['status'] = 'success';
                        $data['message'] = 'Record Found';
                        foreach ($response as $value) {
                            $data['record'][] = array(
                              'reference_no' => $value->reference_no,
                              'edc_amount' => $value->edc_amount,
                              'edp_amount' => $value->edp_amount,
                              'description' => $value->description,
                              'created_date' => $value->created_date
                            );
                        }
                    }
                    else
                    {
                        $data['status'] = 'fail';
                        $data['message'] = 'No Data Found';
                    }
                }
                else
                {
                    $data['status'] = 'fail';
                    $data['message'] = 'Invalid Wallet Code';
                }
            }
            else
            {
                $data['status'] = 'fail';
                $data['message'] = 'Invalid Access Code';
            }

        }
        else
        {
            $data['status'] = 'fail';
            $data['message'] = 'Invalid Method';
        }

        echo json_encode($data);
    }

    public function checkWalletCode(Request $request)
    {
        if($request->isMethod('post'))
        {
            if($this->encrypt==$request->input('access_code'))
            {
                $wallet_code = $request->input('wallet_code');

                $count = $this->ConversionModel->checkWalletCode($wallet_code);
                if($count==1)
                {
                    $response = $this->ConversionModel->getEDANumber($wallet_code);

                    $data['status'] = 'success';
                    $data['message'] = 'Wallet code confirmed';
                    $data['eda_number'] = $response->purchaser_eda;
                }
                else
                {
                    $data['status'] = 'fail';
                    $data['message'] = 'Wallet Code is not registered';
                }
            }
            else
            {
                $data['status'] = 'fail';
                $data['message'] = 'Invalid Access Code';
            }
        }
        else
        {
            $data['status'] = 'fail';
            $data['message'] = 'Invalid Method';
        }

        echo json_encode($data);
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
            $data['success'] = 'fail';
            $data['message'] = 'Connection Lost!';
        }
        else
        {
            $data = json_decode($response);
        }

        return (object) $data;
    }

    private function getEDCoinCurrencyRate()
    {
        $count = $this->RateModel->countEdcoinRate();
        if($count>0)
        {
            $info = $this->RateModel->getLatestRate();
            $edcoin_rate = round($info->rate,18);
        }
        else
        {
            $edcoin_rate = 0.000000000000000000;
        }

        return $edcoin_rate;
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
