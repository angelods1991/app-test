<?php

namespace App\Models;

use DataTables;
use DB;
use Illuminate\Http\Request;

class Country
{
    protected $table = 'countries';

    public function list_country()
    {
        return DB::table($this->table)->where('country_status', '=', '1')->orderBy('country_name', 'asc')->get();
    }

    public function table()
    {
        $dt = DB::table($this->table)
            ->select([
                'id',
                'country_code',
                'country_name',
                'currency_code',
                'country_status'
            ]);

        return DataTables::of($dt)
            ->filter(
                function ($query) {

                    if (request('status') != "all") {
                        $query->where('country_status', '=', request('status'));
                    }

                    if (!empty(request('search'))) {
                        switch (request('col_filter')) {
                            case 1:
                                $query->where('id', '=', request('search'));
                                break;
                            case 2:
                                $query->where('country_code', 'like', '%' . request('search') . '%');
                                break;
                            case 3:
                                $query->where('country_name', 'like', '%' . request('search') . '%');
                                break;
                        }
                    }

                })
            ->toJson();
    }

    public function show($id)
    {
        return DB::table($this->table .' as c')
            ->select([
                'c.*',
                'ub.name as created_name',
                'mb.name as modified_name'
            ])
            ->leftJoin('users as ub','c.created_by','=','ub.id')
            ->leftJoin('users as mb','c.modified_by','=','mb.id')
            ->where('c.id', '=', $id)->get();
    }

    public function store(Request $request)
    {
        DB::table($this->table)
            ->insert([
                'country_code' => $request->input('country_code'),
                'country_name' => $request->input('country_name'),
                'country_status' => $request->input('country_status'),
                'currency_code' => $request->input('currency_code'),
                'created_by' => \Auth::user()->id,
                'created_date' => DB::raw('CURRENT_TIMESTAMP')
            ]);
    }

    public function destroy($id)
    {
        DB::table($this->table)
            ->where('id', '=', $id)
            ->delete();
    }

    public function update(Request $request, $id)
    {
        DB::table($this->table)
            ->where('id', '=', $id)
            ->update([
                //'country_code' => $request->input('country_code'),
                'country_name' => $request->input('country_name'),
                'currency_code' => $request->input('currency_code'),
                'country_status' => $request->input('country_status'),
                'modified_by' => \Auth::user()->id,
                'modified_date' => DB::raw('CURRENT_TIMESTAMP')
            ]);
    }
}
