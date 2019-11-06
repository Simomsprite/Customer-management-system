<?php

class MarketViewModel extends ViewModel
{
    protected $viewFields;

    public function _initialize(){
        $main_must_field = array('market_id', 'executor_role_ids', 'create_time', 'update_time', 'creator_role_id', 'owner_role_id', 'is_lock', 'is_deleted');
        $data_must_field = array('plan', 'execution_description', 'summary', 'effect', 'description');
        $main_list = array_unique(array_merge(M('Fields')->where(array('model' => 'market', 'is_main' => 1))->getField('field', true), $main_must_field));
        $data_list = array_unique(array_merge(M('Fields')->where(array('model' => 'market', 'is_main' => 0))->getField('field', true), $data_must_field));
        $data_list['_type'] = "LEFT";
        $data_list['_on'] = 'market.market_id = market_data.market_id';

        $this->viewFields = array('market' => $main_list, 'market_data' => $data_list);
    }

    
}