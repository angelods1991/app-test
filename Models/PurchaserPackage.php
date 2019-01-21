<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PurchaserPackage
{
    protected $table = "purchaser_packages";

    public function store(Request $request)
    {
        DB::table($this->table)
            ->insert([
                'purchaser_id' => $request->input('purchaser_id'),
                'package_method' => $request->input('package_method'),
                'package_remarks' => $request->input('package_remarks'),
                'package_token_price' => $request->input('package_token_price'),
                'package_paid_amount' => $request->input('package_paid_amount'),
                'package_incentive_percentage' => $request->input('package_incentive_percentage'),

                'package_token_locked' => $request->input('package_token_locked'),
                'package_token_incentive' => $request->input('package_token_incentive'),
                'package_token_total' => $request->input('package_token_total'),

                'package_status' => '0',
                'created_by' => Auth::user()->id,
                'created_date' => DB::raw('CURRENT_TIMESTAMP')
            ]);
    }

    public function table()
    {
        $dt = DB::table($this->table . ' as pp')
            ->select([
                'pp.id',
                'pp.package_method',
                'pp.package_token_price',
                'pp.package_paid_amount',
                'pp.package_token_total',
                'pp.package_incentive_percentage',
                'pp.package_status',
                DB::raw('date(pp.modified_date) as modified_date'),
                DB::raw('date(pp.created_date) as created_date'),
                'uc.name',
                DB::raw('mu.name as modified_by')
            ])
            ->leftJoin('users as uc', 'pp.created_by', '=', 'uc.id')
            ->leftJoin('users as mu', 'pp.modified_by', '=', 'mu.id')
            ->where('pp.purchaser_id', '=', request('id'));

        return DataTables::of($dt)
            ->toJson();
    }

    public function show($id)
    {
        return DB::table($this->table)->where('id', '=', $id)->get();
    }

    public function package_transact($package_id, $status, $modified_by, $modified_date)
    {
        DB::table($this->table)
            ->where('id', '=', $package_id)
            ->update([
                'package_status' => $status,
                'modified_by' => $modified_by,
                'modified_date' => $modified_date
            ]);
    }
}
