<?php

namespace App\Http\Controllers\Purchaser;

use App\Models\PurchaserPackage;
use App\Models\PurchaserWallet;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PurchaserWalletController extends Controller
{
    protected $wallet, $purchaserPackage;

    public function __construct()
    {
        $this->wallet = new PurchaserWallet();
        $this->purchaserPackage = new PurchaserPackage();
    }

    public function show($id)
    {

        $wallet = $this->wallet->show($id);

        if (empty($wallet[0])) {
            $data = [
                'status' => 'BAD',
                'message' => 'No Wallet Found for this Purchaser'
            ];

            return $data;
        }

        /*
        $wallet[0]->wallet_balance = number_format($wallet[0]->wallet_balance,2);
        $wallet[0]->wallet_lock_balance = number_format($wallet[0]->wallet_lock_balance,2);
        $wallet[0]->wallet_available_balance = number_format($wallet[0]->wallet_available_balance,2);
        $wallet[0]->referral_bonus = number_format($wallet[0]->referral_bonus,2);
        */

        $data = [
            'status' => 'OK',
            'data' => $wallet[0]
        ];

        return $data;
    }

    public function store(Request $request)
    {
        $package = $this->purchaserPackage->show($request->input('package_id'));
        $package = $package[0];
        $this->wallet->wallet_store($package->purchaser_id, $package->package_token_total,
            $package->package_token_locked, $package->package_token_incentive);

        $data = [
            'status' => 'OK',
            'message' => 'Package Successfully Posted'
        ];

        return $data;
    }
}
