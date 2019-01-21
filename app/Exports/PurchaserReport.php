<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class PurchaserReport implements FromArray, WithTitle, ShouldAutoSize
{
    use Exportable;

    public function title(): string
    {
        return "Purchaser Report";
    }

    public function array(): array
    {
        $data[] = [
            "A" => "TITLE",
            "B" => "Purchaser Listing",
            "C" => "DATE CREATED",
            "D" => now()->toDateString()
        ];

        $data[] = [
            "A" => null
        ];

        $data[] = [
            "A" => "FILTERS:"
        ];

        if(!empty(\request('search'))){
            $data[] = [
                "A" => null,
                "B" => "COLUMN FILTER",
                "C" => $this->get_column_filter(),
                "D" => "SEARCH VALUE",
                "E" => \request('search')
            ];
        } else {
            $data[] = [
                "A" => null,
                "B" => "NONE"
            ];
        }

        if (request('status') != "all" || request('type') != "all" || request('country') != "all"|| request('package') != "all") {
            $data[] = [
                "A" => "ADVANCE SEARCH:"
            ];

            $data[] = [
                "A" => null,
                "B" => "STATUS:",
                "C" => $this->get_advance_filter_status()
            ];

            $data[] = [
                "A" => null,
                "B" => "TYPE:",
                "C" => $this->get_advance_filter_type()
            ];

            $data[] = [
                "A" => null,
                "B" => "COUNTRY:",
                "C" => $this->get_country(\request('country'))
            ];

            $data[] = [
                "A" => null,
                "B" => "PACKAGE STATUS:",
                "C" => $this->get_advance_filter_package()
            ];
        }

        $data [] = [
            "A" => null
        ];

        $data[] = [
            "A" => "ID NO.",
            "B" => "COUNTRY",
            "C" => "NAME",
            "D" => "EMAIL ADDRESS",
            "E" => "CONTACT NO.",
            "F" => "EDA NO.",
            "G" => "TYPE",
            "H" => "MEMBERSHIP",
            "I" => "REFERRAL NAME",
            "J" => "STATUS",
        ];

        for ($i = 0; $i < DB::table('purchaser')->count(); $i = $i + 10) {
            $db = DB::table('purchaser as p')
                ->select([
                    'p.id',
                    'p.purchaser_country',
                    'p.purchaser_name',
                    'p.purchaser_email',
                    'p.purchaser_contact',
                    'p.purchaser_eda',
                    'p.purchaser_type',
                    'p.purchaser_status',
                    $this->get_membership()
                ])
                ->offset($i)
                ->limit(10);

            if (request('status') != "all") {
                $db = $db->where('p.purchaser_status', '=', request('status'));
            }

            if (request('type') != "all") {
                $db = $db->where('p.purchaser_type', '=', request('type'));
            }

            if (request('country') != "all") {
                $db = $db->where('purchaser_country', '=', request('country'));
            }

            if (request('package') != "all") {
                switch (request('package')) {
                    case "NP": // No Packages and Rejected Only
                        $db = $db->where($this->get_pending(), '=', 0);
                        $db = $db->where(function ($db) {
                            $db->where($this->get_approved(), '=', 0);
                            $db->orWhere($this->get_rejected(), '!=', 0);
                        });
                        break;
                    case "P": // Pending only
                        $db = $db->where($this->get_pending(), '!=', 0);
                        $db = $db->where($this->get_approved(), '=', 0);
                        $db = $db->where($this->get_rejected(), '=', 0);
                        break;
                    case "PA": // Partial Approved
                        $db = $db->where(function ($db) {
                            $db->where($this->get_pending(), '!=', 0);
                            $db->where($this->get_approved(), '!=', 0);
                        });
                        break;
                    case "FP": // Fully Approved
                        $db = $db->where($this->get_pending(), '=', 0);
                        $db = $db->where($this->get_approved(), '!=', 0);
                        break;
                }
            }

            if (!empty(request('search'))) {
                switch (request('col_filter')) {
                    case 1:
                        $db = $db->where('id', '=', request('search'));
                        break;
                    case 2:
                        $db = $db->where('purchaser_name', 'like', '%' . request('search') . '%');
                        break;
                    case 3:
                        $db = $db->where('purchaser_email', 'like', '%' . request('search') . '%');
                        break;
                    case 4:
                        $db = $db->where('purchaser_eda', 'like', '%' . request('search') . '%');
                        break;
                }
            }

            $db = $db->get();

            foreach ($db as $datum) {
                $data[] = [
                    "A" => $datum->id,
                    "B" => $this->get_country($datum->purchaser_country),
                    "C" => $datum->purchaser_name,
                    "D" => $datum->purchaser_email,
                    "E" => $datum->purchaser_contact,
                    "F" => $datum->purchaser_eda,
                    "G" => ($datum->purchaser_type == 1) ? "EXISTING MEMBER" : "PUBLIC MEMBER",
                    "H" => $datum->membership,
                    "I" => $this->get_upline($datum->id),
                    "J" => ($datum->purchaser_status == 1) ? "ACTIVE" : "INACTIVE",
                ];
            }
        }

        return $data;
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

    private function get_column_filter(){
        switch (request('col_filter')) {
            case 1:
                return "ID NO.";
                break;
            case 2:
                return "NAME";
                break;
            case 3:
                return "EMAIL ADDRESS";
                break;
            case 4:
                return "EDA NO.";
                break;
            default:
                return "";
        }
    }

    private function get_advance_filter_status(){
        if(\request('status') != "all"){
            if(\request('status') == 1){
                return "ACTIVE";
            } else {
                return "INACTIVE";
            }
        }

        return "ALL STATUS";
    }

    private function get_advance_filter_type(){
        if(\request('type') != "all"){
            if(\request('type') == 1){
                return "EXISTING MEMBER";
            } else {
                return "PUBLIC MEMBER";
            }
        }

        return "ALL TYPES";
    }

    private function get_advance_filter_package(){
        if (request('package') != "all") {
            switch (request('package')) {
                case "NP": // No Packages and Rejected Only
                    return "NO PACKAGES AND REJECTED ONLY";
                    break;
                case "P": // Pending only
                    return "PENDING ONLY";
                    break;
                case "PA": // Partial Approved
                    return "PARTIAL APPROVED";
                    break;
                case "FP": // Fully Approved
                    return "FULLY APPROVED";
                    break;
            }
        }

        return "ALL PACKAGES";
    }

    private function get_membership(){
        return DB::raw('(select membership from bonus as b where b.id = (select bonus_id from family_tree as ft where ft.purchaser_id = p.id)) as membership');
    }

    private function get_country($country_code){
        if($country_code == "all"){
            return "ALL COUNTRIES";
        }

        $dt = DB::table('countries')
            ->select(['country_name'])
            ->where('country_code','=',$country_code)
            ->get()
            ->first();

        if(empty($dt)){
            return "COUNTRY NOT FOUND";
        }

        return strtoupper($dt->country_name);
    }

    private function get_upline($purchaser_id){
        $dt = DB::table('family_tree as ft')
            ->select([
                'ft.purchaser_id_upline',
                'p.purchaser_name'
            ])
            ->leftJoin('purchaser as p','ft.purchaser_id_upline','=','p.id')
            ->where('purchaser_id','=',$purchaser_id)
            ->get()
            ->first();

        if(empty($dt)){
            return "NO UPLINE";
        }

        return $dt->purchaser_name;
    }
}
