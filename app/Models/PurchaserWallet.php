<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaserWallet
{
    protected $table = "purchaser_wallets";

    public function create_wallet(Request $request, $wallet_code)
    {
        if (!$this->wallet_exists($request->input('purchaser_id'))) {
            
            DB::table($this->table)
                ->insert([
                    'purchaser_id' => $request->input('purchaser_id'),
                    'wallet_balance' => 0,
                    'wallet_lock_balance' => 0,
                    'wallet_available_balance' => 0,
                    'referral_bonus' => -0,
                    'wallet_code' => $wallet_code
                ]);
        }

        return $wallet_code;
    }

    public function wallet_store($purchaser_id,$total_tokens ,$locked_tokens,$incentive_tokens)
    {

        $wallet_balance = $total_tokens;

        $wallet_lock_balance = $locked_tokens;

        $wallet_available_balance = $incentive_tokens;

        DB::table($this->table)
            ->where('purchaser_id', '=', $purchaser_id)
            ->update([
                'wallet_balance' => DB::raw('wallet_balance + ' . $wallet_balance),
                'wallet_lock_balance' => DB::raw('wallet_lock_balance + '. $wallet_balance),
                //'wallet_lock_balance' => DB::raw('wallet_lock_balance + '. $wallet_lock_balance),
                //'wallet_available_balance' => DB::raw( 'wallet_available_balance + '. $wallet_available_balance),

            ]);
    }

    public function show($id)
    {
        return DB::table($this->table)->where('purchaser_id','=', $id)->get();
    }

   /* public function create_wallet_code($length = 16) {
        $code_characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code_length = strlen($code_characters);

        $wallet_code = '';

        for ($i = 0; $i < $length; $i++) {
            $wallet_code .= $code_characters[rand(0, $code_length - 1)];
        }

        if($this->code_exist($wallet_code)){
            $this->create_wallet_code();
        }

        return $wallet_code;
    }*/

    public function code_exist($wallet_code)
    {
        return DB::table($this->table)->where('wallet_code', '=', $wallet_code)->count() == 1;
    }

    /** API - METHODS */

    public function api_wallet_balance($wallet_code){
        return DB::table($this->table)
            ->where('wallet_code', '=', $wallet_code)->select(['wallet_balance','wallet_lock_balance','wallet_available_balance','referral_bonus'])->get();
    }

    /** PRIVATE FUNCTIONS */

    private function wallet_exists($purchaser_id)
    {
        return DB::table($this->table)->where('purchaser_id', '=', $purchaser_id)->count() == 1;
    }
}
