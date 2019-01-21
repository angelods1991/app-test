<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class Purchaser
{
    protected $table = 'purchaser';
    protected $family_table = 'family_tree';
    protected $bonus_table = 'bonus';

    public function destroy($id)
    {
        DB::table($this->table)
            ->where('id', '=', $id)
            ->delete();

        DB::table($this->family_table)
            ->where('purchaser_id', '=', $id)
            ->delete();
    }

    public function store(Request $request)
    {
        return DB::table($this->table)
            ->insertGetId([
                'purchaser_country' => $request->input('purchaser_country'),
                'purchaser_name' => $request->input('purchaser_name'),
                'purchaser_email' => $request->input('purchaser_email'),
                'purchaser_eda' => $request->input('purchaser_eda'),
                'purchaser_type' => $request->input('purchaser_type'),
                'purchaser_status' => $request->input('purchaser_status'),
                'purchaser_contact' => $request->input('purchaser_contact'),
                'purchaser_image' => $request->input('image_name'),
                'created_by' => Auth::user()->id,
                'created_date' => DB::raw('CURRENT_TIMESTAMP')
            ]);
    }

    public function table()
    {
        $dt = DB::table($this->table . ' as p')
            ->select([
                'p.id',
                'p.purchaser_country',
                'p.purchaser_name',
                'p.purchaser_email',
                //'p.purchaser_contact',
                'p.purchaser_eda',
                'p.purchaser_type',
                'p.purchaser_status',
                DB::raw('(select c.country_name from countries as c where p.purchaser_country = c.country_code) as purchaser_country_name'),
                DB::raw('(select count(pp.id) from purchaser_packages as pp where pp.purchaser_id = p.id and pp.package_status = 0) as pending_packages')
            ]);

        return DataTables::of($dt)
            ->filter(
                function ($query) {

                    if (request('status') != "all") {
                        $query->where('purchaser_status', '=', request('status'));
                    }

                    if (request('type') != "all") {
                        $query->where('purchaser_type', '=', request('type'));
                    }

                    if (request('country') != "all") {
                        $query->where('purchaser_country', '=', request('country'));
                    }

                    if (request('package') != "all") {
                        switch (request('package')) {
                            case "NP": // No Packages and Rejected Only
                                $query->where($this->get_pending(), '=', 0);
                                $query->where(function ($query) {
                                    $query->where($this->get_approved(), '=', 0);
                                    $query->orWhere($this->get_rejected(), '!=', 0);
                                });
                                break;
                            case "P": // Pending only
                                $query->where($this->get_pending(), '!=', 0);
                                $query->where($this->get_approved(), '=', 0);
                                $query->where($this->get_rejected(), '=', 0);
                                break;
                            case "PA": // Partial Approved
                                $query->where(function ($query) {
                                    $query->where($this->get_pending(), '!=', 0);
                                    $query->where($this->get_approved(), '!=', 0);
                                });
                                break;
                            case "FP": // Fully Approved
                                $query->where($this->get_pending(), '=', 0);
                                $query->where($this->get_approved(), '!=', 0);
                                break;
                        }
                    }

                    if (!empty(request('search'))) {
                        switch (request('col_filter')) {
                            case 1:
                                $query->where('id', '=', request('search'));
                                break;
                            case 2:
                                $query->where('purchaser_name', 'like', '%' . request('search') . '%');
                                break;
                            case 3:
                                $query->where('purchaser_email', 'like', '%' . request('search') . '%');
                                break;
                            case 4:
                                $query->where('purchaser_eda', 'like', '%' . request('search') . '%');
                                break;
                        }
                    }

                })
            ->toJson();
    }

    public function show($id)
    {
        $purchaser = DB::table($this->table)
            ->select([
                $this->table . '.*',
                $this->bonus_table . '.bonus_level',
                $this->bonus_table . '.bonus_name',
                $this->bonus_table . '.bonus_desc',
                $this->bonus_table . '.id as bonus_id',
                'referral_table.purchaser_name as referral_name',
                $this->family_table . '.purchaser_id_upline',
                $this->bonus_table . '.id as purchaser_membership',
                $this->bonus_table . '.membership',
                'c.country_name',
                'uc.name as created_name',
                'mc.name as modified_name'
            ])
            ->leftJoin($this->family_table, $this->family_table . '.purchaser_id', '=', $this->table . '.id')
            ->leftJoin($this->bonus_table, $this->bonus_table . '.id', '=', $this->family_table . '.bonus_id')
            ->leftJoin($this->table . " AS referral_table", 'referral_table.id', '=', $this->family_table . '.purchaser_id_upline')
            ->leftJoin('users as uc', $this->table . '.created_by', '=', 'uc.id')
            ->leftJoin('users as mc', $this->table . '.modified_by', '=', 'mc.id')
            ->leftJoin('countries as c', $this->table . '.purchaser_country', '=', 'c.country_code')
            ->where($this->table . '.id', '=', $id)
            ->get();

        return $purchaser;
    }

    public function update(Request $request, $id)
    {
        DB::table($this->table)
            ->where('id', '=', $id)
            ->update([
                'purchaser_name' => $request->input('purchaser_name'),
                'purchaser_email' => $request->input('purchaser_email'),
                'purchaser_contact' => $request->input('purchaser_contact'),
                'purchaser_eda' => $request->input('purchaser_eda'),
                'purchaser_type' => $request->input('purchaser_type'),
                'purchaser_status' => $request->input('purchaser_status'),
                //'purchaser_country' => $request->input('purchaser_country'),
                'purchaser_image' => $request->input('image_name'),
                'modified_by' => Auth::user()->id,
                'modified_date' => DB::raw('CURRENT_TIMESTAMP')
            ]);
    }

    public function showPurchaser()
    {
        $list = DB::table($this->table)->get();

        return $list;
    }

    public function list_purchaser(Request $request)
    {
        return DB::table($this->table)
            ->select(['id', 'purchaser_name', 'purchaser_eda'])
            ->orWhere('purchaser_name', 'LIKE', '%' . $request->input('search_value') . '%')
            ->orWhere('purchaser_eda', 'LIKE', '%' . $request->input('search_value') . '%')
            ->get();
    }

    public function check_downline($purchaser_id)
    {
        return DB::table($this->family_table)->where('purchaser_id_upline', '=', $purchaser_id)->count();
    }

    private function get_pending()
    {
        return DB::raw('(select count(pp.id) from purchaser_packages as pp where pp.purchaser_id = p.id and pp.package_status = 0)');
    }

    private function get_approved()
    {
        return DB::raw('(select count(pp.id) from purchaser_packages as pp where pp.purchaser_id = p.id and pp.package_status = 1)');
    }

    private function get_rejected()
    {
        return DB::raw('(select count(pp.id) from purchaser_packages as pp where pp.purchaser_id = p.id and pp.package_status = 3)');
    }
}
