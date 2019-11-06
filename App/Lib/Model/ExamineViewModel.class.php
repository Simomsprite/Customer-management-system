<?php 
	class ExamineViewModel extends ViewModel{
		public $viewFields;
		public function _initialize(){
			$main_must_field = array('examine_id','creator_role_id','create_time','update_time','examine_role_id','type','content','cate','start_time','end_time','duration','money','budget','advance','start_address','vehicle','end_address','examine_status','order_id','examine_role_id','owner_role_id','description');
            $field_main_list = M('Fields')->where(array('model'=>'examine','is_main'=>1))->getField('field', true);
            if ($field_main_list) {
				$main_list = array_unique(array_merge($field_main_list,$main_must_field));
            } else {
            	$main_list = $main_must_field;
            }
			
			$main_list['_type'] = 'LEFT';
			$data_list = M('Fields')->where(array('model'=>'examine','is_main'=>0))->getField('field', true);
			$data_list['_on'] = 'examine.examine_id = examine_data.examine_id';
            $data_list['_type'] = 'LEFT';
			
			$this->viewFields = array(
				'examine'=>$main_list,
				'examine_data'=>$data_list, 
				'examine_status'=>array('name'=>'status_name', '_on'=>'examine.type=examine_status.status', '_type'=>'LEFT'),
			);
		}
	}