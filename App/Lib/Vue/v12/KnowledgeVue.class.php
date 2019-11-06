<?php
/**
 *知识相关
 **/
class KnowledgeVue extends Action{
	/**
	 *用于判断权限
	 *@permission 无限制
	 *@allow 登录用户可访问
	 *@other 其他根据系统设置
	 **/
	public function _initialize(){
		$action = array(
			'permission'=>array(),
			'allow'=>array()
		);
		B('VueAuthenticate', $action);
		$this->_permissionRes = getPerByAction(MODULE_NAME,ACTION_NAME);
		
		Global $role;
		$this->role = $role;
		Global $roles;
		$this->roles = $roles;

		if($roles == 2){
			$this->ajaxReturn('','您没有此权限！',-2);
		}

		if($roles == 3){
			$this->ajaxReturn('','请先登录！',-1);
		}
	}

	/**
	 * 知识列表
	 * @param 
	 * @author 
	 * @return 
	 */
	public function index() {
		if ($this->isPost()) {
			$m_knowledge = M('Knowledge');
			$category = M('knowledgeCategory');
			$where = array();
			$where['role_id'] = array('in', $this->_permissionRes);
			if (isset($_POST['search'])) {
				$where['title'] = array('like','%'.trim($_POST['search']).'%');
			}
			if ($_GET['category_id']) {
				$idArray = Array();
				$categoryList = getSubCategory($_GET['category_id'],$category->select(),'');
				foreach ($categoryList as $value) {
					$idArray[] = $value['category_id'];
				}
				$idList  =empty($idArray) ? $_GET['category_id'] : $_GET['category_id'] . ',' . implode(',', $idArray);
				$where['category_id'] = array('in',$idList);
			}
			$p = isset($_POST['p'])?$_POST['p']:1;
			$list = $m_knowledge->where($where)->order('create_time desc')->field('title,knowledge_id,update_time,category_id,role_id,hits')->Page($p.',10')->select();
			$count = $m_knowledge->where($where)->count();
			foreach($list as $k=>$v){
				$list[$k]['role_name'] = M('User')->where(array('role_id'=>$v['role_id'],'status'=>1))->getField('name');
				$owner_role_id = $v['role_id'];
				//获取操作权限
				$list[$k]['permission'] = permissionlist('knowledge',$owner_role_id);
			}
			$list = empty($list) ? array() : $list;
			$page = ceil($count/10);
			if($p == 1 && $_POST['search'] == ''){
				$category_list = $category->field('category_id,parent_id,name')->select();
				foreach ($category_list as $k=>$v) {
					$category_list[$k]['id'] = $v['category_id'];
					unset($category_list[$k]['category_id']);
				}
				//二维数组递归遍历多维数组实现无限分类
				$category_list = build_tree($category_list,0);
				$category_list = empty($category_list) ? array() : $category_list ;
				$data['category_list'] = $category_list ? $category_list : array();

				// $fields_list = array(
				// 	0 => array('field'=>'title','form_type'=>'text','input_tips'=>'','name'=>'知识标题','setting'=>''),
				// 	array('field'=>'role_id','form_type'=>'user','input_tips'=>'','name'=>'知识负责人','setting'=>''),
				// 	array('field'=>'create_date','form_type'=>'datetime','input_tips'=>'','name'=>'创建时间','setting'=>''),
				// 	array('field'=>'update_date','form_type'=>'datetime','input_tips'=>'','name'=>'修改时间','setting'=>'')
				// );
			}
			$data['list'] = $list;
			$data['page'] = $page;
			$data['info'] = 'success';
			$data['status'] = 1;
			$this->ajaxReturn($data,'JSON');
		}
	}

	/**
	 * 知识详情
	 * @param 
	 * @author 
	 * @return 
	 */
	public function view() {
		if ($this->isPost()) {
			if ($_POST['id']) {
				$m_knowledge = M('Knowledge');
				$knowledge_id = intval($_POST['id']);
				$knowledge_info = $m_knowledge->where('knowledge_id = %d',$knowledge_id)->find();
				//判断权限
				if ($this->_permissionRes && !in_array($knowledge_info['role_id'], $this->_permissionRes)) {
					$this->ajaxReturn('','您没有此权利!',-2);
				}
				if ($knowledge_info) {
					//点击数增加
					$m_knowledge->where('knowledge_id = %d',$knowledge_id)->setInc('hits');
					$knowledge_info['name'] = M('User')->where('role_id = %d',$knowledge_info['role_id'])->getField('name');

					$key_file = file_get_contents(CONF_PATH.'license.dat');	
					//详情图片处理
						$knowledge_info['content'] = str_replace('src=".', 'width = "100%;" src="'.'http://'.$_SERVER['SERVER_NAME'].substr($_SERVER["SCRIPT_NAME"],0,-8), $knowledge_info['content']);
					if ($key_file) {
						//本地部署
						//详情附件处理
						$knowledge_info['content'] = str_replace('href="./Uploads', 'href="'.'http://'.$_SERVER['SERVER_NAME'].substr($_SERVER["SCRIPT_NAME"],0,-8).'/Uploads', $knowledge_info['content']);
					} else {
						//云平台
						//详情附件处理
						$knowledge_info['content'] = str_replace('href="./online', 'href="'.'http://'.$_SERVER['SERVER_NAME'].'/online/', $knowledge_info['content']);
					}
					$this->ajaxReturn($knowledge_info,'success',1);
				} else {
					$this->ajaxReturn('数据不存在或已删除！','数据不存在或已删除！',2);
				}
			} else {
				$this->ajaxReturn('','参数错误！',0);
			}
		}
	}
}