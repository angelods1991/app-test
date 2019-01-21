<?php

namespace App\Http\Controllers\Country;

use App\Exports\CountryReport;
use App\Exports\PurchaserReport;
use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class CountryController extends Controller
{
    protected $country;

    public function __construct()
    {
        $this->middleware(['auth','showMethods']);
        $this->country = new Country();
    }

    public function index()
    {
        $data = [
            'header_title' => 'Country Management',
            'active_module' => 'countrymanagement',
        ];

        return view('pages.country.country')->with($data);
    }

    public function store(Request $request)
    {
        if(Session::get('method') != 'country-store'){
            $data = [
                'status' => 'BAD',
                'message' => 'Invalid Transaction'
            ];

            return $data;
        }

        Session::remove('method');

        $this->country->store($request);

        $data = [
            'status' => 'OK',
            'message' => 'Country Created'
        ];

        return $data;
    }

    public function show($id)
    {
        $country = $this->country->show($id);

        if (empty($country[0])) {
            $data = [
                'status' => 'BAD',
                'message' => 'Country Not Found'
            ];
        } else {
            $data = [
                'status' => 'OK',
                'data' => $country[0]
            ];
        }

        return $data;
    }

    public function update(Request $request, $id)
    {
        if(Session::get('method') != 'country-update'){
            $data = [
                'status' => 'BAD',
                'message' => 'Invalid Transaction'
            ];

            return $data;
        }

        Session::remove('method');

        $this->country->update($request,$id);

        $data = [
            'status' => 'OK',
            'message' => 'Country Updated'
        ];

        return $data;
    }

    public function destroy($id)
    {
        $this->country->destroy($id);

        $data = [
            'status' => 'OK',
            'message' => 'Country Deleted'
        ];

        return $data;
    }

    public function table()
    {
        return $this->country->table();
    }

    public function validate_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_code' => 'required|size:2|unique:countries,country_code',
            'country_name' => 'required',
            'country_status' => 'required'
        ],[
            'country_code.required' => 'The code field is required.',
            'country_name.required' => 'The name field is required.',

            'country_code.size' => 'The code must be 2 characters.',
            'country_code.unique' => 'The code has already been taken.'
        ]);

        if($validator->fails()){
            $data = [
                'status' => 'BAD',
                'message' => $validator->errors()->all()
            ];

            return $data;
        }

        Session::put('method','country-store');

        $data = [
            'status' => 'OK'
        ];

        return $data;
    }

    public function validate_update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            //'country_code' => 'required|size:2|unique:countries,country_code,'.$id,
            'country_name' => 'required',
            'country_status' => 'required'
        ],[
            'country_name.required' => 'The name field is required.',
        ]);

        if($validator->fails()){
            $data = [
                'status' => 'BAD',
                'message' => $validator->errors()->all()
            ];

            return $data;
        }

        Session::put('method','country-update');

        $data = [
            'status' => 'OK'
        ];

        return $data;
    }

    public function download_csv(){
        return Excel::download(new CountryReport(), 'country-listing.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
