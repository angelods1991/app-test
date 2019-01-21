<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FamilyTreeModel;
use App\Models\BonusModel;
use App\Models\PurchaserPackage;

class BonusActivityController extends Controller
{
    protected $parentID = '', $purchaserPackage;

    protected $_columns = array(
        1 => 'candidate_name',
        2 => 'purchaser_name'
    );

    public function __construct()
    {
        $this->FamilyModel = new FamilyTreeModel();
        $this->BonusModel = new BonusModel();
        $this->purchaserPackage = new PurchaserPackage();
    }

    public function distribute(Request $request)
    {
        $bonus_token = 0;
        if($request->isMethod('post'))
        {
            $purchaser_id = $request->input('purchaser_id');

            $package_info = $this->purchaserPackage->show($request->input('package_id'));

            $total_token = $package_info[0]->package_token_locked;

            $approved_by = $request->user()->id;
            $status = $request->input('status');

            $response_ids = $this->selectFamilyTier($purchaser_id);

            array_pop($response_ids);

            while(count($response_ids)!=0)
            {
                $membership = $this->getBonusID($response_ids[0]);

                $response_percentage = $this->collectMembershipPercentage($membership);

                $percent = $this->changePercentToDecimal($response_percentage[count($response_ids)-1]);

                $bonus_token = bcmul($total_token,$percent,18);

                $response = $this->BonusModel->insertBonusActivity($response_ids[0],$purchaser_id,$bonus_token,$status,$approved_by);

                if($response==1)
                {
                    if($status==1):
                      $this->BonusModel->updateBonusWallet($response_ids[0],$bonus_token);
                    endif;
                }
                else
                {
                    $bonus_token = 0;
                }

                array_shift($response_ids);
            }
        }
        else
        {
            return "Invalid Method!";
        }
        return $bonus_token;
    }

    public function showBonusDetails($purchaser_id,$package_total)
    {
        $container = array();

        $tier_percent = $this->getAllBonus();
        $response_ids = $this->selectFamilyTier($purchaser_id);

        $buyer_id = $response_ids[count($response_ids)-1];
        array_pop($response_ids);

        while(count($response_ids)!=0)
        {
            $membership = $this->getBonusID($response_ids[0]);

            $response_percentage = $this->collectMembershipPercentage($membership);

            $percent = $this->changePercentToDecimal($response_percentage[count($response_ids)-1]);

            $bonus_token = bcmul($package_total,$percent,18);

            $name = $this->getPurchaserName($response_ids[0]);

            $container[] = $name."|".$bonus_token;

            array_shift($response_ids);
        }

        krsort($container);

        return $container;
    }

    private function updateBonusStatusWallet($response,$response_ids,$candidate_id)
    {
        $flag = false;
        if($response==1)
        {
            $bonus = $this->BonusModel->getBonusActivity($response_ids[0],0);
            foreach ($bonus as $key => $value) {
                $response_wallet = $this->BonusModel->updateBonusWallet($response_ids[0],$value->amount);
                if($response_wallet==1){
                    $this->BonusModel->updateBonusActivityStatus($response_ids[0],$candidate_id,0);
                    $flag = true;
                }
            }
        }

        return $flag;
    }

    private function collectMembershipPercentage($membership)
    {
        $data = array();
        $response = $this->BonusModel->getPercentageByMembershipType($membership);

        foreach ($response as $key => $value) {
          $data[] = $value->bonus_name;
        }

        return $data;
    }

    private function selectFamilyTier($purchaser_id)
    {
        $a=1;
        $ctr=0;

        $purchaser_container = array();
        $bonus_count = $this->BonusModel->countUsersRecords();

        $bonuses = $this->getAllBonus();

        do
        {
          $response = $this->getFamilyTree($purchaser_id);

          if(!empty($response))
          {
              $purchaser_container[] = $response->purchaser_id;
              $purchaser_id = $response->purchaser_id_upline;
              $a++;
          }
          else
          {
              $a=0;
          }
        }
        while ($a <= ($bonus_count+1) && $a > 0 && $a<5);

        sort($purchaser_container);

        return $purchaser_container;
    }

    private function getBonusID($purchaser_id)
    {
        $response = $this->getFamilyTree($purchaser_id);
        if(!empty($response)){
            $bonus_id = $response->bonus_id;
            $response = $this->BonusModel->getMembershipType($bonus_id);

            return $response->membership;
        }

        return false;
    }

    private function changePercentToDecimal($percentage)
    {
        $decimal = str_replace("%","",$percentage);

        $percent = ($decimal / 100);

        return $percent;
    }

    private function getPackage($package_id)
    {
        $response = $this->BonusModel->getPackage($package_id);
        if(!empty($response)){
            $total_token = $response->package_token;
        }else{
            $total_token = 0;
        }

        return $total_token;
    }

    private function getFamilyTree($purchaser_id)
    {
        $response = $this->FamilyModel->getChildToParentRecord($purchaser_id);

        return $response;
    }

    private function getBonusData($tier)
    {
        $bonus_count = $this->BonusModel->countUsersRecords();
        if($bonus_count<$tier){
          $tier = $bonus_count;
        }

        $response = $this->BonusModel->getDataByID($tier);

        return $response->bonus_name;
    }

    public function getAllBonus()
    {
        $response = $this->BonusModel->getAllBonus();

        return $response;
    }

    public function recordList(Request $request)
    {
        if($request->isMethod('post'))
        {
            if(strlen($request->input('_token'))>0)
            {
                $columns = array(0=>'id',1=>'purchaser_name',2=>'candidate_name',6=>'created_at');

                $total_data = $this->BonusModel->countActivityRecords();

                $total_filtered_data = $total_data;

                $limit = $request->input('length');
                $start = $request->input('start');
                $order = $columns[$request->input('order.0.column')];
                $dir = $request->input('order.0.dir');

                $date = $this->setDateTime($request->input('_date_from'),$request->input('_date_to'));

                if(empty($request->input('_search')))
                {
                    $post = $this->BonusModel->showAllActivityRecords($start,$limit,$order,$dir,$date['from'],$date['to']);
                }
                else
                {
                    $search = $request->input('_search');
                    $column_search = $request->input('_radio_value');

                    $column_search = $this->selectColumnSearch($column_search);

                    $post = $this->BonusModel->fetchActivityList($search,$start,$limit,$order,$dir,
                                                                  $column_search,$date['from'],$date['to']);

                    $total_filtered_data = $this->BonusModel->_countActivity($search,$column_search,$date['from'],$date['to']);


                }

                $data = array();

                if(!empty($post)):
                    $data = $this->setNestedData($post,$start);
                endif;

                $json_data = $this->setJsonData($request,$total_data,$total_filtered_data,$data);

                $data = $json_data;
            }
            else
            {
                $data['result'] = 'fail';
                $data['message'] = 'Please login before using this api!';
            }
        }
        else
        {
            $data['result'] = 'fail';
            $data['message'] = 'Invalid Method!';
        }

        return json_encode($data);
    }

    private function setDateTime($date_from,$date_to)
    {
        if(empty($date_from))
        {
            $date_from = date('Y-m-d H:i:s', strtotime(date('Y-m-d').'-30 days'));
            $date_to = date('Y-m-d H:i:s',strtotime("23:59:59"));
        }
        else
        {
            $date_from = date('Y-m-d H:i:s',strtotime($date_from));
            $date_to = date('Y-m-d H:i:s',strtotime($date_to . " 23:59:59"));
        }

        return array('from'=>$date_from,'to'=>$date_to);
    }

    private function selectColumnSearch($column_search)
    {
        switch ($column_search) {
          case '1':
              $column_search = $this->_columns[$column_search];
            break;
          case '2':
              $column_search = $this->_columns[$column_search];
            break;
          case '3':
              $column_search = $this->_columns[$column_search];
            break;

          default:
            $column_search = 'candidate_name';
            break;
        }

      return $column_search;
    }

    private function setJsonData($request,$total_data,$total_filtered_data,$data)
    {
        $json_data = array(
                    "draw"            => intval($request->input('draw')),
                    "recordsTotal"    => intval($total_data),
                    "recordsFiltered" => intval($total_filtered_data),
                    "data"            => $data
                    );

        return $json_data;
    }

    private function setNestedData($post,$start)
    {
        $data = array();
        $iter=$start+1;

        foreach ($post as $post_data)
        {
            $nestedData['no'] = $post_data->id;
            $nestedData['purchaser_name'] = $post_data->purchaser_name;
            $nestedData['candidate_name'] = $post_data->candidate_name;
            $nestedData['amount'] = $post_data->amount;
            $nestedData['status'] = ($post_data->status==0?"Cancelled":"Posted");
            $nestedData['created_by'] = $post_data->name;
            $nestedData['date'] = $post_data->created_at;

            $iter++;

            $data[] = $nestedData;
        }

        return $data;
    }

    private function getPurchaserName($purchaser_id)
    {
        $response = $this->BonusModel->getPurchaserName($purchaser_id);

        return $response->purchaser_name;
    }
}
