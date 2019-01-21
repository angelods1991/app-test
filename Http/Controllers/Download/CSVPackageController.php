<?php

namespace App\Http\Controllers\Download;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DownloadModel;
use App\Models\UsersModel;
use App\Models\BonusModel;
use Illuminate\Routing\UrlGenerator;

class CSVPackageController extends Controller
{
    protected $access_code = "`_`3Dm@rK`4dm1n`ConV3rs10n`_`";

    public function __construct()
    {
        $this->middleware(['auth','showMethods']);
        $this->DownloadModel = new DownloadModel();
        $this->UsersModel = new UsersModel();
        $this->BonusModel = new BonusModel();
        $this->encrypt = md5($this->access_code);
    }

    public function index()
    {
        $data['base_url'] = url()->current();
        $data['active_menu'] = 'downloadreports';
        $data['header_title'] = 'Download Reports';
        $data['active_module'] = str_replace(" ","",strtolower($data['header_title']));

        return view('download/csv')->with($data);
    }

    public function download(Request $request)
    {
        $data = "";
        if($request->isMethod('get'))
        {
            if($this->encrypt==$request->input('access_code'))
            {
              $response = $this->DownloadModel->getMemberMain();

              header('Content-Type: text/csv; charset=utf-8');
              header('Content-Disposition: attachment; filename=accumulative_contributed_report.csv');

              $output = fopen('php://output', 'w');
              fputcsv($output,array('CONTRIBUTOR NAME','1ST LEVEL TOTAL PAID AMOUNT WITH APPROVAL',
              '1ST LEVEL TOTAL PAID AMOUNT WITH PENDING','1ST LEVEL TOTAL PAID AMOUNT WITH REJECT',
              'TOTAL REFEREE'));

              foreach ($response as $key => $value) {
                  $total_approved_amount = 0;
                  $total_pending_amount = 0;
                  $total_reject_amount = 0;
                  $total_referee = 0;
                  $container = array();
                  $second_level = $this->DownloadModel->selectMemberFirstLevel($value->id);

                  foreach ($second_level as $info) {
                      switch ($info->package_status) {
                        case "1":
                          $total_approved_amount = $info->package_paid_amount + $total_approved_amount;
                          break;
                        case "0":
                          $total_pending_amount = $info->package_paid_amount + $total_pending_amount;
                          break;
                        case "2":
                          $total_reject_amount = $info->package_paid_amount + $total_reject_amount;
                          break;
                      }

                      if(!in_array($info->purchaser_id,$container)):
                          $total_referee++;
                          $container[] = $info->purchaser_id;
                      endif;
                  }

                  fputcsv($output, array($value->purchaser_name,$total_approved_amount,$total_pending_amount,$total_reject_amount,$total_referee));
              }
            }
            else
            {
              $data['result'] = 'fail';
              $data['message'] = 'Invalid Access Code!';
            }
        }
        else
        {
          $data['result'] = 'fail';
          $data['message'] = 'Invalid Method!';
        }

        return $data;
    }

    public function downloadContributorDetails(Request $request)
    {
        $data = '';
        if($request->isMethod('get'))
        {
            if($this->encrypt==$request->input('access_code'))
            {
                $response = $this->DownloadModel->getDistributorDetails();
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=contributor_contributed_and_edc_balance_report.csv');

                $title_head = array(
                  'COUNTRY',
                  'CONTRIBUTOR NAME',
                  'EMAIL',
                  'EDA NO.',
                  'TYPE',
                  'MEMBERSHIP',
                  'TOTAL PAID AMOUNT WITH APPROVAL',
                  'TOTAL PAID AMOUNT WITH PENDING',
                  'TOTAL PAID AMOUNT WITH REJECT',
                  'CURRENCT COIN BALANCE'
                );

                $output = fopen('php://output', 'w');
                fputcsv($output,$title_head);

                foreach ($response as $contributor) {
                    $response_data = $this->collectPackages($contributor->id);
                    $data_body = array(
                      $contributor->purchaser_country,
                      $contributor->purchaser_name,
                      $contributor->purchaser_email,
                      $contributor->purchaser_eda,
                      $this->purchaserType($contributor->purchaser_type),
                      $contributor->membership,
                      $response_data[0],
                      $response_data[1],
                      $response_data[2],
                      $response_data[3]
                    );

                    fputcsv($output,$data_body);
                }
            }
            else
            {
              $data['result'] = 'fail';
              $data['message'] = 'Invalid Access Code!';
            }
        }
        else
        {
          $data['result'] = 'fail';
          $data['message'] = 'Invalid Method!';
        }

        return $data;
    }

    private function collectPackages($purchaser_id)
    {
        $total_approved = 0;
        $total_pending = 0;
        $total_reject = 0;
        $total_coins = 0;

        $packages = $this->DownloadModel->getSelectedDistributorPackage($purchaser_id);

        foreach ($packages as $value) {
            switch ($value->package_status) {
              case "1":
                $total_approved = $value->package_paid_amount + $total_approved;
                break;
              case "0":
                $total_pending = $value->package_paid_amount + $total_pending;
                break;
              case "2":
                $total_reject = $value->package_paid_amount + $total_reject;
                break;
            }

            $total_coins = $value->package_token_total + $total_coins;
        }

        return [$total_approved,$total_pending,$total_reject,$total_coins];
    }

    private function packageStatus($status)
    {
        switch ($status) {
          case '0':
            $status = "Pending";
            break;
          case '1':
            $status = "Approved";
            break;
          case '2':
            $status = "Rejected";
            break;
        }

        return $status;
    }

    private function purchaserType($type)
    {
        switch ($type) {
          case '0':
            $type = "Public Member";
            break;
          case '1':
            $type = "Existing Member";
            break;
        }

        return $type;
    }

    private function getUserName($user_id)
    {
        $name = $this->DownloadModel->getUserName($user_id);

        return $name;
    }
}
