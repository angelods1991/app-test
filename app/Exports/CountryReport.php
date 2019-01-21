<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class CountryReport implements FromArray, WithTitle, ShouldAutoSize
{
    use Exportable;

    public function title(): string
    {
        return "Country Listing";
    }

    public function array(): array
    {
        $data[] = [
            "A" => "Country Listing",
            "C" => "DATE CREATED",
            "D" => now()->toDateTimeString()
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

        if (request('status') != "all") {
            $data[] = [
                "A" => "ADVANCE SEARCH:"
            ];

            $data[] = [
                "A" => null,
                "B" => "STATUS:",
                "C" => $this->get_advance_filter_status()
            ];
        }

        $data [] = [
            "A" => null
        ];


        $data[] = [
            "A" => "ID",
            "B" => "CODE",
            "C" => "NAME",
            "D" => "STATUS",
        ];

        for ($i = 0; $i < DB::table('countries')->count(); $i = $i + 10) {
            $db = DB::table('countries')
                ->select([
                    'id',
                    'country_code',
                    'country_name',
                    'country_status'
                ])
                ->offset($i)
                ->limit(10);

            if (request('status') != "all") {
                $db = $db->where('country_status', '=', request('status'));
            }

            if (!empty(request('search'))) {
                switch (request('col_filter')) {
                    case 1:
                        $db = $db->where('id', '=', request('search'));
                        break;
                    case 2:
                        $db = $db->where('country_code', 'like', '%' . request('search') . '%');
                        break;
                    case 3:
                        $db = $db->where('country_name', 'like', '%' . request('search') . '%');
                        break;
                }
            }

            $db = $db->get();

            foreach ($db as $datum) {
                $data[] = [
                    "A" => $datum->id,
                    "B" => $datum->country_code,
                    "C" => $datum->country_name,
                    "D" => ($datum->country_status == 1) ? "Active" : "Inactive",
                ];
            }
        }

        return $data;
    }

    private function get_column_filter(){
        switch (request('col_filter')) {
            case 1:
                return "ID No.";
                break;
            case 2:
                return "Name";
                break;
            case 3:
                return "Email Address";
                break;
            case 4:
                return "EDA";
                break;
            default:
                return "";
        }
    }

    private function get_advance_filter_status(){
        if(\request('status') != "all"){
            if(\request('status') == 1){
                return "Active";
            } else {
                return "Inactive";
            }
        }

        return "All";
    }
}
