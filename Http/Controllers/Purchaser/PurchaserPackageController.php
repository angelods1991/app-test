<?php

namespace App\Http\Controllers\Purchaser;

use App\Http\Controllers\BonusActivityController;
use App\Http\Controllers\Controller;
use App\Models\PurchaserPackage;
use App\Models\PurchaserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PurchaserPackageController extends Controller
{
    protected $purchaserPackage, $purchaserWallet, $BonusActivityController;

    public function __construct()
    {
        $this->purchaserPackage = new PurchaserPackage();
        $this->purchaserWallet = new PurchaserWallet();
        $this->BonusActivityController = new BonusActivityController();
    }

    public function store(Request $request)
    {
        if (Session::get('method') != 'purchaser-package-create') {
            $data = [
                'status' => 'BAD',
                'message' => 'Invalid Transaction'
            ];

            return $data;
        }

        Session::remove('method');

        if ($request->input('package_incentive_percentage') == "") {
            $request['package_token_percentage'] = 0;
        }

        /*
        $request['package_token_locked'] = $request->input('package_locked_tokens');
        $request['package_token_incentive'] =  $request->input('package_incentive_tokens');
        $request['package_token_total'] =  $request->input('package_tokens');
        */

        $based_token = $this->get_based_token($request->input('package_token_price'), $request->input('package_paid_amount'));
        $incentive_token = $this->get_incentive_token($based_token, $request->input('package_incentive_percentage'));
        $total_token = $this->get_total_token($based_token, $incentive_token);

        $request['package_token_locked'] = $based_token;
        $request['package_token_incentive'] = $incentive_token;
        $request['package_token_total'] = $total_token;

        $this->purchaserPackage->store($request);

        $data = [
            'status' => 'OK',
            'message' => 'Package Added to Purchaser',
        ];

        return $data;
    }

    public function show($id)
    {

        $package = $this->purchaserPackage->show($id);

        $upline_data = $this->setUplineData($package[0]->purchaser_id, $package[0]->package_token_locked);

        $data = [
            'status' => 'OK',
            'data' => $package[0],
            'upline' => $upline_data
        ];

        return $data;
    }

    public function table()
    {
        return $this->purchaserPackage->table();
    }

    public function package_post(Request $request)
    {
        $modified_by = $request->user()->id;
        $modified_date = date('Y-m-d H:i:s');
        $package_id = $request->input('package_id');
        $this->purchaserPackage->package_transact($package_id, 1,$modified_by,$modified_date);
    }

    public function package_reject(Request $request)
    {
        $this->purchaserPackage->package_transact($request->input('package_id'), 2);

        $data = [
            'status' => 'OK',
            'message' => 'Package has Been Successfully Rejected.'
        ];

        return $data;
    }

    public function compute_token(Request $request)
    {
        $valid_numeric = true;

        if (empty($request->input('package_incentive'))) {
            $request['package_incentive'] = 0;
        }

        if ($valid_numeric) {

            $based_token = $this->get_based_token($request->input('package_token_price'), $request->input('package_paid_amount'));
            $incentive_token = $this->get_incentive_token($based_token, $request->input('package_incentive_percentage'));
            $total_token = $this->get_total_token($based_token, $incentive_token);

            $data = [
                'status' => 'OK',
                'data' => [
                    'package_tokens' => $total_token,
                    'package_locked_tokens' => $based_token,
                    'package_incentive_tokens' => $incentive_token
                ]
            ];

        } else {

            $data = [
                'status' => 'OK',
                'data' => [
                    'package_tokens' => 0,
                    'package_locked_tokens' => 0,
                    'package_incentive_tokens' => 0
                ]
            ];

        }

        return $data;
    }

    public function validate_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_token_price' => 'required|numeric|min:0.12',
            'package_paid_amount' => 'required|numeric|min:0.12',
            'package_incentive_percentage' => 'required|numeric|min:0',
            'package_method' => 'required',
            'package_remarks' => 'required'
        ],
            [
                'package_token_price.required' => 'The token price field is required.',
                'package_paid_amount.required' => 'The paid amount field is required.',
                'package_incentive_percentage.required' => 'The incentive percentage field is required',
                'package_method.required' => 'The payment method field is required',
                'package_remarks.required' => 'The remarks field is required',

                'package_token_price.numeric' => 'The token price must be a number.',
                'package_paid_amount.numeric' => 'The paid amount must be a number.',
                'package_incentive_percentage.numeric' => 'The incentive percentage must be a number.',

                'package_token_price.min' => 'The token price must be higher or equal to :min.',
                'package_paid_amount.min' => 'The paid amount must be higher or equal to :min.',
                'package_incentive_percentage.min' => 'The incentive percentage be higher or equal to :min.'
            ]);

        if ($validator->fails()) {
            $data = [
                'status' => 'BAD',
                'message' => $validator->errors()->all()
            ];

            return $data;
        }

        Session::put('method', 'purchaser-package-create');

        $data = [
            'status' => 'OK'
        ];

        return $data;
    }


    /** Private Functions **/

    private function get_based_token($token_price, $amount_paid, $decimal = 18)
    {
        return bcdiv($amount_paid, $token_price, $decimal);
    }

    private function get_incentive_token($based_token, $incentive_percentage, $decimal = 18)
    {
        return bcmul($based_token, ($incentive_percentage / 100), $decimal);
    }

    private function get_total_token($based_token, $incentive_token, $decimal = 18)
    {
        return bcadd($based_token, $incentive_token, $decimal);
    }

    private function setUplineData($purchaser_id, $package_total)
    {
        $data = [];

        $upline = $this->BonusActivityController->showBonusDetails($purchaser_id, $package_total);
        $iter = 1;

        foreach ($upline as $value) {
            $details = explode('|', $value);
            $data['upline_name_' . $iter] = $details[0];
            $data['upline_token_' . $iter] = $details[1];

            $iter++;
        }

        return $data;
    }
}
