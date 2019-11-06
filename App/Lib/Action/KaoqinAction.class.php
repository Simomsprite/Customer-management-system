<?php
/**
*日志模块
*
**/
class KaoqinAction extends Action{
	/**
	*用于判断权限
	*@permission 无限制
	*@allow 登录用户可访问
	*@other 其他根据系统设置
	**/
	public function _initialize(){
		$action = array(
			'permission'=>array('remind', 'remind2'),
			'allow'=>array('record','set_map','index','indexdata','export')
		);
		B('Authenticate', $action);
	}

	private $jia_list = array('事假' => 'shijia', '病假' => 'bingjia', '调休' => 'tiaoxiu', '年休假' => 'nianxiu', '婚假' => 'hunjia', '丧假' => 'sangjia', '生育假' => 'shengyu', '外勤' => 'waiqin', '其他' => 'qita');

	/**
	 * 考勤统计
	 **/
	public function analytics(){
		$m_kaoqin = M('Kaoqin');
		$m_examine = M('Examine');
		$m_user = M('User');
		$m_kaoqin_config = M('KaoqinConfig');
		$config_info = $m_kaoqin_config->find();
		//权限范围
		$below_ids = getPerByAction(MODULE_NAME,ACTION_NAME);
		$role_id_array = array();
		if(intval($_GET['role'])){
			$role_id_array = array(intval($_GET['role']));
		}else{
			if(intval($_GET['department'])){
				$department_id = intval($_GET['department']);
				foreach(getRoleByDepartmentId($department_id, true) as $k=>$v){
					$role_id_array[] = $v['role_id'];
				}
			}
		}
		//过滤权限范围内的role_id
		if($role_id_array){
			//数组交集
			$idArray = array_intersect($role_id_array,$below_ids);
		}else{
			$idArray = $below_ids;
		}
		//分页功能
		if ($_GET['act'] === 'export') {
			$role_list = $m_user->where(array('role_id'=> array("IN", $idArray), 'kaoqin' => 1, 'status' => 1))->field('role_id,full_name,number,thumb_path')->select();
			foreach ($role_list as $key => $val) {
				$role_list[$key]['thumb_path'] = headPathHandle($val['thumb_path']);
				$position_id = M('role')->where(array('role_id' => $val['role_id']))->getField('position_id');
				$position = M('position')->where(array('position_id' => $position_id))->field('name,department_id')->find();
				$role_list[$key]['position'] = $position['name'];
				$role_list[$key]['department'] = M('role_department')->where(array('department_id' => $position['department_id']))->getField('name');
			}
		} else {
			$p = $_GET['p'] ? intval($_GET['p']) : 1;
			import("@.ORG.Page");
			$role_list = $m_user->where(array('role_id'=>array('IN', $idArray), 'kaoqin' => 1, 'status'=>1))->page($p.',15')->field('role_id,full_name,thumb_path')->order('user_id')->select();
			foreach ($role_list as $key => $val) {
				$role_list[$key]['thumb_path'] = headPathHandle($val['thumb_path']);
			}
			$count = $m_user->where(array('role_id'=>array('in', $idArray), 'status'=>1))->count();
			$Page = new Page($count,15);
			$this->count = $count;
			$this->assign('count',$count);
			$Page->parameter = implode('&', $params);
			$this->assign('page', $Page->show());
		}
		

		//时间段搜索
		$search_time_year = $_GET['search_year'] ? intval($_GET['search_year']) : date('Y',time());
		$search_time_month = $_GET['search_month'] ? intval($_GET['search_month']) : date('m',time());
		$search_time = $search_time_year.'-'.$search_time_month;
		//查询使用年份、月份数组
		$min_time = $m_kaoqin->min('daka_time');
		$min_year = $min_time ? date('Y',$min_time) : date('Y');
		$max_year = date('Y');
		$year_array = array();
		for ($i=$min_year; $i <= $max_year; $i++) { 
			$year_array[] = $i;
		}
		$month_array = array('1','2','3','4','5','6','7','8','9','10','11','12');
		$this->year_array = $year_array;
		$this->month_array = $month_array;
		$this->search_time_year = $search_time_year;
		$this->search_time_month = $search_time_month;

		//当前时间
		$date = $search_time;
		
		$this->date_now = $date;
		//根据月份计算天数
		$days = getmonthdays(strtotime($date));
		$this->days = $days;
		
		$m_workrule = M('Workrule');
		//获取时间范围内的每日时间戳数组(当月)
		$start = strtotime($date.'-'.'01');
		$end = strtotime($date.'-'.$days);
		$day_list = dateList($start,$end);

		//月时间戳范围
		$month_time = array('between',array($start,$end+86400));

		//计算月休息天数
		$month_no_array = $m_workrule->where(array('sdate'=>$month_time,'type'=>1))->getField('sdate',true);

		//开始到当前月时间戳范围
		$end = (strtotime($date.'-'.$days) > time()) ? time() : strtotime($date.'-'.$days);
		$month_time = array('between',array($start,$end+86400));
		$month_no = $m_workrule->where(array('sdate'=>$month_time,'type'=>1))->count();
		//月总天数
		$month_count_total = $days;
		$week_array = array(); //星期六、星期日的日期数组

		foreach ($day_list as $k=>$v) {
			$no_work = 1;
			$week = '';
			$week = getTimeWeek($v['sdate']);
			if (!in_array($v['sdate'],$month_no_array)) {
				$no_work = 0;
			}
			$day_list[$k]['no_work'] = $no_work;

			//判断星期六、日
			if ($week == '星期六' || $week == '星期日') {
				$week_array[] = $k+1;
			}
		}
		$this->week_array = $week_array;
		$now = time();

		// 应出勤、正常出勤、休息天数、迟到、早退、缺卡、旷工
		//考勤
		$jia_list = $this->jia_list;
		foreach($role_list as $k=>$v){
			$chuqin = 0; //出勤
			$queka = 0; //缺卡
			$kuanggong = 0; //旷工
			$chidao_fenzhong = 0;
			$zaotui_fenzhong = 0;
			$shijia = $bingjia = $tiaoxiu = $nianxiu = $hunjia = $sangjia = $shengyu = $waiqin = $qita = $chuchai = array(0, 0); // 事假、病假、调休、年休、婚假、丧假、生育、外勤、其他 、出差
			//本月休息天数
			$role_list[$k]['xiuxi'] = $month_no;
			// 本月工作天数
			$role_list[$k]['work'] = $days - $month_no;
			//本月迟到数
			$month_chidao_count = 0;
			$month_chidao_count = $m_kaoqin->where(array('role_id'=>$v['role_id'],'status'=>2,'daka_time'=>$month_time))->count();
			$role_list[$k]['chidao'] = $month_chidao_count;
			//本月早退数
			$month_zaotui_count = 0;
			$month_zaotui_count = $m_kaoqin->where(array('role_id'=>$v['role_id'],'status'=>3,'daka_time'=>$month_time))->count();
			$role_list[$k]['zaotui'] = $month_zaotui_count;
			//判断是否请假、出差
			$examine_list = array();
			$examine_list = $m_examine->where(array('owner_role_id'=>$v['role_id'],'create_time'=>$month_time,'type'=>array('in',array('2','5')),'examine_status'=>2))->select();
			foreach ($examine_list as $kk => $vv) {
				if ($vv['type'] == 2) {
					foreach ($jia_list as $j_key => $j_val) {
						if ($j_key == $vv['content']) {
							$tmp_arr = $$j_val;
							$tmp_arr[0] += 1;
							$tmp_arr[1] += $vv['duration'];
							$$j_val = $tmp_arr;
							break;
						}
					}
				} else {
					$chuchai[0] += 1;
					$chuchai[1] += $vv['duration'];
				}
			}
			//每日数据
			foreach($day_list as $key=>$val){
				if (time() > $val['sdate']) {
					$kaoqin_count = 0;
					$is_comment = 0;
					$title = '';
					$search_daka_time = date('Y-m-d',$val['sdate']).'+-+'.date('Y-m-d',$val['sdate']);
					$kaoqin_list = $m_kaoqin->where(array('role_id'=>$v['role_id'],'daka_time'=>array('between',array($val['sdate'],$val['edate']))))->select();
					$kaoqin_count = count($kaoqin_list);
					if($val['no_work'] == 1 && !$kaoqin_count){
						$kaoqin_type = 3; //休
					} else {
						if ($kaoqin_count == 1) {
							$kaoqin_type = 4; //缺卡
						} elseif (empty($kaoqin_count)) {
							if (strtotime(date('Y-m-d')) == $val['sdate']) {
								if ($now > strtotime(date('Y-m-d').$config_info['xiaban_time'])) {
									$kaoqin_type = 9; //旷工
								} else {
									$kaoqin_type = 4; //缺卡
								}
							} else {
								$kaoqin_type = 9; //旷工
							}
						}
					}
					if ($kaoqin_count == 1) $queka += 1;
					if ($kaoqin_list) {
						$chuqin += 1;
						$status_arr = array();
						$title = '';
						$a_arr = array('1','4'); //正常打卡
						$b_arr = array('2','3'); //迟到加早退
						foreach ($kaoqin_list as $key2=>$val2) {
							$status_arr[] = $val2['status'];
							switch ($val2['status']) {
								case 1 :
									$status_name = '签到';
									$role_list[$k]['kaoqin_type'][$key+1]['qiandao'] = date('H:i:s', $val2['daka_time']);
									break;
								case 2 : 
									$chidao_fenzhong += $val2['daka_time'] - strtotime(date('Y-m-d ', $val2['daka_time']) . $val2['shangban_time']);
									$status_name = '迟到';
									$role_list[$k]['kaoqin_type'][$key+1]['qiandao'] = date('H:i:s', $val2['daka_time']);
									$role_list[$k]['kaoqin_type'][$key+1]['remark']['chidao'] = $val2['remark'];
									break;
								case 3 :
									$zaotui_fenzhong += strtotime(date('Y-m-d ', $val2['daka_time']) . $val2['xiaban_time']) - $val2['daka_time'];
									$status_name = '早退';
									$role_list[$k]['kaoqin_type'][$key+1]['qiantui'] = date('H:i:s', $val2['daka_time']);
									$role_list[$k]['kaoqin_type'][$key+1]['remark']['zaotui'] = $val2['remark'];
									break;
								case 4 : 
									$status_name = '签退';
									$qiantui = $val['daka_time'];
									$role_list[$k]['kaoqin_type'][$key+1]['qiantui'] = date('H:i:s', $val2['daka_time']);
									break;
								default : $status_name = '缺卡';break;
							}
							if ($key2) {
								$title .= '&#10;'.date('Y-m-d H:i:s',$val2['daka_time']).'&nbsp;&nbsp;'.$status_name;
							} else {
								$title = date('Y-m-d H:i:s',$val2['daka_time']).'&nbsp;&nbsp;'.$status_name;
							}
						}
						sort($status_arr);
						if ($status_arr == $a_arr) {
							$kaoqin_type = 1; //正常
						} elseif ($status_arr == $b_arr) {
							$kaoqin_type = 2; //非正常
						} elseif (in_array('2',$status_arr)) {
							$kaoqin_type = 5; //迟到
						} elseif (in_array('3',$status_arr)) {
							$kaoqin_type = 6; //早退
						}
					}
					// } else {
					// 	if($now > $val['sdate'] && empty($val['no_work'])){
					// 		$kuanggong += 1;
					// 	}
					// }
					//判断是否请假、出差
					$is_examine = false;
					if ($examine_list) {
						foreach ($examine_list as $key1=>$val1) {
							$dateList = array();
							$dateList = dateList($val1['start_time'],$val1['end_time']);
							$new_dateList = array();
							foreach ($dateList as $key2=>$val2) {
								$new_dateList[] = $val2['sdate'];
							}
							if (in_array($val['sdate'],$new_dateList) && $val1['type'] == 2) {
								if ($_GET['act'] == 'export') {
									switch ($kaoqin_type) {
										case '5':
											$kaoqin_type = 10; //假&迟
											break;
										case '6':
											$kaoqin_type = 11; //假&退
											break;
										case '2':
											$kaoqin_type = 12; //假&迟&退
											break;
										case '4':
											$kaoqin_type = 16; //假&缺
											break;
										default:
											$kaoqin_type = 7; //假
											break;
									}
								} else {
									$kaoqin_type = 7; //假
								}
								$module_url = U('examine/view','id='.$val1['examine_id'].'&type=2');
								$is_examine = true;
							}
							if (in_array($val['sdate'],$new_dateList) && $val1['type'] == 5) {
								if ($_GET['act'] == 'export') {
									switch ($kaoqin_type) {
										case '4':
											$kaoqin_type = 13; //差&迟
											break;
										case '5':
											$kaoqin_type = 13; //差&迟
											break;
										case '6':
											$kaoqin_type = 14; //差&退
											break;
										case '2':
											$kaoqin_type = 15; //差&迟&退
											break;
										case '4':
											$kaoqin_type = 17; //差&缺
											break;
										default:
											$kaoqin_type = 8; //差
											break;
									}
								} else {
									$kaoqin_type = 8; //差
								}
								$module_url = U('examine/view','id='.$val1['examine_id'].'&type=5');
								$is_examine = true;
							}
						}
					}
					if (!$kaoqin_list) {
						if($now > $val['sdate'] && empty($val['no_work']) && $is_examine == false){
							$kuanggong += 1;
						}
					}
					$role_list[$k]['kaoqin_type'][$key+1]['kaoqin'] = $kaoqin_list;
					$role_list[$k]['kaoqin_type'][$key+1]['title'] = $title;
					$role_list[$k]['kaoqin_type'][$key+1]['type'] = $kaoqin_type;
					$role_list[$k]['kaoqin_type'][$key+1]['kaoqin_count'] = $kaoqin_count;
					$role_list[$k]['kaoqin_type'][$key+1]['url'] = $module_url ? : '';
					$role_list[$k]['kaoqin_type'][$key+1]['search_daka_time'] = $search_daka_time;
				} else {
					$role_list[$k]['kaoqin_type'][$key+1] = array();
				}
				$role_list[$k]['kaoqin_type'][$key+1]['week'] = getTimeWeek($val['sdate']);
			}

			// 出勤数 缺卡数 矿工数 迟到分钟 早退分钟
			$role_list[$k]['chuqin'] = $chuqin;
			$role_list[$k]['queka'] = $queka;
			$role_list[$k]['kuanggong'] = $kuanggong;
			$role_list[$k]['chidao_fenzhong'] = floor($chidao_fenzhong / 60);
			$role_list[$k]['zaotui_fenzhong'] = floor($zaotui_fenzhong / 60);

			// 各种假期类型
			foreach ($jia_list as $j_key => $j_val) {
				$tmp_arr = $$j_val;
				$role_list[$k][$j_val.'_cishu'] =  $tmp_arr[0];
				$role_list[$k][$j_val.'_tianshu'] = $tmp_arr[1];
			}
			// 出差
			$role_list[$k]['chuchai_cishu'] = $chuchai[0];
			$role_list[$k]['chuchai_tianshu'] = $chuchai[1];
			
		}
		$roleList = getUserByRoleIdArray($below_ids);
		foreach	($roleList as $key => $val) {
			if ($val['kaoqin'] != 1 || $val['status'] == 3) {
				unset($roleList[$key]);
			}
		}
		$this->roleList = $roleList;

		if ($_GET['act'] == 'export') {
			if (checkPerByAction('kaoqin','export')) {
				// 考勤规则
				$week = array(1 => '周一', '周二', '周三', '周四', '周五', '周六', '周日');
				$workrule_config = M('WorkruleConfig')->where('year=%d', $search_time_year)->getField('value');
				$workrule_config = trim($workrule_config, ',');
				$workrule_config = str_replace(array_keys($week), $week, $workrule_config);
				$date = M('KaoqinConfig')->field('shangban_time,xiaban_time')->find();
				$workrule_config .= ' '.$date['shangban_time'].'-'.$date['xiaban_time'];
				$kaoqin_list = $m_kaoqin->where(array('role_id' => array('IN', $idArray), 'daka_time' => $month_time))->select();
				$date['year'] = $search_time_year;
				$date['month'] = $search_time_month;
				$this->export($role_list, $workrule_config, $date);
			} else {
				alert('error',  L('HAVE NOT PRIVILEGES'),$_SERVER['HTTP_REFERER']);
			}
		}

		$this->role_list = $role_list;
		$this->alert = parseAlert();
		$this->display();
	}

	private function export($list = null, $workrule_config, $date)
	{
		import("ORG.PHPExcel.PHPExcel");
		$objPHPExcel = new PHPExcel();
		$objProps = $objPHPExcel->getProperties();
		// ======= 第一页 ========
		$objPHPExcel->setActiveSheetIndex(0);
		$objActSheet_1 = $objPHPExcel->getActiveSheet();
		$objActSheet_1->setTitle('月度考勤表');
		$objActSheet_1->freezePane('E6');
		// 表头
		$objActSheet_1->setCellValue('A1', '月度考勤表  '.$date['year'].'-'.$date['month']);
		$objActSheet_1->mergeCells('A1:I1');
		$objActSheet_1->getStyle('A2:I2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objActSheet_1->getStyle('A2')->getFill()->getStartColor()->setRGB('D3A005');
		$objActSheet_1->getStyle('C2')->getFill()->getStartColor()->setRGB('1AB394');
		$objActSheet_1->getStyle('E2')->getFill()->getStartColor()->setRGB('5E916F');
		$objActSheet_1->getStyle('G2')->getFill()->getStartColor()->setRGB('0E6387');
		$objActSheet_1->getStyle('I2')->getFill()->getStartColor()->setRGB('EF4344');
		$objActSheet_1->setCellValue('A2', '迟到');
		$objActSheet_1->setCellValue('C2', '早退');
		$objActSheet_1->setCellValue('E2', '迟&退');
		$objActSheet_1->setCellValue('G2', '缺卡');
		$objActSheet_1->setCellValue('I2', '旷工');

		$three2one = array('A', 'B', 'C', 'D', 'E', 'F', 'G');
		foreach ($three2one as $val) {
			$objActSheet_1->mergeCells($val.'3:'.$val.'5');
		}
		// 第三行
		$objActSheet_1->setCellValue('H3', '考勤异常');
		$objActSheet_1->mergeCells('H3:L3');
		$objActSheet_1->setCellValue('M3', '审批-请假');
		$objActSheet_1->mergeCells('M3:AD3');
		$objActSheet_1->setCellValue('AE3', '审批-出差');
		$objActSheet_1->mergeCells('AE3:AF3');
		// 第四行
		// 14 -31 
		$two2one_x = array('事假', '病假', '调休', '年休假', '婚假', '生育假', '丧假', '外勤假', '其他假');
		$k = 0;
		for ($i=12; $i < 30; $i++) { 
			$col_1 = PHPExcel_Cell::stringFromColumnIndex($i);
			$col_2 = PHPExcel_Cell::stringFromColumnIndex($i+1);
			$objActSheet_1->setCellValue($col_1.'4', $two2one_x[$k]);
			$objActSheet_1->mergeCells($col_1.'4:'.$col_2.'4');
			$k++;
			$i++;
		}
		$two2one_y = array('H', 'I', 'J', 'K', 'L', 'AE', 'AF');
		foreach ($two2one_y as $key => $val) {
			$objActSheet_1->mergeCells($val.'4:'.$val.'5');
		}

		// 第五行
		$table_title_5 = array('员工', '岗位', '部门', '考勤规则', '应出勤天数（天）', '正常出勤（天）', '旷工（天）', '迟到(次)', '迟到(分钟)', '早退(次)', '早退(分钟)', '未打卡(次)', '次数', '天数', '次数', '天数', '次数', '天数', '次数', '天数', '次数', '天数', '次数', '天数', '次数', '天数', '次数', '天数', '次数', '天数', '次数', '住宿天数');
		foreach ($table_title_5 as $key => $val) {
			$col = PHPExcel_Cell::stringFromColumnIndex($key);
			if (in_array($col, $three2one)) {
				$objActSheet_1->setCellValue($col.'3', $val);
			} else if (in_array($col, $two2one_y)) {
				$objActSheet_1->setCellValue($col.'4', $val);
			} else {
				$objActSheet_1->setCellValue($col.'5', $val);
			}
		}
		$row = 6;
		$field = array('full_name', 'position', 'department', 'workrule_config', 'work', 'chuqin', 'kuanggong', 'chidao', 'chidao_fenzhong', 'zaotui', 'zaotui_fenzhong', 'queka');
		foreach ($this->jia_list as $val) {
			$field[] = $val.'_cishu';
			$field[] = $val.'_tianshu';
		}
		$field[] = 'chuchai_cishu';
		$field[] = 'chuchai_tianshu';
		// ======= 第二页 ========
		$objPHPExcel->createSheet();
		$objActSheet_2 = $objPHPExcel->setactivesheetindex(1);
		$objActSheet_2->setTitle('考勤记录');
		$objActSheet_2->freezePane('D3');
		$objActSheet_2->setCellValue('A1', '考勤记录');
		$objActSheet_2->mergeCells('A1:I1');
		$page_2_title = array('员工姓名', '岗位', '部门', '打卡日期', '打卡时间', '打卡状态', '打卡地点/wifi', '实际工作时长(小时)', '备注');
		foreach ($page_2_title as $key => $val) {
			$col = PHPExcel_Cell::stringFromColumnIndex($key);
			$objActSheet_2->setCellValue($col.'2', $val);
		}

		// ======= 第三页 ========
		$objPHPExcel->createSheet();
		$objActSheet_3 = $objPHPExcel->setactivesheetindex(2);
		$objActSheet_3->setTitle('未打卡');
		$objActSheet_3->freezePane('D3');	
		$objActSheet_3->setCellValue('A1', '未打卡');
		$objActSheet_3->mergeCells('A1:I1');
		$page_3_title = array('员工姓名', '岗位', '部门', '日期', '星期', '打卡类型', '规则时间', '状态(未签到、未签退)', '出差(当天)', '请假(当天)');
		foreach ($page_3_title as $key => $val) {
			$col = PHPExcel_Cell::stringFromColumnIndex($key);
			$objActSheet_3->setCellValue($col.'2', $val);
		}

		$row_2 = $row_3 = 3; // 第二 三页开始行数
		$kaoqin_status = array(1 => '正常签到', '迟到', '早退', '正常签退');
		$kaoqin_config_type = array(1 => 'wifi_name', 'address');
		foreach ($list as $key => $val) {
			foreach ($field as $k => $v) {
				$col = PHPExcel_Cell::stringFromColumnIndex($k);
				if ($v === 'workrule_config') {
					$objActSheet_1->setCellValue($col.$row, $workrule_config);
				} else {
					$objActSheet_1->setCellValue($col.$row, $val[$v]);
				}
			}
			// 每一天
			foreach ($val['kaoqin_type'] as $kk => $kv) {
				// 今天之前(含今天)
				if (isset($kv['type'])) {
					$col_num = $kk;
					// === 第二,三页 ===
					$kaoqin_count = count($kv['kaoqin']);
					if($kaoqin_count == 2) {
						$time_length = ceil(abs($kv['kaoqin'][0]['daka_time'] - $kv['kaoqin'][1]['daka_time']) / 3600);
						foreach ($kv['kaoqin'] as $kqk => $kqv) {
							$objActSheet_2->getRowDimension($row_2)->setRowHeight(22);
							$objActSheet_2->setCellValue('A'.$row_2, $val['full_name']);		// 姓名
							$objActSheet_2->setCellValue('B'.$row_2, $val['position']);			// 岗位		
							$objActSheet_2->setCellValue('C'.$row_2, $val['department']);		// 部门
							$objActSheet_2->setCellValue('D'.$row_2, date('Y-m-d', $kqv['daka_time']) . ' ' . $kv['week']);		// 日期
							$objActSheet_2->setCellValue('E'.$row_2, date('H:i:s', $kqv['daka_time']));		// 时间
							$objActSheet_2->setCellValue('F'.$row_2, $kaoqin_status[$kqv['status']]);		// 状态
							$objActSheet_2->setCellValue('G'.$row_2, $kqv[$kaoqin_config_type[$kqv['config_type']]]);		// 地点/WIFI
							$objActSheet_2->setCellValue('H'.$row_2, $time_length);		// 工作时长
							$objActSheet_2->setCellValue('I'.$row_2, $kq['remark']);		// 备注
							$row_2++;
						}
					} else if($kaoqin_count == 1) {
						$kqv = $kv['kaoqin'][0];
						$objActSheet_2->getRowDimension($row_2)->setRowHeight(22);
						$objActSheet_2->setCellValue('A'.$row_2, $val['full_name']);		// 姓名
						$objActSheet_2->setCellValue('B'.$row_2, $val['position']);			// 岗位		
						$objActSheet_2->setCellValue('C'.$row_2, $val['department']);		// 部门
						$objActSheet_2->setCellValue('D'.$row_2, date('Y-m-d', $kqv['daka_time']) . ' ' . $kv['week']);		// 日期
						$objActSheet_2->setCellValue('E'.$row_2, date('H:i:s', $kqv['daka_time']));		// 时间
						$objActSheet_2->setCellValue('F'.$row_2, $kaoqin_status[$kqv['status']]);		// 状态
						$objActSheet_2->setCellValue('G'.$row_2, $kqv[$kaoqin_config_type[$kqv['config_type']]]);		// 地点/WIFI
						$objActSheet_2->setCellValue('H'.$row_2, '缺卡,无法计算时长');		// 工作时长
						$objActSheet_2->setCellValue('I'.$row_2, $kq['remark']);		// 备注
						$row_2++;
						if ($kqv['status'] == 1 || $kqv['status'] == 4) {
							$daka_type = '签退';
							$guize_time = $date['xiaban_time'];
							$queka_status = '未签退';
						} else {
							$daka_type = '签到';
							$guize_time = $date['shangban_time'];
							$queka_status = '未签到';
						}
						$objActSheet_3->getRowDimension($row_3)->setRowHeight(22);
						$objActSheet_3->setCellValue('A'.$row_3, $val['full_name']);		// 姓名
						$objActSheet_3->setCellValue('B'.$row_3, $val['position']);			// 岗位		
						$objActSheet_3->setCellValue('C'.$row_3, $val['department']);		// 部门
						$objActSheet_3->setCellValue('D'.$row_3, date('Y-m-d', $kqv['daka_time']));		// 日期
						$objActSheet_3->setCellValue('E'.$row_3, $kv['week']);		// 星期
						$objActSheet_3->setCellValue('F'.$row_3, $daka_type);		// 打卡类型
						$objActSheet_3->setCellValue('G'.$row_3, $guize_time);		// 规则时间
						$objActSheet_3->setCellValue('H'.$row_3, $queka_status);		// 状态
						$chuchai = ($kv['type'] == 8) ? '有' : '无';
						$objActSheet_3->setCellValue('I'.$row_3, $chuchai);		// 出差(当日)
						$qingjia = ($kv['type'] == 7) ? '有' : '无';
						$objActSheet_3->setCellValue('J'.$row_3, $qingjia);		// 请假(当日)
						$row_3++;
					} else {
						for ($i=0; $i < 2; $i++) {
							$daka_type = $i ? '签退' : '签到';
							$guize_time = $i ? $date['xiaban_time'] : $date['shangban_time'];
							$queka_status = $i ? '未签退' : '未签到';
							$objActSheet_3->getRowDimension($row_3)->setRowHeight(22);
							$objActSheet_3->setCellValue('A'.$row_3, $val['full_name']);		// 姓名
							$objActSheet_3->setCellValue('B'.$row_3, $val['position']);			// 岗位		
							$objActSheet_3->setCellValue('C'.$row_3, $val['department']);		// 部门
							$objActSheet_3->setCellValue('D'.$row_3, substr($kv['search_daka_time'], 0, 10));		// 日期
							$objActSheet_3->setCellValue('E'.$row_3, $kv['week']);		// 星期
							$objActSheet_3->setCellValue('F'.$row_3, $daka_type);		// 打卡类型
							$objActSheet_3->setCellValue('G'.$row_3, $guize_time);		// 规则时间
							$objActSheet_3->setCellValue('H'.$row_3, $queka_status);		// 状态
							$chuchai = ($kv['type'] == 8) ? '有' : '无';
							$objActSheet_3->setCellValue('I'.$row_3, $chuchai);		// 出差(当日)
							$qingjia = ($kv['type'] == 7) ? '有' : '无';
							$objActSheet_3->setCellValue('J'.$row_3, $qingjia);		// 请假(当日)
							$row_3++;
						}
					}
					// === 第二,三页 END ===
				}
				$col = PHPExcel_Cell::stringFromColumnIndex($k + $kk);
				$objActSheet_1->getStyle($col.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				// 表头
				$objActSheet_1->setCellValue($col.'3', $kv['week']);
				$objActSheet_1->setCellValue($col.'4', $kk.'日');
				$objActSheet_1->mergeCells($col.'4:'.$col.'5');
				// 1正常 2迟&退 3休 4缺卡 5迟到 6早退 7假 8差 9旷工 10假&迟 11假&退 12假&迟&退 13差&迟 14差&退 15差&迟&退 16假&缺 17差&缺
				$content = '';
				switch ($kv['type']) {
					case 1:
						$content = "正常 \n\n 详情： \n 【签到】 {$kv['qiandao']} \n 【签退】 {$kv['qiantui']}";
						break;
					case 2:
						$objActSheet_1->getStyle($col.$row)->getFill()->getStartColor()->setRGB('5E916F');
						$content = "迟&退  \n\n 详情： \n 【签到】 {$kv['qiandao']} \n 备注：{$kv['remark']['chidao']} \n 【签退】 {$kv['qiantui']} \n 备注：{$kv['remark']['zaotui']}";
						break;
					case 3:
						$content = "休";
						break;
					case 4:
						if ($kv['qiandao']) {
							$qiandao = $kv['qiandao'];
						} else {
							$qiandao = $date['shangban_time'] . ' 未打卡';
						}
						if ($kv['qiantui']) {
							$qiantui = $kv['qiantui'];
						} else {
							$qiantui = $date['xiaban_time'] . ' 未打卡';
						}
						$objActSheet_1->getStyle($col.$row)->getFill()->getStartColor()->setRGB('0E6387');
						$content = "缺卡  \n\n 详情： \n 【签到】 {$qiandao} \n 【签退】 {$qiantui}";
						break;
					case 5:
						$objActSheet_1->getStyle($col.$row)->getFill()->getStartColor()->setRGB('D3A005');
						$content = "迟到  \n\n 详情： \n 【签到】 {$kv['qiandao']} \n 备注：{$kv['remark']['chidao']} \n 【签退】 {$kv['qiantui']}";
						break;
					case 6:
						$objActSheet_1->getStyle($col.$row)->getFill()->getStartColor()->setRGB('1AB394');
						$content = "早退  \n\n 详情： \n 【签到】 {$kv['qiandao']} \n 【签退】 {$kv['qiantui']} \n 备注：{$kv['remark']['zaotui']}";
						break;
					case 7:
						$content = "请假";
						break;
					case 8:
						$content = "出差";
						break;
					case 9:
						$objActSheet_1->getStyle($col.$row)->getFill()->getStartColor()->setRGB('EF4344');
						$content = "旷工  \n\n 详情： \n 【签到】 {$date['shangban_time']} 未打卡 \n 【签退】 {$date['xiaban_time']} 未打卡";
						break;
					case 10:
						$objActSheet_1->getStyle($col.$row)->getFill()->getStartColor()->setRGB('D3A005');
						$content = "假&迟  \n\n 详情： \n 【签到】 {$kv['qiandao']} \n 备注：{$kv['remark']['chidao']} \n 【签退】 {$kv['qiantui']}";
						break;
					case 11:
						$objActSheet_1->getStyle($col.$row)->getFill()->getStartColor()->setRGB('1AB394');
						$content = "假&退  \n\n 详情： \n 【签到】 {$kv['qiandao']} \n 【签退】 {$kv['qiantui']} \n 备注：{$kv['remark']['zaotui']}";
						break;
					case 12:
						$objActSheet_1->getStyle($col.$row)->getFill()->getStartColor()->setRGB('5E916F');
						$content = "假&迟&退  \n\n 详情： \n 【签到】 {$kv['qiandao']} \n 备注：{$kv['remark']['chidao']} \n 【签退】 {$kv['qiantui']} \n 备注：{$kv['remark']['zaotui']}";
						break;
					case 13:
						$objActSheet_1->getStyle($col.$row)->getFill()->getStartColor()->setRGB('D3A005');
						$content = "差&迟  \n\n 详情： \n 【签到】 {$kv['qiandao']} \n 备注：{$kv['remark']['chidao']} \n 【签退】 {$kv['qiantui']}";
						break;
					case 14:
						$objActSheet_1->getStyle($col.$row)->getFill()->getStartColor()->setRGB('1AB394');
						$content = "差&退  \n\n 详情： \n 【签到】 {$kv['qiandao']} \n 【签退】 {$kv['qiantui']} \n 备注：{$kv['remark']['zaotui']}";
						break;
					case 15:
						$objActSheet_1->getStyle($col.$row)->getFill()->getStartColor()->setRGB('5E916F');
						$content = "差&迟&退  \n\n 详情： \n 【签到】 {$kv['qiandao']} \n 备注：{$kv['remark']['chidao']} \n 【签退】 {$kv['qiantui']} \n 备注：{$kv['remark']['zaotui']}";
						break;
					case 16:
						if ($kv['qiandao']) {
							$qiandao = $kv['qiandao'];
						} else {
							$qiandao = $date['shangban_time'] . ' 未打卡';
						}
						if ($kv['qiantui']) {
							$qiantui = $kv['qiantui'];
						} else {
							$qiantui = $date['xiaban_time'] . ' 未打卡';
						}
						$objActSheet_1->getStyle($col.$row)->getFill()->getStartColor()->setRGB('0E6387');
						$content = "假&缺  \n\n 详情： \n 【签到】 {$qiandao} \n 备注：{$kv['remark']['chidao']} \n 【签退】 {$qiantui}";
						break;
					case 17:
						if ($kv['qiandao']) {
							$qiandao = $kv['qiandao'];
						} else {
							$qiandao = $date['shangban_time'] . ' 未打卡';
						}
						if ($kv['qiantui']) {
							$qiantui = $kv['qiantui'];
						} else {
							$qiantui = $date['xiaban_time'] . ' 未打卡';
						}
						$objActSheet_1->getStyle($col.$row)->getFill()->getStartColor()->setRGB('0E6387');
						$content = "差&缺  \n\n 详情： \n 【签到】 {$qiandao} \n 备注：{$kv['remark']['chidao']} \n 【签退】 {$qiantui}";
						break;
				}
				$objActSheet_1->setCellValue($col.$row, $content);
			}
			$objActSheet_1->getRowDimension($row)->setRowHeight(135);
			$row++;
		}

		$row--;
		$borderStyle = array(  
			'borders' => array(  
				'allborders' => array(  
					'style' => PHPExcel_Style_Border::BORDER_THIN, 		// 细边框  
					'color' => array('argb' => '00000000'),  
				),  
			),  
		);
		$objActSheet_1->getStyle('A1:I2')->applyFromArray($borderStyle);		// 1 2的边框
		$objActSheet_1->getStyle('A3:'.$col.'5')->applyFromArray($borderStyle); 	// 3 4 5行的边框
		$col_border = PHPExcel_Cell::stringFromColumnIndex(31 + $col_num);
		$objActSheet_1->getStyle('A6:'.$col_border.$row)->applyFromArray($borderStyle);		// 其他所有内容
		$objActSheet_2->getStyle('A1:I'.$row_2)->applyFromArray($borderStyle);		// 第二页
		$objActSheet_3->getStyle('A1:J'.$row_3)->applyFromArray($borderStyle);		// 第三页

		// 背景色
		$objActSheet_1->getStyle('A6:D'.$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objActSheet_1->getStyle('A6:D'.$row)->getFill()->getStartColor()->setRGB('99CCFF');		// 左侧锁死背景色
		$objActSheet_1->getStyle('A3:'.$col.'5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objActSheet_1->getStyle('A3:'.$col.'5')->getFill()->getStartColor()->setRGB('99CCFF');		// 三四五行背景色
		$objActSheet_2->getStyle('A2:I2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objActSheet_2->getStyle('A2:I2')->getFill()->getStartColor()->setRGB('99CCFF');
		$objActSheet_3->getStyle('A2:J2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objActSheet_3->getStyle('A2:J2')->getFill()->getStartColor()->setRGB('99CCFF');

		// 垂直居中
		$objActSheet_1->getStyle('A1:I2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);		// 一二行
		$objActSheet_1->getStyle('A3:'.$col.$row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);		// 其他所有
		$objActSheet_2->getStyle('A1:I'.$row_2)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objActSheet_3->getStyle('A1:J'.$row_3)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		// 水平居中
		$objActSheet_1->getStyle('A1:I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 		// 一二行
		$objActSheet_1->getStyle('A3:'.$col.'5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		// 三四五
		$objActSheet_1->getStyle('A6:AF'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		// 三四五
		$objActSheet_2->getStyle('A1:I'.$row_2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objActSheet_3->getStyle('A1:J'.$row_3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		// 自动换行
		$objActSheet_1->getStyle('A1:'.$col.$row)->getAlignment()->setWrapText(TRUE);
		
		// 行高
		$objActSheet_1->getRowDimension(1)->setRowHeight(22);
		$objActSheet_1->getRowDimension(2)->setRowHeight(32);
		$objActSheet_2->getRowDimension(1)->setRowHeight(25);
		$objActSheet_2->getRowDimension(2)->setRowHeight(25);
		$objActSheet_3->getRowDimension(2)->setRowHeight(25);
		$objActSheet_3->getRowDimension(2)->setRowHeight(25);

		// 列宽
		for ($i=0; $i < 32; $i++) { 
			$col = PHPExcel_Cell::stringFromColumnIndex($i);
			if ($i < 9) {
				$objActSheet_2->getColumnDimension($col)->setWidth(15);
			}
			if ($i < 10) {
				$objActSheet_3->getColumnDimension($col)->setWidth(15);
			}
			$objActSheet_1->getColumnDimension($col)->setWidth(12);
		}
		$objActSheet_3->getColumnDimension('D')->setWidth(20);
		$objActSheet_1->getColumnDimension('D')->setWidth(20);
		foreach (array('E', 'F', 'L', 'M') as $val) {
			$objActSheet_1->getColumnDimension($val)->setWidth(18);
		}
		for($i = 32; $i < 32 + $col_num; $i++) {
			$col = PHPExcel_Cell::stringFromColumnIndex($i);
			$objActSheet_1->getColumnDimension($col)->setWidth(25);
		}

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		ob_end_clean();
		header("Content-Type: application/vnd.ms-excel;");
        header("Content-Disposition:attachment;filename=pdcrm_月度考勤表_{$date['year']}-{$date['month']}.xls");
        header("Pragma:no-cache");
        header("Expires:0");
		$objWriter->save('php://output');
		exit();
	}

	/**
	 * 考勤统计
	 **/
	public function record(){
		//权限判断
		if(!getPerByAction('kaoqin','analytics')){
			alert('error','您没有此权利！',0);
		}
		$m_kaoqin = M('Kaoqin');
		//权限范围
		$below_ids = getPerByAction('kaoqin','analytics');
		$role_id_array = array();
		if(intval($_GET['role'])){
			$role_id_array = array(intval($_GET['role']));
		}else{
			if(intval($_GET['department'])){
				$department_id = intval($_GET['department']);
				foreach(getRoleByDepartmentId($department_id, true) as $k=>$v){
					$role_id_array[] = $v['role_id'];
				}
			}
		}
		//过滤权限范围内的role_id
		if($role_id_array){
			//数组交集
			$idArray = array_intersect($role_id_array,$below_ids);
		}else{
			$idArray = $below_ids;
		}
		//时间段搜索
		if($_GET['daka_time']){
			$daka_time = explode(' - ',trim($_GET['daka_time']));
			if($daka_time[0]){
				$start_time = strtotime($daka_time[0]);
			}
			$end_time = $daka_time[1] ?  strtotime(date('Y-m-d 23:59:59',strtotime($daka_time[1]))) : strtotime(date('Y-m-d 23:59:59',time()));
			$params[] = "daka_time=" . trim($_GET['daka_time']);
		}else{
			$start_time = strtotime(date('Y-m-01 00:00:00'));
			$end_time = strtotime(date('Y-m-d H:i:s'));
		}
		if($_GET['role_id']){
			$where['role_id'] = intval($_GET['role_id']);
			$params[] = "role_id=" . trim($_GET['role_id']);
		}else{
			$where['role_id'] = array('in',$idArray);
		}
		if($_GET['month']){
			//本月时间戳范围
			$start_time = $month_start_time = strtotime(date($_GET['year'].'-'.$_GET['month'].'-01')); 
			$end_time = $month_end_time = strtotime($_GET['year']."-".$_GET['month']."-".date("t",$month_start_time))+86400;
			$month_time = array('between',array($month_start_time,$month_end_time));
			$where['daka_time'] = $month_time;
			$params[] = "year=" . trim($_GET['year']);
			$params[] = "month=" . trim($_GET['month']);
		}else{
			$where['daka_time'] = array('between',array($start_time,$end_time));
		}
		if($_GET['status']){
			$where['status'] = trim($_GET['status']);
			$params[] = "status=" . trim($_GET['status']);
		}
	
		if($_GET['listrows']){
			$listrows = intval($_GET['listrows']);
			$params[] = "listrows=" . intval($_GET['listrows']);
		}else{
			$listrows = 15;
			$params[] = "listrows=".$listrows;
		}
		$p = intval($_GET['p'])?intval($_GET['p']):1;

		$count = $m_kaoqin->where($where)->count();
		$p_num = ceil($count/$listrows);
		if($p_num<$p){
			$p = $p_num;
		}
		$list = $m_kaoqin->where($where)->order('daka_time desc')->page($p.','.$listrows)->select();
		import("@.ORG.Page");
		$Page = new Page($count,$listrows);
		
		$this->parameter = implode('&', $params);
		$weekarray=array("日","一","二","三","四","五","六");
		foreach ($list as $k => $v) {
			$user_info = array();
			$user_info = getUserByRoleId($v['role_id']);
			$list[$k]['user'] = $user_info;
			$list[$k]['date'] = date('Y-m-d',$v['daka_time']);
			$list[$k]['time'] = date('H:i:s',$v['daka_time']);
			$list[$k]['week'] = "星期".$weekarray[date("w",$v['daka_time'])];
			$list[$k]['address'] = $v['address'] ? $v['address'] : $v['wifi_name'];
			switch ($v['status']) {
				case 1: $status_name = '正常签到'; break;
				case 2: $status_name = '迟到'; break;
				case 3: $status_name = '早退'; break;
				case 4: $status_name = '正常签退'; break;
			}
			$list[$k]['status_name'] = $status_name;
		}
		
		$this->daterange = daterange();	
		//部门岗位
		$url = getCheckUrlByAction(MODULE_NAME,ACTION_NAME);
		$per_type =  M('Permission')->where('position_id = %d and url = "%s"', session('position_id'), $url)->getField('type');
		if($per_type == 2 || session('?admin')){
			$departmentList = M('RoleDepartment')->select();
		}else{
			$departmentList = M('RoleDepartment')->where('department_id =%d',session('department_id'))->select();
		}
		$this->assign('departmentList', $departmentList);
		$this->roleList = getUserByRoleIdArray($below_ids);
		$this->list = $list;
		$this->role_list = $role_list;
		$this->start_date = date('Y-m-d',$start_time);
		$this->end_date = date('Y-m-d',$end_time);
		$this->listrows = $listrows;
		$this->assign('count',$count);
		$Page->parameter = implode('&', $params);
		$this->assign('page', $Page->show());
		$this->alert = parseAlert();
		$this->display();
	}

	/**
	 * 考勤规则设置
	 * @param 
	 * @author 
	 * @return 
	 */
	public function setting() {
		$m_kaoqin_config = M('KaoqinConfig');
		$m_route = M('Route');
		$config_info = $m_kaoqin_config->find();
		if ($this->isPost()) {
			$attendance_address = explode(',',trim($_POST['attendance_address']));
			if ($_POST['attendance_address']) {
				$y = $attendance_address[0];
				$x = $attendance_address[1];
			} else {
				$x = '';
				$y = '';
			}
			
			if ($m_kaoqin_config->create()) {
				$m_kaoqin_config->x = $x;
				$m_kaoqin_config->y = $y;
				$m_kaoqin_config->create_role_id = session('role_id');
				$m_kaoqin_config->update_time = time();
				if ($config_info) {
					$m_kaoqin_config->where(array('id'=>$config_info['id']))->save();
				} else {
					$m_kaoqin_config->add();
				}
			}
			$route = array_values($_POST['route']);
			$m_route->where(array('id'=>array('gt',0)))->delete();
			if ($route) {
				foreach ($route as $k=>$v) {
					$data = array();
					$data['wifi_name'] = $v['wifi_name'];
					$data['mac_address'] = $v['mac_address'];
					$data['create_time'] = time();
					$data['create_role_id'] = session('role_id');
					$m_route->add($data);
				}
			}
			alert('success','保存成功！',$_SERVER['HTTP_REFERER']);
		} else {
			$this->route_list = $m_route->select();
			$config_info['attendance_address'] = '';
			if ($config_info['x'] && $config_info['y']) {
				$config_info['attendance_address'] = $config_info['y'].','.$config_info['x'];
			}
			$config_info['radius'] = $config_info['radius'] ? $config_info['radius'] : '';	
			$this->config_info = $config_info;
			// dd($config_info);
			$this->alert = parseAlert();
			$this->display();
		}
	}

	/**
	 * 考勤规则地图
	 * @param 
	 * @author 
	 * @return 
	 */
	public function set_map(){
		$this->display();
	}

	/**
	 * 考勤月历
	 * @param 
	 * @author 
	 * @return 
	 */
	public function index(){
		$this->now_date = date('Y-m-d',time());
		$this->alert = parseAlert();
		$this->display();
	}

	/**
	 * 考勤月历(数据调用)
	 * @param 
	 * @author 
	 * @return 
	 */
	public function indexdata(){
		//权限判断
		// $below_ids = getPerByAction('kaoqin','index');
		// if(!$below_ids){
		// 	$this->ajaxReturn('','您没有此权利！',0);
		// }
		$m_kaoqin = M('Kaoqin');
		$m_user = M('User');

		$where = array();
		$order = "daka_time asc";
		$type = $_POST['type'] ? trim($_POST['type']) : '';

		if($_GET['search_date']){
			$timestamp = strtotime($_POST['search_date']);
			$mdays = date('t',$timestamp);
			$start_time = strtotime(date('Y-m-1 00:00:00',$timestamp));
			$end_time = strtotime(date('Y-m-'.$mdays.' 23:59:59',$timestamp));
		}else{
			if($_GET['start'] && $_GET['end']){
				$start_time = trim($_GET['start']);
				$end_time = trim($_GET['end']);
			}else{
				$start_time = strtotime(date('Y-m-01')); 
				$end_time = strtotime(date("Y")."-".date("m")."-".date("t"))+86400;
			}
		}
		$where['daka_time'] = array('between',array($start_time,$end_time));
		$where['role_id'] = session('role_id');

		$list = $m_kaoqin->where($where)->order($order)->select();
		//status 状态（1 正常签到）（2 迟到）（3 早退）（4 正常签退）
		foreach($list as $k=>$v){
			$start_date = strtotime(date('Y-m-d',$v['daka_time']));
			$end_date = $start_date+86399;
			switch ($v['status']) {
				case '1' : 
					$name = '【 签到 】'.date('H:i:s',$v['daka_time']);
					$color = '#62A8EA';
					break;
				case '2' : 
					$name = '【 迟到 】'.date('H:i:s',$v['daka_time']);
					$color = '#d3a005';
					break;
				case '3' :
					$name = '【 早退 】'.date('H:i:s',$v['daka_time']);
					$color = '#1ab394';
					break;
				case '4' :
					$name = '【 签退 】'.date('H:i:s',$v['daka_time']);
					$color = '#62A8EA';
					break;
				default : 
					$name = '【 缺卡 】';
					break;
			}
			$list[$k]['title'] = trim($v['remark']) ? $name.'   备注：'.trim($v['remark']) : $name;
			$list[$k]['start'] = date('Y-m-d H:i:s',$start_date);
			$list[$k]['end'] = date('Y-m-d H:i:s',$end_date);
			$list[$k]['allDay'] = true;
			$list[$k]['color'] = $color;
		}
		if($this->isAjax()){
			echo $kaoqin_list = json_encode($list);
		}
	}


	public function remind()
	{
		$workrule = M('workrule')->where(array('sdate' => strtotime(date('Y-m-d'))))->getField('type');
		if ($workrule != 2) {
			dd('今天休息。');
		}
		if ($_GET['kklt'] == 'wukong') {
			$record = F('kaoqin_remind');
			$today = date('Ymd');

			if ($record['date'] != $today) {
				$record = array('date' => $today, 'am' => False, 'pm' => False);
			}

			$m_config = M('Config');
			$num_id = $m_config->where('name = "num_id"')->getField('value');

			if (date('H') < 12) {
				if ($record['am']) {
					dd('上班打卡提醒已推送。');
				}
				$record['am'] = True;
				$msg = '上班时间快到了，别忘记打卡。';
			} else {
				if ($record['pm']) {
					dd('下班打卡提醒已推送。');
				}
				$record['pm'] = True;
				$msg = '下班时间快到了，别忘记打卡和填写工作日志。';
			}

			F('kaoqin_remind', $record);
			if ($user_id = (int) $_GET['user_id']) {
				$role_id = M('User')->where('user_id=' . $user_id)->getField('role_id');
				if ($role_id) {
					$user_list = array($role_id);
				} else {
					dd('user_id错误');
				}
			} elseif ($role_id = (int) $_GET['role_id']) {
				$user_list = array($role_id);
			} else {
				$user_list = M('User')->where(array('kaoqin' => 1))->getField('role_id', true);
			}
			// $num_id = '100200';
			// $user_list = array(208);
			// $user_list = array(149, 193, 131, 208);
			foreach ($user_list as $role_id) {
				$account_list[] = $num_id . '@' . $role_id;
			}
			$res = Xinge2($account_list, '您有新的消息!', msubstr($msg, 0, 30));

			dd($msg, $res);
		}
	}

	public function remind2()
	{
		// if ($_GET['kklt'] != 'wukong') dd('没有权限！');

		$workrule = M('workrule')->where(array('sdate' => strtotime(date('Y-m-d'))))->getField('type');
		// if ($workrule != 2) dd('今天休息。');

		$push_record = F('kaoqin_remind_2');
		$today = date('Y-m-d');
		if ($push_record['date'] != $today) {
			$record = array('date' => $today, 'time' => date('H:i:s'));
		} else {
			// dd('今日已推送。');
		}

		// 上下班时间
		$config = M('KaoqinConfig')->field('shangban_time, xiaban_time')->find();
		$sb_date = $config['shangban_time'];
		$xb_date = $config['xiaban_time'];
		$xb_date = $sb_date = '16:25:10';

		// 上下班提示信息
		$sb_msg = '上班时间快到了，别忘记打卡。';
		$xb_msg = '下班时间快到了，别忘记打卡和填写工作日志。';

		// 组织名
		$num_id = M('Config')->where('name = "num_id"')->getField('value');
		$num_id = '100200';

		// 员工列表
		$user_list = M('User')->where(array('kaoqin' => 1, 'status' => 1))->getField('role_id', true);
		$user_list = array('208', '131', '209');

		// 信鸽账号列表
		$account_list = array();
		foreach ($user_list as $role_id) {
			$account_list[] = $num_id . '@' . $role_id;
		}

		// 上班
		if ($sb_date && $sb_time = strtotime($today . ' ' . $sb_date)) {
			// $sb_time -= 60 * 10;	// 提前十分钟提醒
			// $sb_res = Xinge2($account_list, '您有新的消息!', msubstr($sb_msg, 0, 30), date('Y-m-d H:i:s', $sb_time));
			$sb_res = Xinge2('100200@209', '您有新的消息!', msubstr($sb_msg, 0, 30), date('Y-m-d H:i:s', $sb_time));
			dd(date('Y-m-d H:i:s', $sb_time), $sb_res);
		}
		// 下班
		if ($xb_date && $xb_time = strtotime($today . ' ' . $xb_date)) {
			$xb_res = Xinge2($account_list, '您有新的消息!', msubstr($xb_msg, 0, 30), date('Y-m-d H:i:s', $xb_time));
		}
		
		$record['res'] = array('上班' => $sb_res, '下班' => $xb_res);
		F('kaoqin_remind_2', $record);
	}

}
