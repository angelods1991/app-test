<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\PurchaserWallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    protected $purchaserWallet;

    public function __construct()
    {
        $this->purchaserWallet = new PurchaserWallet();
    }

    public function wallet_balance(Request $request){
        if(empty($request->input('wallet_code'))){
            $data = [
                'status' => 'fail',
                'message' => 'Please Enter Wallet Code'
            ];

            return $data;
        }

        $wallet_balance = $this->purchaserWallet->api_wallet_balance($request->input('wallet_code'));

        if(empty($wallet_balance[0])){
            $data = [
                'status' => 'fail',
                'message' => 'Wallet Does Not Exist'
            ];

            return $data;
        }

        $wallet_balance[0]->wallet_balance = number_format($wallet_balance[0]->wallet_balance,2);
        $wallet_balance[0]->wallet_lock_balance = number_format($wallet_balance[0]->wallet_lock_balance,2);
        $wallet_balance[0]->wallet_available_balance = number_format($wallet_balance[0]->wallet_available_balance,2);
        $wallet_balance[0]->referral_bonus = number_format($wallet_balance[0]->referral_bonus,2);

        $data = [
            'status' => 'success',
            'data' => $wallet_balance[0]
        ];

        return $data;
    }

 }
