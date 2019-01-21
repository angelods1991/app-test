<?php

namespace App\Http\Controllers\Purchaser;

use App\Exports\PurchaserReport;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\Controller;
use App\Mail\WalletCodeEmail;
use App\Models\Country;
use App\Models\FamilyTreeModel;
use App\Models\Purchaser;
use App\Models\PurchaserPackage;
use App\Models\PurchaserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;


class PurchaserController extends Controller
{
    protected $purchaser, $purchaserPackage, $purchaserWallet, $FamilyTreeModel, $purchaserActivity, $BonusController, $country;

    public function __construct()
    {
        // Auth
        $this->middleware(['auth', 'showMethods']);
        // Models
        $this->purchaser = new Purchaser();
        $this->FamilyTreeModel = new FamilyTreeModel();
        $this->purchaserPackage = new PurchaserPackage();
        $this->purchaserWallet = new PurchaserWallet();
        $this->BonusController = new BonusController();
        $this->country = new Country();
    }

    public function index()
    {
        $data = [
            'header_title' => 'Purchaser Management',
            'active_module' => 'purchasermanagement',
        ];

        $data['membership_options'] = $this->BonusController->membershipList();

        return view('pages.purchaser.purchaser')->with($data);
    }

    public function destroy($id)
    {
        $purchaser = $this->purchaser->show($id);

        if (empty($purchaser[0])) {
            $data = [
                'status' => 'BAD',
                'message' => 'Purchaser Does Not Exist'
            ];

            return $data;
        }

        if ($this->purchaser->check_downline($id) > 0) {
            $data = [
                'status' => 'BAD',
                'message' => 'Purchaser cannot be deleted because he/she has a downlines. Purchaser cannot be deleted because he/she has a downline.'
            ];

            return $data;
        }

        if($purchaser[0]->purchaser_image != null){
            if(Storage::exists('public/img/purchaser/'.$purchaser[0]->purchaser_image)){
                Storage::delete('public/img/purchaser/' . $purchaser[0]->purchaser_image);
            }
        }

        $this->purchaser->destroy($id);

        $data = [
            'status' => 'OK',
            'message' => 'Purchaser Deleted',
        ];

        return $data;
    }

    public function show($id)
    {
        $purchaser = $this->purchaser->show($id);

        if (empty($purchaser[0])) {
            $data = [
                'status' => 'BAD',
                'data' => 404
            ];
        } else {

            if(!Storage::exists('public/img/purchaser/'.$purchaser[0]->purchaser_image)){
                $purchaser[0]->purchaser_image = null;
            }

            $data = [
                'status' => 'OK',
                'data' => $purchaser[0]
            ];
        }

        return $data;
    }

    public function table()
    {
        return $this->purchaser->table();
    }

    public function store(Request $request)
    {
        if (Session::get('method') != 'purchaser-store') {

            $data = [
                'status' => 'BAD',
                'message' => 'Invalid Transaction'
            ];

            return $data;
        }

        Session::remove('method');

        if ($request->input('purchaser_type') == 0) {
            $request['purchaser_eda'] = null;
        }

        if ($request->file('purchaser_image') != null) {
            $image_name = $request->file('purchaser_image')->hashName();
        } else {
            $image_name = null;
        }

        $request['image_name'] = $image_name;

        $id = $this->purchaser->store($request);

        $request['purchaser_id'] = $id;

        if ($request->file('purchaser_image') != null) {
            $request->file('purchaser_image')->store('public/img/purchaser');
        }

        $wallet_code = $this->create_wallet_code();

        $this->purchaserWallet->create_wallet($request, $wallet_code);

        $this->insertFamilyTree(Auth::user()->id, $id, $request);

        $request['wallet_code'] = $wallet_code;

        Mail::to($request->input('purchaser_email'))->send(new WalletCodeEmail($request));

        $data = [
            'status' => 'OK',
            'message' => 'Purchaser Created'
        ];

        return $data;
    }

    public function create_wallet_code()
    {
        $length = 16;
        $code_characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code_length = strlen($code_characters);

        $wallet_code = '';

        for ($i = 0; $i < $length; $i++) {
            $wallet_code .= $code_characters[rand(0, $code_length - 1)];
        }

        if ($this->purchaserWallet->code_exist($wallet_code)) {
            $wallet_code = $this->create_wallet_code();
        }

        return $wallet_code;
    }

    public function update(Request $request, $id)
    {
        if (Session::get('method') != 'purchaser-update') {

            $data = [
                'status' => 'BAD',
                'message' => 'Invalid Transaction'
            ];

            return $data;
        }

        Session::remove('method');

        $purchaser = $this->purchaser->show($id);

        if (empty($purchaser[0])) {
            $data = [
                'status' => 'BAD',
                'message' => 'Purchaser Does Not Exist'
            ];

            return $data;
        }

        if ($request->input('purchaser_type') == 0) {
            $request['purchaser_eda'] = null;
        }

        if ($request->file('purchaser_image') != null) {
            $request['image_name'] = $request->file('purchaser_image')->hashName();
        } else {
            $request['image_name'] = $purchaser[0]->purchaser_image;
        }

        $this->purchaser->update($request, $id);

        if ($request->file('purchaser_image') != null) {
            $request->file('purchaser_image')->store('public/img/purchaser');

            if(Storage::exists('public/img/purchaser/' . $purchaser[0]->purchaser_image)){
                Storage::delete('public/img/purchaser/' . $purchaser[0]->purchaser_image);
            }
        }

        if($request->input('purchaser_delete_image') && $purchaser[0]->purchaser_image != null){
            Storage::delete('public/img/purchaser/' . $purchaser[0]->purchaser_image);
        }

        $level = $this->checkLevel($request->input('referral'));
        $this->FamilyTreeModel->modify($request, $id, $level);

        $data = [
            'status' => 'OK',
            'message' => 'Purchaser Updated'
        ];
        return $data;
    }

    public function validate_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchaser_country' => 'required',
            'purchaser_name' => 'required|max:191',
            'purchaser_email' => 'required|email|max:191|unique:purchaser,purchaser_email',
            'purchaser_eda' => 'nullable|required_if:purchaser_type,1|unique:purchaser,purchaser_eda',
            'purchaser_status' => 'required|boolean',
            'purchaser_membership' => 'required',
            'purchaser_contact' =>'nullable|numeric',
            'purchaser_image' => 'nullable|image|max:2000'
        ], [
            'purchaser_country.required' => 'The country field is required.',
            'purchaser_name.required' => 'The name field is required.',
            'purchaser_email.required' => 'The email address field is required.',
            'purchaser_email.email' => 'The email address must be a valid email address.',
            'purchaser_email.unique' => 'The email address has already been taken.',
            'purchaser_eda.required_if' => 'The EDA number is required if the member type is \'Existing Member\'.',
            'purchaser_eda.unique' => 'The EDA number is already taken.',
            'purchaser_membership.required' => 'The membership field is required.',

            'purchaser_contact.numeric' => 'The contact no. field must be numeric.',

            'purchaser_image.image' => 'The photo field must be a image.',
            'purchaser_image.max' => 'The photo is too large, 2mb is the maximum file size.'
        ]);

        if ($validator->fails()) {
            $data = [
                'status' => 'BAD',
                'message' => $validator->errors()->all()
            ];

            return $data;
        }

        $data = [
            'status' => 'OK'
        ];

        Session::put('method', 'purchaser-store');
        return $data;
    }

    public function validate_update(Request $request, $id)
    {
        $purchaser = $this->purchaser->show($id);

        if (empty($purchaser[0])) {
            $data = [
                'status' => 'BAD',
                'message' => ['Purchaser Does Not Exist']
            ];

            return $data;
        }

        $validator = Validator::make($request->all(), [
            //'purchaser_name' => 'required|max:191',
            'purchaser_email' => 'required|email|max:191|unique:purchaser,purchaser_email,' . $id,
            'purchaser_eda' => 'nullable|required_if:purchaser_type,1|unique:purchaser,purchaser_eda,' . $id,
            //'purchaser_status' => 'required|boolean',
            //'referral' => 'not_in:' . $id,
            //'referred' => 'required_if:referral,on'
            'purchaser_contact' =>'nullable|numeric',
            'purchaser_image' => 'nullable|image|max:2000'
        ], [
            //'referral.not_in' => 'The referral cannot be the same as the purchaser.',
            //'purchaser_name.required' => 'The name field is required.',
            'purchaser_email.required' => 'The email address field is required.',
            'purchaser_email.email' => 'The email address must be a valid email address.',
            'purchaser_email.unique' => 'The email address has already been taken.',
            'purchaser_eda.required_if' => 'The EDA number is required if the member type is \'Existing Member\'.',
            'purchaser_eda.unique' => 'The EDA number is already taken.',
            'purchaser_membership.required' => 'The membership field is required.',
            'purchaser_contact.numeric' => 'The contact no. field must be numeric.',
            //'purchaser_contact.required' => 'The contact no. field is required.',
            'purchaser_image.image' => 'The photo field must be a image.',
            'purchaser_image.max' => 'The photo is too large, 2mb is the maximum file size.'
        ]);

        if ($validator->fails()) {
            $data = [
                'status' => 'BAD',
                'message' => $validator->errors()->all(),
            ];

            return $data;
        }

        $data = [
            'status' => 'OK'
        ];

        Session::put('method', 'purchaser-update');
        return $data;
    }

    public function list_purchaser(Request $request)
    {
        $list_purchaser = $this->purchaser->list_purchaser($request);

        if (empty($list_purchaser)) {
            $data = [
                'status' => 'OK',
                'data' => []
            ];
        } else {
            $data = [
                'status' => 'OK',
                'data' => $list_purchaser
            ];
        }
        return  $data;
    }

    public function list_country()
    {
        $data = [
            'status' => 'OK',
            'data' => $this->country->list_country()
        ];

        return $data;
    }

    public function resend_wallet_code($id)
    {
        $purchaser = $this->purchaser->show($id);

        $wallet = $this->purchaserWallet->show($id);

        $request['wallet_code'] = $wallet[0]->wallet_code;
        $request['purchaser_name'] = $purchaser[0]->purchaser_name;

        Mail::to($purchaser[0]->purchaser_email)->send(new WalletCodeEmail($request));

        $data = [
            'status' => 'OK',
            'message' => 'Wallet Code Successfully Sent'
        ];

        return $data;

    }

    public function download_csv(){
        return Excel::download(new PurchaserReport(), 'purchaser-listing.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    /** PRIVATE FUNCTIONS USED FOR THIS CONTROLLER */

    private function insertFamilyTree($user_id, $id, Request $request)
    {
        $bonus_id = $request->input("purchaser_membership");
        $referral_id = ($request->input('referred') ? $request->input('referral') : 0);
        $level = $this->checkLevel($referral_id);
        $this->FamilyTreeModel->insertFamilyTree($user_id, $id, $referral_id, $bonus_id, $level);
    }

    private function checkLevel($referral_id)
    {
        $level = 1;
        do {
            $count_parent = $this->FamilyTreeModel->checkParentID($referral_id);
            if ($count_parent > 0) {
                $response = $this->FamilyTreeModel->getChildToParentRecord($referral_id);
                if (isset($response->purchaser_id_upline)) {
                    $referral_id = $response->purchaser_id_upline;
                    $boolean = true;
                    $level++;
                } else {
                    $boolean = false;
                }
            } else {
                $boolean = false;
            }

        } while ($boolean == true);

        return $level;
    }

}
