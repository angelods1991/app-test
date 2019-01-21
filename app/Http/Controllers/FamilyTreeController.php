<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FamilyTreeModel;
use App\Models\BonusModel;

class FamilyTreeController extends Controller
{
    protected $level_container = array();

    public function __construct()
    {
        $this->TreeModel = new FamilyTreeModel();
        $this->BonusModel = new BonusModel();
    }

    public function index()
    {
        $data['active_menu'] = 'family_tree';
        $data['header_title'] = 'Family Tree';

        $data['tree'] = $this->viewList();

        return view('pages.family_tree.index',$data);
    }

    public function viewList()
    {
        $html='';

        $category = $this->TreeModel->getCategory(0);

        if(count($category)>0)
        {
            $tree='<ul class="first">';
            foreach ($category as $category) {
                $membership = $this->showMembershipTitle($category->bonus_id);
                $count_child = $this->TreeModel->checkChildRecord($category->id);
                $tree .='<li>';
                $tree .='     <span>';
                $tree .='       <i class="fa '.($count_child>0?"fa-folder":"fa-eye").'"></i>';
                $tree .='     </span>';
                $tree .='     <a href="">'.$category->purchaser_name.' ('.$membership.')</a>';
                if($count_child>0):
                    $tree .= $this->childView($category->purchaser_id);
                endif;
                $tree .='</li>';
            }

            $tree .='</ul>';
        }
        else
        {
          $tree = "No Data Found!";
        }

        return $tree;
    }

    private function showMembershipTitle($purchaser_id)
    {
        $response = $this->BonusModel->getMembershipType($purchaser_id);

        return $response->membership;
    }

    public function purchaserFamilyList(Request $request)
    {
        $id = $request->input('_fid');

        $check_count = $this->TreeModel->checkParentID($id);

        if($check_count>0){

            $purchaser = $this->TreeModel->getpurchaserInformationByID($id);
            $count_child = $this->TreeModel->checkChildRecord($purchaser->id);

            $bonus_id = $this->getFamilyTreeBonusID($purchaser->id);
            $membership = $this->showMembershipTitle($bonus_id);

            $iter = 1;
            $tree ='<ul>';

                $tree .='<li>';
                $tree .='     <span>';
                $tree .='       <i class="fa '.($count_child>0?"fa-minus":"fa-user").'"></i>';
                $tree .='     </span>';
                $tree .='     <a>'.$purchaser->purchaser_name.' ('.$membership.')</a>';
                if($count_child>0){
                  $tree .= $this->getChild($purchaser->id);
                }
                $tree .='</li>';

            $tree .='</ul>';

        }else{
          $tree = "No Data Found!";
        }

        echo $tree;
    }

    private function getFamilyTreeBonusID($id)
    {
        $response = $this->TreeModel->getParentID($id);

        return $response->bonus_id;
    }

    private function parentView()
    {
        $html ='<ul class="child">';

        $child_response = $this->TreeModel->getCategory($category_id);

        foreach ($child_response as $record) {

            $count_child = $this->TreeModel->checkChildRecord($record->purchaser_id);
            if($count_child>0){
                $html .='<li>';
                $html .='   <span>';
                $html .='     <i class="fa fa-minus"></i>';
                $html .='   </span>';
                if($id==$record->purchaser_id):
                  $html .='   <span>';
                  $html .='     <i class="fa fa-star"></i>';
                  $html .='   </span>';
                endif;
                $html .='   <a>'.$record->purchaser_name.'</a>';
                                $html.= $this->childView($record->purchaser_id,$id);
                $html .='</li>';
            }else{
                $html .='<li>';
                if($id==$record->purchaser_id):
                  $html .='   <span>';
                  $html .='     <i class="fa fa-star"></i>';
                  $html .='   </span>';
                endif;
                $html .=' <a>'.$record->purchaser_name.'</a>';
                $html .="</li>";
            }
        }

        $html .="</ul>";
    }

    private function getParent($parent_id)
    {
        $flag = 0;
        $data_container = array();

        do{
          $response = $this->TreeModel->getParentID($parent_id);

          if(!empty($response)){
            $data_container[] = $response;
            if($response->purchaser_id_upline==0):
                $parent_id = $response->purchaser_id;
                $flag = 0;
            else:
                $parent_id = $response->purchaser_id_upline;
                $flag = 1;
            endif;

          }else{
            $flag = 0;
          }

        }while($flag!=0);

        return $data_container;
    }

    public function getChild($parent_id)
    {
        $html ='<ul class="child">';

        $response = $this->TreeModel->getChildRecord($parent_id);
        $count_child = $this->TreeModel->checkChildRecord($parent_id);
        foreach ($response as $info) {
            $purchaser = $this->TreeModel->getpurchaserInformationByID($info->purchaser_id);
            $count_child = $this->TreeModel->checkChildRecord($info->purchaser_id);

            $bonus_id = $this->getFamilyTreeBonusID($purchaser->id);
            $membership = $this->showMembershipTitle($bonus_id);

            if($count_child>0){
                $html .='<li>';
                $html .='   <span>';
                $html .='     <i class="fa fa-minus"></i>';
                $html .='   </span>';
                $html .='   <a>'.$purchaser->purchaser_name.' ('.$membership.')</a>';
                                $html.= $this->getChild($info->purchaser_id);
                $html .='</li>';
            }else{
                $html .='<li>';
                $html .=' <a>'.$purchaser->purchaser_name.' ('.$membership.')</a>';
                $html .="</li>";
            }
        }

        $html .= '</ul>';

        return $html;
    }

    public function updateFamilyTreeLevel(Request $request)
    {
        if($request->isMethod('get'))
        {
            $referral_id = $request->input('referral_id');
            $purchaser_id = $request->input('purchaser_id');

            $referral_info = $this->TreeModel->getParentID($referral_id);
            $purchaser_info = $this->TreeModel->getParentID($purchaser_id);
            $referral_level = $referral_info->purchaser_level;
            $purchaser_level = $purchaser_info->purchaser_level;
            $purchaser_level = ($referral_level+1);
            echo "Referral Level: " . $referral_level."<hr>";
            echo "Purchaser Level: " . $purchaser_level ."<hr>";
            $this->updateLevelSequence($purchaser_id,$purchaser_level);



            echo json_encode($this->level_container)."<hr>";
        }
        else
        {
            echo "Invalid Method!";
        }
    }

    private function updateLevelSequence($purchaser_id,$purchaser_level)
    {
          $iter=1;
          $child_records = $this->TreeModel->getChildRecord($purchaser_id);

          if(count($child_records)>0)
          {
            foreach ($child_records as $key => $value)
            {
              $purchaser_level++;
              if(!array_key_exists($value->purchaser_id_upline,$this->level_container)){
                  $this->level_container[$value->purchaser_id_upline] = $purchaser_level;
              }
              $purchaser_id = $value->purchaser_id;
              echo "Upline ID: ".$this->level_container[$value->purchaser_id_upline]." | JSON Encode: " . $value->purchaser_id ." | ". $value->purchaser_id_upline."<hr>";
              $this->updateLevelSequence($purchaser_id,$purchaser_level);
              $iter++;
            }
          }
          else
          {
              $purchaser_level = $purchaser_level;
          }
    }

    private function getReferralLevel($referral_id)
    {
        $response = $this->TreeModel->getParentID($referral_id);

        if(!empty($response)){
          $level = $response->purchaser_level;
        }else{
          $level = "No Data Found!";
        }

        return $level;
    }
}
