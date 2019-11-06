<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>CRM管理系统</title>
<!--	<?php echo C('defaultinfo.name');?> - Powered By <?php echo L('AUTHOR');?>-->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="author" content="<?php echo L('AUTHOR');?>"/>
	<!-- 360浏览器默认使用Webkit内核 -->
	<meta name="renderer" content="webkit">
	<link rel="shortcut icon" href="__PUBLIC__/ico/favicon.png"/>
	<link type="text/css" href="__PUBLIC__/css/jquery-ui-1.10.0.custom.css" rel="stylesheet" />
	<link type="text/css" href="__PUBLIC__/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link href="__PUBLIC__/style/css/bootstrap.min.css" rel="stylesheet">
	<link href="__PUBLIC__/style/font-awesome/css/font-awesome.css" rel="stylesheet">
	<link href="__PUBLIC__/css/font-awesome.min.css" rel="stylesheet">
	<link type="text/css" href="__PUBLIC__/css/hovershow.css" rel="stylesheet"/>
	<!-- animate -->
	<link rel='stylesheet' href='__PUBLIC__/css/animate/animate.min.css'>
	<link rel="stylesheet" href="__PUBLIC__/css/animate/notification.css">

	<link href="__PUBLIC__/css/new.css">
	<style type="text/css">
		a.active{
			font-weight: bold;
			color: #777;
		}
		.nobr{
			white-space:nowrap;
		}
		#header-top{
		    position: absolute;
		    right:0px;
		    z-index: 102;
		}
		.sidebar-collapse{z-index:9999;}
		.tooltip{width:85px;line-height:36px;}
		.tooltip-inner{padding:3px 12px;text-align:left;}
		.table{margin-bottom:0px;}
		@media (max-width: 768px){
			.nav.left-side{
				display: none;
			}
		}
		.navbar{margin-bottom:0px;}
		.check_list{width: 20px;height: 20px;}
		.check_all{width: 20px;height: 20px;}
		.check_all{width: 20px;height: 20px;}
		input[type=checkbox], input[type=radio]{margin-top:0px;}
		.radio.radio-inline {padding-left: 0px;}
		.alert-error{margin:20px;line-height:30px;}
		.text{color:#000;}
		.notification{top:150px;z-index:9999;}
		.checkbox, .radio{margin:0 auto;}
		/*闪烁效果*/
		.crm_heart a{
		    animation:heart 1s ease infinite;
		}
		@keyframes heart {
	        0% {color:#FF6D57;}
	        100%{color:#93A6B5;}
		}

		#side-menu a .fa{
			width: 15px;
			text-align: center;
		}

	</style>
	<!-- Toastr style -->
	<link href="__PUBLIC__/style/css/plugins/toastr/toastr.min.css" rel="stylesheet">
	<!-- Sweet Alert -->
	<link href="__PUBLIC__/style/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
	<link href="__PUBLIC__/style/css/style.css" rel="stylesheet">
	<link href="__PUBLIC__/style/css/new.style.css" rel="stylesheet">
    <link href="__PUBLIC__/style/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css" rel="stylesheet">
	<!-- Mainly scripts -->
	<script src="__PUBLIC__/style/js/jquery-2.1.1.js"></script>
	<script src="__PUBLIC__/style/js/bootstrap.min.js"></script>
	<script src="__PUBLIC__/js/daterangepicker/moment.min.js"></script>
	<script src="__PUBLIC__/style/js/jquery.form.js" type="text/javascript"></script>
	<script src="__PUBLIC__/style/js/plugins/metisMenu/jquery.metisMenu.js"></script>
	<script src="__PUBLIC__/style/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
	<!-- Toastr -->
	<script src="__PUBLIC__/style/js/plugins/toastr/toastr.min.js"></script>
	<!-- Custom and plugin javascript -->
	<script src="__PUBLIC__/style/js/plugins/pace/pace.min.js"></script>
	
	<script src="__PUBLIC__/js/pdcrm_zh-cn.js" type="text/javascript"></script>
	<script src="__PUBLIC__/js/pdcrm.js" type="text/javascript"></script>
	<!-- jQuery UI -->
	<script src="__PUBLIC__/js/jquery-ui-1.10.0.custom.min.js" type="text/javascript"></script>
	<script src="__PUBLIC__/js/WdatePicker.js" type="text/javascript"></script>
	<!-- Sweet alert -->
	<script src="__PUBLIC__/style/js/plugins/sweetalert/sweetalert.min.js"></script>
	<!-- Jquery Validate -->
	<script src="__PUBLIC__/style/js/plugins/validate/jquery.validate.min.js"></script>
	<script src="__PUBLIC__/style/js/plugins/validate/messages_zh.min.js"></script>
	<script src="__PUBLIC__/js/bootstrap-tooltip.js"></script>
	<!-- 下拉筛选 -->
	<link rel="stylesheet" href="__PUBLIC__/css/bootstrap-select.css">
	<script type="text/javascript" src="__PUBLIC__/js/bootstrap-select.js" charset="UTF-8"></script>
	<!-- select2 -->
	<link href="__PUBLIC__/style/css/plugins/select2/select2.min.css" rel="stylesheet">
	<script src="__PUBLIC__/style/js/plugins/select2/select2.full.min.js"></script>
</head>
<script>
$(function(){
	var innerHeight = window.innerHeight;
	if(innerHeight < 768){
		innerHeight = 768;
	}
	$("#page-wrapper").css("min-height",innerHeight);
	$(window).resize(function(){
		var innerHeight = window.innerHeight;
		if(innerHeight < 768){
			innerHeight = 768;
		}
		$("#page-wrapper").css("min-height",innerHeight);
	});
	$(".select2").select2({
        placeholder: "--请选择--"
        // allowClear: true
    });

	setTimeout(() => {
		$("#left_sidebar_div").height(window.innerHeight-10);
	}, 1);
	$(window).resize(function(){
		$("#left_sidebar_div").height(window.innerHeight-10);
	})
});
</script>
<body class="navbar <?php echo cookie('mini-navbar') == 1 ? 'mini-navbar':'';?>">
	<div id="wrapper">
		<nav class="navbar-default navbar-static-side" role="navigation" >
			<div id="left_sidebar_div" class="full-height-scroll">
				<div class="sidebar-collapse" style="width:inherit; height: 100%;">
					<?php
 $d_system = D('System'); $show_module_list = $d_system->showModuleList(); $customer_url = $d_system->customerUrl(); $analytics_url = $d_system->analyticsUrl(); $setting_url = $d_system->settingUrl(); ?>
					<script>
						$(() => {
							$('.nav.metismenu.left-side>li').each((key, val) => {
								if ($(val).is('.nav-header')) {
									return true;
								}
								if ($(val).is('.menu-btn')) {
									return true; // 新修改显示 展开按钮 移至菜单栏 0520
								}
								if ($(val).is('.rhome-btn')) {
									return true; // 新修改显示 展开按钮 移至菜单栏 0520
								}
								if ($(val).find('a').next('ul').find('li').length == 0) {
									$(val).hide();
								}
							});
						});
					</script>
					<ul class="nav metismenu left-side" id="side-menu" >
						<li class="nav-header">
							<?php
 $defaultinfo_info = M('Config')->where('name = "defaultinfo"')->find(); $defaultinfo = unserialize($defaultinfo_info['value']); ?>
								<?php if($defaultinfo['logo_min_thumb_path']): ?><img class="img-circle" src="<?php echo ($defaultinfo['logo_min_thumb_path']); ?>" style="" alt="<?php echo ($defaultinfo['name']); ?>">
								<?php else: ?>
									<img class="img-circle" src="__PUBLIC__/img/logo2.png" alt="PDCRM"><?php endif; ?>
						</li>
						<li class="menu-btn navbar-minimalize">
							<a class="minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i></a>
							<span>折叠菜单</span>
					
						</li>
						<li class="rhome-btn">
											
<a href="<?php echo U('Index/index');?>" title="回首页"><i class="fa fa-home"></i><span class="nav-label">回首页</span><span class="fa arrow"></span></a>
					
						</li>
						<?php if (in_array('customer',$show_module_list)): ?>
						<li>
							<a href="#" title="客户管理"><i class="fa fa-user"></i><span class="nav-label">客户管理</span><span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<?php if(checkPerByAction('leads','index')): ?><li id="leads-index"><a href="<?php echo U('leads/index');?>"><span class="fa fa fa-child"></span>&nbsp;&nbsp;线索</a></li>
									<li id="leads-index-public"><a href="<?php echo U('leads/index','by=public');?>"><span class="fa fa fa-child"></span>&nbsp;&nbsp;线索池</a></li><?php endif; ?>
								<li class="divider"></li>
								<?php if(checkPerByAction('customer','index')): ?><li id="customer-index"><a href="<?php echo ($customer_url); ?>"><span class="fa fa-user"></span>&nbsp;&nbsp;客户</a></li><?php endif; ?>
								<?php if(checkPerByAction('customer','index')): ?><li id="customer-index-resource"><a href="<?php echo U('customer/index','content=resource');?>"><span class="fa fa-group"></span>&nbsp;&nbsp;客户池</a></li><?php endif; ?>
								<?php if(checkPerByAction('contacts','index')): ?><li id="contacts-index"><a href="<?php echo U('contacts/index');?>"><span class="fa fa-phone-square"></span>&nbsp;&nbsp;客户联系人</a></li><?php endif; ?>
								<li id="customer-nearby"><a href="<?php echo U('customer/nearby');?>"><span class="fa fa-map-marker"></span>&nbsp;&nbsp;附近的客户</a></li>
							</ul>
						</li>
						<?php endif; ?>
						
						<?php if (in_array('business',$show_module_list)): ?>
						<li>
							<a href="#" title="商机管理"><i class="fa fa-suitcase"></i><span class="nav-label">商机管理</span><span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<?php if(checkPerByAction('business','index')): ?><li id="business-index"><a href="<?php echo U('business/index');?>"><span class="fa fa fa-suitcase"></span>&nbsp;&nbsp;商机</a></li><?php endif; ?>
							</ul>
						</li>
						<?php endif; ?>
						
						<?php if (in_array('contract',$show_module_list)): ?>
						<li>
							<a href="#" title="合同订单"><i class="fa fa-list-alt"></i><span class="nav-label">合同订单</span><span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<?php if(checkPerByAction('contract','index')): ?><li id="contract-index"><a href="<?php echo U('contract/index');?>"><span class="fa fa-list-alt"></span>&nbsp;&nbsp;合同</a></li><?php endif; ?>
								<?php if (C('PSS_STATUS')): ?>
								<?php if(checkPerByAction('sales','return_index')): ?><li id="sales-index"><a href="<?php echo U('sales/return_index');?>"><span class="fa fa-list-alt"></span>&nbsp;&nbsp;销售退货</a></li><?php endif; ?>
								<?php endif; ?>
							</ul>
						</li>
						<?php endif; ?>
						
						
						<?php if (in_array('finance',$show_module_list)): ?>
						<li>
							<a href="#" title="财务管理"><i class="fa fa-credit-card"></i><span class="nav-label">财务管理</span><span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<?php if(checkPerByAction('finance','index_receivables')): ?><li id="finance-index-receivables"><a href="<?php echo U('finance/index','t=receivables');?>"><span class="fa fa-credit-card"></span>&nbsp;&nbsp;应收款</a></li><?php endif; ?>
								<?php if(checkPerByAction('finance','index_payables')): ?><li id="finance-index-payables"><a href="<?php echo U('finance/index','t=payables');?>"><span class="fa fa-credit-card"></span>&nbsp;&nbsp;应付款</a></li><?php endif; ?>
								<?php if(checkPerByAction('finance','index_receivingorder')): ?><li id="finance-index-receivingorder"><a href="<?php echo U('finance/index','t=receivingorder');?>"><span class="fa fa-money"></span>&nbsp;&nbsp;回款单</a></li><?php endif; ?>
								<?php if(checkPerByAction('finance','index_paymentorder')): ?><li id="finance-index-paymentorder"><a href="<?php echo U('finance/index','t=paymentorder');?>"><span class="fa fa-money"></span>&nbsp;&nbsp;付款单</a></li><?php endif; ?>
								<?php if(checkPerByAction('invoice','index')): ?><li id="invoice-index"><a href="<?php echo U('invoice/index');?>"><span class="fa fa-file-text-o"></span>&nbsp;&nbsp;发票</a></li><?php endif; ?>
								<li class="divider"></li>
							</ul>
						</li>
						<?php endif; ?>
						
						
						<?php if (C('PSS_STATUS') && in_array('purchase', $show_module_list)): ?>
						<li>
							<a href="#" title="采购管理"><i class="fa fa-shopping-cart"></i><span class="nav-label">采购管理</span><span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<?php if(checkPerByAction('purchase','index')): ?><li id="purchase-index"><a href="<?php echo U('purchase/index');?>"><i class="fa fa-clipboard"></i>采购订单</a></li><?php endif; ?>
								<?php if(checkPerByAction('purchase','return_goods')): ?><li id="purchase-return_goods"><a href="<?php echo U('purchase/return_goods');?>"><i class="fa fa-reply"></i>采购退货</a></li><?php endif; ?>
								<!-- <?php if(checkPerByAction('purchase','analytics')): ?><li id="purchase-analytics"><a href="<?php echo U('purchase/analytics');?>"><i class="fa fa-pie-chart"></i>采购汇总</a></li><?php endif; ?> -->
								<?php if(checkPerByAction('supplier','index')): ?><li id="supplier-index"><a href="<?php echo U('supplier/index');?>"><i class="fa fa-user"></i>供应商管理</a></li><?php endif; ?>
							</ul>
						</li>
						<?php endif; ?>
						
						
						<?php if (C('PSS_STATUS') && in_array('stock', $show_module_list)): ?>
						<li>
							<a href="#" title="库存管理"><i class="fa fa-database"></i><span class="nav-label">库存管理</span><span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<?php if(checkPerByAction('stock','index')): ?><li id="stock-index"><a href="<?php echo U('stock/index');?>"><i class="fa fa-database"></i>产品库存</a></li><?php endif; ?>
								<?php if(checkPerByAction('stock','transfer')): ?><li id="stock-transfer"><a href="<?php echo U('stock/transfer');?>"><i class="fa fa-refresh"></i>库存调拨</a></li><?php endif; ?>
								<?php if(checkPerByAction('stock','instock')): ?><li id="stock-instock"><a href="<?php echo U('stock/instock');?>"><i class="fa fa-sign-in"></i>入库记录</a></li><?php endif; ?>
								<?php if(checkPerByAction('stock','outstock')): ?><li id="stock-outstock"><a href="<?php echo U('stock/outstock');?>"><i class="fa fa-sign-out"></i>出库记录</a></li><?php endif; ?>
								<?php if(checkPerByAction('warehouse','index')): ?><li id="warehouse-index"><a href="<?php echo U('warehouse/index');?>"><i class="fa fa-home"></i>仓库管理</a></li><?php endif; ?>
								<!-- <?php if(checkPerByAction('stock','inventory')): ?><li id="stock-inventory"><a href="<?php echo U('stock/inventory');?>"><i class="fa fa-check-square-o"></i>库存盘点</a></li><?php endif; ?> -->
								<!-- <?php if(checkPerByAction('stock','cost_adjust')): ?><li id="stock-cost_adjust"><a href="<?php echo U('stock/cost_adjust');?>"><i class="fa fa-refresh"></i>成本调整</a></li><?php endif; ?> -->
								<!-- <?php if(checkPerByAction('stock','stock_record')): ?><li id="stock-stock_record"><a href="<?php echo U('stock/stock_record');?>"><i class="fa fa-edit"></i>出入库明细</a></li><?php endif; ?> -->
								<!-- <?php if(checkPerByAction('stock','analytics')): ?><li id="stock-analytics"><a href="<?php echo U('stock/analytics');?>"><i class="fa fa-bar-chart-o"></i>产品收发汇总</a></li><?php endif; ?> -->
							</ul>
						</li>
						<?php endif; ?>
						
						<?php if (in_array('product',$show_module_list)): ?>
						<li>
							<a href="#" title="产品管理"><i class="fa fa-th-large"></i><span class="nav-label">产品管理</span><span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<?php if(checkPerByAction('product','index')): ?><li id="product-index"><a href="<?php echo U('product/index');?>"><i class="fa fa-th-large"></i>产品</a></li><?php endif; ?>
								<?php if(checkPerByAction('product_info','spec')): ?><li id="productinfo-index"><a href="<?php echo U('product_info/spec');?>"><i class="fa fa-list"></i>规格分类</a></li><?php endif; ?>
								<?php if (C('PSS_STATUS')): ?>
								<?php if(checkPerByAction('goods','sn_track')): ?><li id="goods-index"><a href="<?php echo U('goods/sn_track');?>"><i class="fa fa-paw"></i>序列号跟踪表</a></li><?php endif; ?>
								<?php endif; ?>
							</ul>
						</li>
						<?php endif; ?>
						
						<?php if (in_array('analytics', $show_module_list)): ?>
						<li class="analytics">
							<a href="#" title="数据分析"><i class="fa fa-area-chart"></i><span class="nav-label">数据分析</span><span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li id="analytics-index"><a href="<?php echo ($analytics_url); ?>"><span class="fa fa-area-chart"></span>&nbsp;&nbsp;数据分析</a></li>
							</ul>
						</li>
						<?php endif; ?>
						
						<?php if (in_array('office',$show_module_list)): ?>
						<li>
							<a href="#" title="办公"><i class="fa fa-desktop"></i><span class="nav-label">办公</span><span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<?php if(checkPerByAction('log','index')): ?><li id="log-index"><a href="<?php echo U('log/index');?>"><span class="fa fa-pencil-square"></span>&nbsp;&nbsp;工作日志</a></li><?php endif; ?>
								<?php if(checkPerByAction('examine','index')): ?><li id="examine-index"><a href="<?php echo U('examine/index');?>"><span class="fa fa-pencil-square"></span>&nbsp;&nbsp;审批</a></li><?php endif; ?>
								<?php if(checkPerByAction('knowledge','index')): ?><li id="knowledge-index"><a href="<?php echo U('knowledge/index');?>"><span class="fa fa-book"></span>&nbsp;&nbsp;知识</a></li><?php endif; ?>
								<?php if(checkPerByAction('announcement','index')): ?><li id="announcement-index"><a href="<?php echo U('announcement/index');?>"><span class="fa fa-volume-up"></span>&nbsp;&nbsp;公告</a></li><?php endif; ?>
								<?php if(checkPerByAction('sign','index')): ?><li id="sign-index"><a href="<?php echo U('sign/index');?>"><span class="fa fa-map-pin"></span>&nbsp;&nbsp;定位签到</a></li><?php endif; ?>
								<?php if(checkPerByAction('event','index')): ?><li id="event-index"><a href="<?php echo U('event/index');?>"><span class="fa fa-calendar"></span>&nbsp;&nbsp;日程</a></li><?php endif; ?>
								<?php if(checkPerByAction('task','index')): ?><li id="task-index"><a href="<?php echo U('task/index');?>"><span class="fa fa-tasks"></span>&nbsp;&nbsp;任务</a></li><?php endif; ?>
								<li id="kaoqin-index"><a href="<?php echo U('kaoqin/index');?>"><span class="fa fa-hand-pointer-o"></span>&nbsp;&nbsp;考勤月历</a></li>
								<li class="divider"></li>
							</ul>
						</li>
						<?php endif; ?>
						<?php if (in_array('contacts',$show_module_list)): ?>
						<li>
							<a href="#" title="通讯录"><i class="fa fa-phone-square"></i><span class="nav-label">通讯录</span><span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<?php if(checkPerByAction('user','contacts')): ?><li id="user-contacts"><a href="<?php echo U('user/contacts');?>"><span class="fa fa-list-alt"></span>&nbsp;&nbsp;通讯录</a></li><?php endif; ?>
							</ul>
						</li>
						<?php endif; ?>
						
						<?php if (in_array('marketing',$show_module_list)): ?>
						<li>
							<a href="#" title="营销"><i class="fa fa-envelope"></i><span class="nav-label">营销</span><span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<?php if (checkPerByAction('market', 'index')) : ?>
									<li id="market-index"><a href="<?php echo U('market/index');?>"><span class="fa fa-fa"></span>&nbsp;&nbsp;市场活动</a></li>
								<?php endif; ?>
								<?php if (checkPerByAction('setting', 'sendsms')) : ?>
									<li id="setting-sendsms"><a href="<?php echo U('setting/sendsms');?>"><span class="fa fa-comments-o"></span>&nbsp;&nbsp;<?php echo L('SEND_SMS');?></a></li>
								<?php endif; ?>
								<?php if (checkPerByAction('setting', 'smsrecord')) : ?>
									<li id="setting-smsrecord"><a href="<?php echo U('setting/smsrecord');?>"><span class="fa fa-envelope"></span>&nbsp;&nbsp;<?php echo L('SMS_RECORD');?></a></li>
								<?php endif; ?>
								<?php if (checkPerByAction('setting', 'sendemail')) : ?>
									<li id="setting-sendemail"><a href="<?php echo U('setting/sendemail');?>"><span class="fa fa-folder"></span>&nbsp;&nbsp;<?php echo L('SEND_EMAIL');?></a></li>
								<?php endif; ?>
							</ul>
						</li>
						<?php endif; ?>
						
						<?php if (in_array('setting',$show_module_list)): ?>
						<li>
							<a href="#" title="系统设置"><i class="fa fa-cog"></i><span class="nav-label">系统设置</span><span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li id="setting-index"><a href="<?php echo ($setting_url); ?>"><span class="fa fa-cog"></span>&nbsp;&nbsp;系统设置</a></li>
							</ul>
						</li>
						<?php endif; ?>
					</ul>
				</div>
	        </div>
	    </nav>
		<div id="page-wrapper" class="gray-bg" style="background:#e6e9f0">
    		<div class="row border-bottom white-bg" style="box-shadow: 0px 4px 13px -8px #5A5A5A;z-index: 102;">
    			<div class="navbar-header" style="height:67px;" >
    				
    				

    				
					<div class="abe-fl sys-name pdr_30">ASRCRM <span class="abe-ft28">系统</span></div>
					<div role="search" class="navbar-form-custom mtg_5" method="post" action="" style="width:275px;">
                        <div class="form-group">
                            <input type="text" placeholder="请输入您需要查找的名称或手机号码" class="form-control" name="top-search" id="top-search" onkeydown="if(event.keyCode==13)topsearch()">
                        </div>
                    </div>
    			</div>

    			<nav class="navbar nav-head navbar-default navbar-static-top" id="header-top" role="navigation" style="margin-bottom: 0">
	    			<ul class="nav navbar-top-links navbar-right" style="margin-right:0px;">
						<li class="dropdown" style="">
		                    <a class="dropdown-toggle count-info" id="todo_url"  data-toggle="dropdown" href="#" title="待办事项">
		                        <img src="__PUBLIC__/img/remain.png" alt="" /><div class="label label-info" style="background-color: #FA7252;" id="todo_num"></div>
		                    </a>
	                   		<ul class="dropdown-menu dropdown-alerts folder-list m-b-md" style="width:250px;">
	                   			<?php $contract_examine_role_ids = M('ContractExamine')->getField('role_id',true); ?>
	                   			<?php if (checkPerByAction('customer','index')): ?>
			            			<li style="border-bottom:1px dashed #e7eaec !important;"><a href="<?php echo U('customer/index','by=todaycontact');?>"><i class="fa fa-user"></i>&nbsp;&nbsp;今日需跟进客户<div class="label label-info pull-right" style="background-color: #FA7252;" id="header_follow_customer_num"></div></a></li>
			            		<?php endif; ?>
	                   			<?php if (checkPerByAction('contract','index')): ?>
									<li style="border-bottom:1px dashed #e7eaec !important;"><a href="<?php echo U('contract/index','by=dqcontact');?>"><i class="fa fa-user"></i>&nbsp;&nbsp;合同到期提醒<div class="label label-info pull-right" style="background-color: #FA7252;" id="header_dqcontact_num"></div></a></li>
								<?php endif; ?>
	                   			<?php if (checkPerByAction('contract','check') || in_array(session('role_id'),$contract_examine_role_ids)): ?>
			            			<li style="border-bottom:1px dashed #e7eaec !important;"><a href="<?php echo U('contract/index','contract_checked=1&by=all');?>"><i class="fa fa-list-alt"></i>&nbsp;&nbsp;待审核的合同<div class="label label-info pull-right" style="background-color: #FA7252;" id="header_check_contract_num"></div></a></li>
			            		<?php endif; ?>
			            		<?php if (checkPerByAction('finance','index_receivables')): ?>
			            			<li style="border-bottom:1px dashed #e7eaec !important;"><a href="<?php echo U('finance/index','t=receivables&r_status=1&by=me');?>"><i class="fa fa-money"></i>&nbsp;&nbsp;应收款提醒<div class="label label-info pull-right" style="background-color: #FA7252;" id="receivables_num"></div></a></li>
			            		<?php endif; ?>
								<?php if (checkPerByAction('finance','check')): ?>
			            			<li style="border-bottom:1px dashed #e7eaec !important;"><a href="<?php echo U('finance/index','t=receivingorder&status[value]=0&by=all');?>"><i class="fa fa-money"></i>&nbsp;&nbsp;待审核的回款<div class="label label-info pull-right" style="background-color: #FA7252;" id="header_receivables_num"></div></a></li>
			            		<?php endif; ?>
			            		<?php if (checkPerByAction('invoice','check')): ?>
			            			<li style="border-bottom:1px dashed #e7eaec !important;"><a href="<?php echo U('invoice/index','is_checked=0');?>"><i class="fa fa-bookmark"></i>&nbsp;&nbsp; 待审核的发票<div class="label label-info pull-right" style="background-color: #FA7252;" id="check_invoice_num"></div></a></li>
			            		<?php endif; ?>
			            		<?php if (checkPerByAction('examine','index')): ?>
			            			<li style="border-bottom:1px dashed #e7eaec !important;"><a href="<?php echo U('examine/index','by=me_examine&examining=1');?>"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;待处理的办公审批<div class="label label-info pull-right" style="background-color: #FA7252;" id="header_examine_num"></div></a></li>
								<?php endif; ?>
								
								<!-- 进销存相关待审核信息 -->
			            		<?php if (C('PSS_STATUS')): ?>
			            			<?php if (checkPerByAction('purchase','index')): ?>
			            				<li style="border-bottom:1px dashed #e7eaec !important;"><a href="<?php echo U('purchase/index','unchecked=1');?>"><i class="fa fa-clipboard"></i>&nbsp;&nbsp;待审核的采购单<div class="label label-info pull-right" style="background-color: #FA7252;" id="check_purchase_num"></div></a></li>
			            			<?php endif; ?>
			            			<?php if (checkPerByAction('purchase','return_goods')): ?>
			            				<li style="border-bottom:1px dashed #e7eaec !important;"><a href="<?php echo U('purchase/return_goods','unchecked=1');?>"><i class="fa fa-reply"></i>&nbsp;&nbsp;待审核的采购退货<div class="label label-info pull-right" style="background-color: #FA7252;" id="check_purchase_return_num"></div></a></li>
			            			<?php endif; ?>
			            			<?php if (checkPerByAction('sales','return_index')): ?>
			            				<li style="border-bottom:1px dashed #e7eaec !important;"><a href="<?php echo U('sales/return_index','unchecked=1');?>"><i class="fa fa-list-alt"></i>&nbsp;&nbsp;待审核的销售退货<div class="label label-info pull-right" style="background-color: #FA7252;" id="check_sales_return_num"></div></a></li>
			            			<?php endif; ?>
			            			<?php if (checkPerByAction('stock','transfer')): ?>
			            				<li style="border-bottom:1px dashed #e7eaec !important;"><a href="<?php echo U('stock/transfer','unchecked=1');?>"><i class="fa fa-refresh"></i>&nbsp;&nbsp;待审核的库存调拨<div class="label label-info pull-right" style="background-color: #FA7252;" id="check_transfer_num"></div></a></li>
			            			<?php endif; ?>
			            		<?php endif; ?>
	                   		</ul>
		                </li>
		                <li class="dropdown" style="">
		                    <a class="dropdown-toggle count-info" id="event_url" data-toggle="dropdown" href="#" title="今日日程">
		                        <img src="__PUBLIC__/img/event.png" alt="" /><div class="label label-warning" id="event_num"></div>
		                    </a>
	                   		<ul class="dropdown-menu dropdown-alerts">
			            		<li class="list-group" role="presentation" id="event_group" style="height:220px;display:none;">
	                        		<div class="full-height-scroll" id="event_list" data-height="220px" data-plugin="slimScroll" style="overflow: hidden; width: auto;">
	                                </div>
	                    		</li>
	                    		<li>
			                        <div class="text-center link-block">
			                            <a href="<?php echo U('event/index');?>">
			                                <strong>查看全部日程</strong>
			                                <i class="fa fa-angle-right"></i>
			                            </a>
			                        </div>
			                    </li>
			            		<li class="divider" style="height:0px;"></li>
	                   		</ul>
		                </li>
		                <li class="dropdown">
		                    <a class="dropdown-toggle count-info" id="message_url" data-toggle="dropdown" href="#" title="站内信">
		                        <img src="__PUBLIC__/img/bell.png" alt="" /><div class="label label-primary" id="message_num"></div>
		                    </a>
	                    	<ul class="dropdown-menu dropdown-alerts" style="width:365px;">
	                    		<li class="list-group" role="presentation" id="message_group" style="height:220px;display:none;">
	                        		<div class="full-height-scroll" id="message_list" data-height="220px" data-plugin="slimScroll" style="overflow: hidden; width: auto;">
	                        		</div>
	                        	</li>
	                    		<li>
			                        <div class="text-center link-block">
			                            <a href="<?php echo U('message/index');?>">
			                                <strong>站内信列表</strong>
			                                <i class="fa fa-angle-right"></i>
			                            </a>
			                        </div>
			                    </li>
			                    <li class="divider" style="height:0px;"></li>
	                    	</ul>
		                </li>
		                <li style="padding-left: 30px;" title="<?php echo ($_SESSION['full_name']); ?>(<?php echo ($_SESSION['role_name']); ?>)">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" style="padding: 2px;">
								<img alt="头像" style="height:38px;width:38px" class="img-circle" src="<?php echo headPathHandle($_SESSION['user_img']);?>" />
							</a>
							<ul class="dropdown-menu">
								<li><a href="<?php echo U('user/edit');?>">个人资料</a></li>
	                            <li><a href="<?php echo U('setting/lockscreen');?>">一键锁屏</a></li>
								<li class="divider"></li>
								<li><a class="logout" href="javascript:void(0);"><?php echo L('EXIT');?></a></li>
							</ul>
						</li>
		            </ul>
		        </nav>
    		</div>
			<div class="modal inmodal" id="Profile" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content animated bounceInRight">

					</div>
				</div>
			</div>
			<!-- 模态框 -->
			<div class="modal inmodal fade" id="Modal_login" tabindex="-1"  style=" overflow:auto; border:1px solid #000000;" role="dialog" >
			    <div class="modal-dialog modal-md" style="width:700px;">
			        <div class="modal-content" id="login_modal">

			        </div>
			    </div>
			</div>

			<div class="modal inmodal fade" id="Modal_anthorize" aria-hidden="true" role="dialog" tabindex="-1">
			    <div class="modal-dialog" style="width:500px;">
		        	<div class="modal-content" id="anthorize_modal">
			        	
					</div>
			    </div>
			</div>

<script type="text/javascript">
	/**
	 * 设置默认打开菜单效果
	 * 本来想用js session来实现，但是右键打开新窗口或新标签，以及从非菜单栏点击的链接的情况都没法处理
	 */ 
	$(function(){
		var module_name = "<?php echo strtolower(MODULE_NAME);?>";
		var action_name = "<?php echo strtolower(ACTION_NAME);?>";
		// by、content、t，历史遗留问题，需要特殊处理
		var by = "<?php echo ($_GET['by']); ?>";
		var content = "<?php echo ($_GET['content']); ?>";
		var t = "<?php echo ($_GET['t']); ?>";
		if (module_name == 'leads' && by == 'public') {
			// 线索池
			param = 'public';
		} else if (module_name == 'customer' && content == 'resource') {
			// 客户池
			param = 'resource';
		} else if (module_name == 'finance') {
			// 财务模块
			param = t;
		} else {
			param = '';
		}
		$.ajax({
			type:'get',
			url: "<?php echo U('system/set_open_menu');?>",
			data: {module_name:module_name,action_name:action_name,param:param},
			async: false,
			success:function(data){
				if (data.status == 1) {
					var menu_html_id = data.data;
					$("#"+menu_html_id).parent().parent().addClass('active');
					$("#"+menu_html_id).addClass('active');
					$("#"+menu_html_id).parent().addClass('in');
				}
			},
			dataType:'json'
		});
	});
	/* 设置默认打开菜单效果 */

	$(function(){
		/*记录菜单隐藏状态*/
		$(".navbar-minimalize").click(function(){
			var arr,reg = new RegExp("(^| )mini-navbar=([^;]*)(;|$)");
			arr = document.cookie.match(reg);
			if(arr){
				var nav_status = unescape(arr[2]) == 1 ? 0:1;
			}else{
				var nav_status = 1;
			}
			document.cookie = "mini-navbar="+nav_status;
		});

		/**
		* 左侧菜单栏，设置滚动条后会遮挡li的hover事件的div悬浮层，改成用js强制定位，并动态改变位置
		* @author lee
		**/
		$('#left_sidebar_div li').mouseover(function(){
			//侧边菜单栏如果是关闭状态，则动态修改效果
			if ($('body').hasClass('mini-navbar')) {
				//当悬浮层超出底部时，向上弹出
				var ul_offset_top = $(this).children('ul').offset().top; //悬浮层距顶部的距离
				var windows_scroll = $(window).scrollTop();  //滚动条滚动的距离
				var ul_height = $(this).children('ul').height();  //悬浮层自身的高度
				var window_height = $(window).height();  //浏览器窗口的高度

				var diff_val = window_height - (ul_offset_top - windows_scroll + ul_height);
				if (diff_val > 0) {
					var offset_top = $(this).offset().top - windows_scroll;  //浏览器窗口高度足够，正常向下展示			
				} else {
					var offset_top = $(this).offset().top - (ul_height - 31); //向上展示，31是误差凑的值，没有特殊意义
				}
				$(this).children('ul').css({'position':'fixed','top':offset_top,'left':'70px'});
			}
		});
		$('#left_sidebar_div li').mouseout(function(){
			//侧边菜单栏如果是关闭状态，则动态修改效果
			if ($('body').hasClass('mini-navbar')) {
				$(this).children('ul').css({'position':'inherit','top':'0px','left':'0px'});
			}
		});
	});

	$('[data-toggle="tooltip"]').tooltip({html:true});
	/*时间插件*/
	$('.date').datepicker({
		format: "yyyy-mm-dd",
		todayBtn: "linked",
		keyboardNavigation: false,
		forceParse: false,
		calendarWeeks: true,
		autoclose: true
	});

	$("#dialog-message-send").dialog({
	    autoOpen: false,
	    modal: true,
		width: 800,
		maxHeight: 600,
		position: ["center",100]
	});

	function salert(){
		var txt = "<?php if(is_array($alert["content"])): foreach($alert["content"] as $k=>$v): if(is_array($v)): foreach($v as $kk=>$vv): echo ($vv); endforeach; endif; endforeach; endif; ?>";
		if(txt != ''){
			swal(txt, "", "<?php echo ($alert['type'][0]); ?>")
		}
	}

	/*head 特效*/
	$('.nav-head .navbar-left li').mouseover(function(){
		$(this).find('a span').css("color", '#ffb173');
	});

	$('.nav-head .navbar-left li').mouseout(function(){
		$(this).find('a span').css("color", '#e6e9f2');
	});

	// 快捷搜索
	function topsearch() {
		var search = $('#top-search').val();
		if (search) {
			layer.open({
				type: 2,
				title: '快捷搜索',
				shadeClose: true,
				shade: false,
				maxmin: true, //开启最大化最小化按钮
				resize: false,
				area: ['850px', '600px'],
				content: './index.php?m=system&a=topsearch&search='+search
			});
		} else {
			alert_crm('查询内容不能为空！');
		}
	}

</script>

<link href="__PUBLIC__/css/litebox.css" rel="stylesheet" type="text/css">
<script src="__PUBLIC__/js/PCASClass.js" type="text/javascript"></script>
<!-- nice-scroll -->
<script src="__PUBLIC__/style/js/plugins/nice-scroll/jquery.nicescroll.min.js" type="text/javascript"></script>
<script type="text/javascript" src="__PUBLIC__/style/js/TableFreeze.js"></script>
<script src="__PUBLIC__/js/pdcrm_more.js" type="text/javascript"></script>
<style>
	body{
		overflow-y: hidden;
	}
	.option{padding-left:-30px;}
	#oDivL_tab_Test3{background-color: #fff;min-width: 300px;}
	#oTableLH_tab_Test3{min-width:300px;}
	.table{max-width: none;}
	.scene_active{background-color: #e6e9f0;}
	#tab_Test3 >tr {height:45px;line-height:45px;}
	.radio label::before{border:none;}
</style>
<script>
$(function(){
	var scroll_width = 10;
	// var oTableLH_tab_Test3 = 38;
	$("#table_div").height(window.innerHeight-$("#table_div").offset().top-$("#tfoot_div").height()-parseInt($("#table_container").css("padding-bottom").replace("px",""))-10);
	$(window).resize(function(){
		$("#table_div").height(window.innerHeight-$("#table_div").offset().top-$("#tfoot_div").height()-parseInt($("#table_container").css("padding-bottom").replace("px",""))-10);
		$("#oDivL_tab_Test3").height($("#table_div").height()-scroll_width-1).width($("#oTableLH_tab_Test3").width());
		$("#oDivH_tab_Test3").height($("#oTableLH_tab_Test3").height()).width($("#table_div").width()-scroll_width);
	})
	$(".nicescroll").niceScroll({
		cursorcolor: "#999",//#CC0071 光标颜色 
	    cursoropacitymax: 0.4, //改变不透明度非常光标处于活动状态（scrollabar“可见”状态），范围从1到0 
	    touchbehavior: false, //使光标拖动滚动像在台式电脑触摸设备 
	    cursorwidth: scroll_width+"px", //像素光标的宽度 
	    cursorborder: "0", //     游标边框css定义 
	    cursorborderradius: "3px",//以像素为光标边界半径 
	    autohidemode: false, //是否隐藏滚动条 
	    zindex:100,
	    background:"#F3F3F5",//滚动条背景色
	});
	if ("<?php echo ($_GET['by']); ?>") {
		$("#filter_ul").prev().html($("#filter_ul>li>a.active").text()+'<span class="fa fa-angle-down small_fa"></span>');
	}
	var get_content = "<?php echo ($_GET['content']); ?>";
	if (get_content == 'resource') {
		$("#tab_Test3").FrozenTable(1,0,3);
	} else {
		$("#tab_Test3").FrozenTable(1,0,4);
	}
	$("#oDivL_tab_Test3").height($("#table_div").height()-scroll_width-1).width($("#oTableLH_tab_Test3").width()).css({'zIndex':9});
	$("#oDivL_tab_Test3").css({"background-color":"#fff","border-right":"1px solid #e7eaec"});
	$("#oTableLH_tab_Test3").css({"border-right":"1px solid #e7eaec"});
	$("#oDivH_tab_Test3").height($("#oTableLH_tab_Test3").height()).width($("#table_div").width()-scroll_width).css({'zIndex':9});
})
</script>
<div class="wrapper wrapper-content">
	<!--<?php dump($alert); ?>-->
<?php if(!empty($alert['content'])): if(is_array($alert['content'])): foreach($alert['content'] as $k=>$v): if(is_array($v)): foreach($v as $kk=>$vv): ?><input type="hidden" class="alert_tishi" rel="<?php echo ($k); ?>" value="<?php echo ($vv); ?>"><?php endforeach; endif; endforeach; endif; ?>
<input type="hidden" id="alert_defaultinfo_name" value="<?php echo C('defaultinfo.name');?>" />
<script>
    $(document).ready(function() {
        var tishi = $('.alert_tishi').val();
        var is_success = $('.alert_tishi').attr('rel');
        var alert_defaultinfo_name = $('#alert_defaultinfo_name').val();
        setTimeout(function() {
            if(is_success == 'error'){
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    showMethod: 'slideDown',
                    timeOut: 4000,
                    fadeIn: 7000
                };
                toastr.error(alert_defaultinfo_name,tishi);
            }else{
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    showMethod: 'slideDown',
                    timeOut: 2000
                };
                toastr.success(alert_defaultinfo_name,tishi);
            }
        }, 800);
    });
</script><?php endif; ?>
    <div class="row">
        <div class="col-md-12">
            <div class="ibox float-e-margins">
				<div class="title-bar" style="position: relative;z-index: 99;">
					<div class="row  clearfix" id="title-hide" style="display:none;">
						<ul class="breadcrum pull-left">
							<li>已选中&nbsp;<span id="icheck_num"></span>&nbsp;项</li>
							<?php if($_GET['content']!= 'resource'): ?><li class="single_btn"><a href="javascript:void(0)" id="log_customer"><i class="fa fa-file-text"></i>&nbsp;添加沟通日志</a></li>
								<li><a href="javascript:void(0)" id="remind"><i class="fa fa-bell-o"></i>&nbsp;提醒</a></li><?php endif; ?>
							<?php if($_GET['content']== 'resource'): ?><li><a id="batch_receive" href="javascript:void(0)"><i class="fa fa-reply"></i>&nbsp;领取</a></li>
								<li><a id="batch_assign" href="javascript:void(0)"><i class="fa fa-share"></i>&nbsp;分配</a></li>
							<?php else: ?>
								<?php if($_GET['by']!= 'share'): ?><li><a id="remove" href="javascript:void(0)"><i class="fa fa-users"></i>&nbsp;放入客户池</a></li><?php endif; endif; ?>
							<?php if($_GET['content']!= 'resource'): if($_GET['by']!= 'share' && checkPerByAction('customer','edit')): ?><li class="single_btn"><a href="javascript:void(0)" id="edit_customer"><i class="fa fa-pencil"></i>&nbsp;编辑</a></li><?php endif; ?>
								<li class="single_btn business_btn"><a href="javascript:void(0)" id="to_top"><i class="fa fa-arrow-up"></i><span id="to_top_span">&nbsp;置顶</span></a></li><?php endif; ?>
							<?php if($_GET['content']!= 'resource' and $_GET['by']!= 'deleted' and $_GET['by']!= 'share'): if(checkPerByAction('customer','transfer_edit')): ?><li><a id="change" href="javascript:void(0)"><i class="fa fa-exchange"></i>&nbsp;转移</a></li><?php endif; endif; ?>
							<?php if($_GET['content']!= 'resource' and $_GET['by']!= 'deleted' and $_GET['by']!= 'share'): if(checkPerByAction('customer','share')): ?><li><a id="share" href="javascript:void(0)"><i class="fa fa-share"></i>&nbsp;分享</a></li><?php endif; endif; ?>
							<?php if($_GET['by']== 'myshare'): if(checkPerByAction('customer','close_share')): ?><li><a id="close_share" href="javascript:void(0)"><i class="fa fa-mail-reply"></i>&nbsp;取消分享</a></li><?php endif; endif; ?>
							<?php if($_GET['by']!= 'share'): if(checkPerByAction('customer','excelexport')): ?><li><a href="javascript:void(0);" class="link excelExport"><i class="fa fa-download"></i>&nbsp;导出</a></li><?php endif; endif; ?>
							<?php if($_GET['by']!= 'share'): if((checkPerByAction('customer','delete') && $_GET['content'] != 'resource') || (checkPerByAction('customer','del_resource') && $_GET['content'] == 'resource') || session('?admin')): ?><li>
										<a class="sendsms" where="ids" href="javascript:void(0)">
											<i class="fa fa-comments-o"></i>&nbsp;发送短信
										</a>
									</li><?php endif; endif; ?>
							<?php if($_GET['by']!= 'share'): if((checkPerByAction('customer','delete') && $_GET['content'] != 'resource') || (checkPerByAction('customer','del_resource') && $_GET['content'] == 'resource') || session('?admin')): ?><li><a id="delete" href="javascript:void(0)"><i class="fa fa-times"></i>&nbsp;删除</a></li><?php endif; endif; ?>
							<li class="last_li"><big><a class="fa fa-times pull-right" id="back-show"></a></big></li>
						</ul>
					</div>
					<div class="row " id="title-show">
						<ul class="nav pull-left" style="margin:2px 0 0 15px;">
							<?php if($_GET['content'] != 'resource'): ?><a href="<?php echo U('Customer/add');?>" class="btn btn-primary btn-sm pull-left" style="margin-right:8px">
									<i class="fa fa-plus-circle"></i>&nbsp; <?php echo L('CREATE_NEW_CUSTOMER');?>
								</a><?php endif; ?>
						</ul>
						<div class="pull-right" style="margin-right:20px;">
							<form class="form-inline pull-left" id="customer_search" action="" method="get">
								<ul class="breadcrum pull-left" style="margin-bottom: 0px;padding-right:0px;">
									<?php if($_GET['content'] != 'resource'): ?><li>
											<div class="input-group">
												<a href="#" class="dropdown-toggle" data-toggle="dropdown" style="padding: 2px;">
													<div class="input-group" style="margin-right: 10px;margin-bottom: 5px;color:#667B8F;"><?php echo ($scene_name); ?> <span class="caret"></span></div>
												</a>
												<ul class="dropdown-menu dropdown-alerts folder-list m-b-md" style="width:150px;">
							                   		<li class="list-group" role="presentation" id="event_group_1" style="height:160px;margin-bottom: 0px;">
						                        		<div class="full-height-scroll" id="scene_list" data-height="220px" data-plugin="slimScroll" style="overflow: hidden; width: auto;">
															<?php if(is_array($scene_list)): $i = 0; $__LIST__ = $scene_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['type'] == 1): ?><a href="<?php echo U('customer/index','by='.$vo['by']);?>" style="width:100%;color:#676a6c;padding:5px !important;float:left;" <?php if($_GET['by'] == $vo['by']): ?>class="scene_active"<?php endif; ?> title="<?php echo ($vo['name']); ?>">
																<?php else: ?>
																	<a href="<?php echo U('customer/index','scene_id='.$vo['id']);?>" style="width:100%;color:#676a6c;padding:5px !important;float:left;" <?php if($_GET['scene_id'] == $vo['id']): ?>class="scene_active"<?php endif; ?> title="<?php echo ($vo['name']); ?>"><?php endif; ?>
																	<div style="padding:0 10px;">
										                            	<span class="pull-left" style="line-height:26px;">&nbsp;&nbsp;<?php echo ($vo['cut_name']); ?></span>
										                            </div>
												                </a><?php endforeach; endif; else: echo "" ;endif; ?>
					                                    </div>
					                        		</li>
					                                <li style="float:left;width:100%;">
					                                	<a href="javascript:void(0);" id="add_scene" style="width:100%;color:#676a6c;padding:5px !important;float:left;">
					                                		<div style="padding:0 10px;">
								                            	<span class="pull-left" style="line-height:26px;"><i class="fa fa-plus-circle"></i>&nbsp;新建场景</span>
								                            </div>
								                        </a>
					                                </li>
					                                <li style="float:left;width:100%;">
					                                	<a href="javascript:void(0);" id="setting_scene" style="width:100%;color:#676a6c;padding:5px !important;float:left;">
					                                		<div style="padding:0 10px;">
								                            	<span class="pull-left" style="line-height:26px;"><i class="fa fa-cog"></i>&nbsp;管理</span>
								                            </div>
								                        </a>
					                                </li>
						                   		</ul>
											</div>
										</li><?php endif; ?>
									<li>
										搜索：
										<div class="input-group">
											<input type="hidden" name="m" value="customer"/>
											<input type="hidden" name="a" value="index"/>
											<input type="hidden" name="field" value="name"/>
											<input type="hidden" name="condition" value="contains"/>
											<input type="hidden" name="content" value="<?php echo ($_GET['content']); ?>"/>
											<input type="hidden" name="by" value="<?php echo ($by); ?>"/>
											<input type="hidden" name="scene_id" value="<?php echo ($_GET['scene_id']); ?>"/>

											<!-- 导出相关 -->
											<input type="hidden" name="this_page" value="<?php echo ($this_page); ?>" />
											<input type="hidden" name="act" id="act_field" value="index"/>
											<input type="hidden" name="daochu" id="daochu_field"/>
											<input type="hidden" name="selectexcelxport" id="selectexcelxport_field"/>
											<input type="hidden" name="current_page" id="current_page_field" value=""/>
											<input type="hidden" name="export_limit" id="export_limit_field" value=""/>
											<!-- 导出相关 -->

											<input id="short_search" type="text" style="width:180px;" placeholder="客户名/联系人/联系电话" onkeydown='if(event.keyCode==13) {$("#short_search_btn").trigger("click");return false;}' class="form-control input-sm" name="search" <?php if($_GET['field'] == 'name'): ?>value="<?php echo ($_GET['search']); ?>"<?php endif; ?>/>
											<span class="input-group-btn">
												<button class="btn btn-default btn-search" id="short_search_btn" type="submit"><i class="fa fa-search"></i></button>
											</span>
										</div>
										&nbsp;&nbsp;<a title="高级搜索" href="javascript:void(0)" id="search_type" class="btn btn-white btn-bitbucket"><i class="fa fa-filter" style="color: #D8E3EF;"></i></a>
									</li>
								</ul>
							</form>
							<div class="nav navbar-top-links navbar-left" style="margin-right:15px;">
								<div class="dropdown" style="">
				                    <a title="排序" href="javascript:void(0)" data-toggle="dropdown" class="btn btn-white btn-bitbucket dropdown-toggle" style="margin-left:-4px;"><i class="fa fa-sort-amount-asc" style="color: #D8E3EF;"></i></a>
			                   		<ul class="dropdown-menu dropdown-menu-left" style="width:150px;left:-100px;" id='dropdown_order'>
					            		<li class="list-group" role="presentation" style="height:150px;" >
			                        		<div class="full-height-scroll" data-height="150px" data-plugin="slimScroll" style="overflow: hidden; width: auto;margin-top:10px;" >
			                        			<div class="link-block" style="margin-left:15px;">
			                        				<div class="radio radio-info radio-inline">
			                                            <input type="radio" class="save_order" name="order_field" id="create_time_order" value="create_time" <?php if($_GET['order_field'] == 'create_time'): ?>checked<?php endif; ?> >
			                                            <label for="create_time_order">
			                                                创建时间
			                                            </label>
			                                        </div>
						                        </div>
						                        <div class="link-block" style="margin-left:15px;">
						                            <div class="radio radio-info radio-inline">
			                                            <input type="radio" class="save_order" name="order_field" id="update_time_order" value="update_time" <?php if($_GET['order_field'] == 'update_time' || $_GET['order_field'] == ''): ?>checked<?php endif; ?>>
			                                            <label for="update_time_order">
			                                                修改时间
			                                            </label>
			                                        </div>
						                        </div>
			                        			<?php if(is_array($order_fields)): $i = 0; $__LIST__ = $order_fields;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="link-block" style="margin-left:15px;">
							                            <div class="radio radio-info radio-inline">
				                                            <input type="radio" class="save_order" name="order_field" id="<?php echo ($vo['field']); ?>_order" value="<?php echo ($vo['field']); ?>" <?php if($_GET['order_field'] == $vo['field']): ?>checked<?php endif; ?>>
				                                            <label for="<?php echo ($vo['field']); ?>_order">
				                                                <?php echo ($vo['name']); ?>
				                                            </label>
				                                        </div>
							                        </div><?php endforeach; endif; else: echo "" ;endif; ?>
		                                    </div>
		                        		</li>
		                        		<li class="divider" style="height:1px;"></li>
		                        		<li>
					                        <div class="link-block" style="margin-left:15px;">
					                            <div class="radio radio-info radio-inline">
		                                            <input type="radio" class="save_order" name="order_type" id="asc_order" value="asc" <?php if($_GET['order_type'] == 'asc'): ?>checked<?php endif; ?>>
		                                            <label for="asc_order">
		                                                正序
		                                            </label>
		                                        </div>
					                        </div>
					                        <div class="link-block" style="margin-left:15px;">
					                            <div class="radio radio-info radio-inline">
		                                            <input type="radio" class="save_order" name="order_type" id="desc_order" value="desc" <?php if($_GET['order_type'] == 'desc' || $_GET['order_type'] == ''): ?>checked<?php endif; ?>>
		                                            <label for="desc_order">
		                                                倒序
		                                            </label>
		                                        </div>
					                        </div>
					                        <div class="link-block" style="margin-left:15px;">
					                            <div class="radio radio-info radio-inline">
		                                            <input type="radio" class="save_order" name="order_type" id="cancel_order" value="cancel_order" >
		                                            <label for="cancel_order">
		                                                取消排序
		                                            </label>
		                                        </div>
					                        </div>
					                    </li>
			                   		</ul>
				                </div>
				            </div>
							<div class="btn-group pull-left" style="margin-right: 10px;">
	                            <button data-toggle="dropdown" class="btn btn-primary dropdown-toggle" aria-expanded="false">操作 <span class="caret"></span>
	                            </button>
	                            <ul class="dropdown-menu">
	                                <?php if(checkPerByAction('customer','excelimport')): ?><li><a id="import_excel" class="link" href="javascript:void(0);"><i class="fa fa-upload"></i>&nbsp;导入</a></li><?php endif; ?>
									<?php if(checkPerByAction('customer','excelexport')): ?><li><a href="javascript:void(0);" class="link excelExport"><i class="fa fa-download"></i>&nbsp;导出</a></li><?php endif; ?>
									<?php if(checkPerByAction('customer','sendsms')): ?><li><a href="javascript:void(0);" class="link sendsms"><i class="fa fa-comments-o"></i>&nbsp;发送短信</a></li><?php endif; ?>
	                            </ul>
	                        </div>
						</div>
					</div>
				</div>
				<div class="ibox-content clearfix" id="table_container" style="z-index: 1;">
					<form id="form1" action="" method="post" style="position:relative;">
						<input type="hidden" name="owner_role" id="hidden_owner_id" value="0"/>
						<input type="hidden" name="message_alert" id="hidden_message" value="0"/>
						<input type="hidden" name="sms_alert" id="hidden_sms" value="0"/>
						<input type="hidden" name="email_alert" id="hidden_email" value="0"/>
						<input type="hidden" name="operating_type" id="operating_type" value=""/>
						<div id="table_div" class="nicescroll">
						<table class="table table-hover table-striped table_thead_fixed" id="tab_Test3">
							<?php if($customerlist == null): ?><div style="background-color:#fff;"><div style="text-align:center;color:#E4E4E4;font-size:22px;font-weight:700;padding:15px 0px;">
	<img src="__PUBLIC__/img/exclamation_mark.png" style="margin-top:-3px;">&nbsp;&nbsp;暂无数据
</div></div>
							<?php else: ?>
								<tr id="childNodes_num" class="tabTh">
									<td style="width:30px;padding-left: 20px">
										<div class="checkbox checkbox-primary">
											<input type="checkbox" class="check_all"/>
											<label for=""></label>
										</div>
									</td>
									<?php if($_GET['content']!= 'resource'): ?><td style="width: 26px;">&nbsp;</td><?php endif; ?>
									<?php if(is_array($name_field_array)): $i = 0; $__LIST__ = $name_field_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['field'] == 'name'): ?><td style="min-width:300px;"><?php echo ($vo["name"]); ?></td>
										<?php else: ?>
											<td ><?php echo ($vo["name"]); ?></td><?php endif; endforeach; endif; else: echo "" ;endif; ?>
									<td style="width:30px;border-right:1px solid #e7eaec;"></td>
									<?php if($cn_is_show == 1): ?><td>客户联系人</td><?php endif; ?>
									<?php if($ct_is_show == 1): ?><td>联系电话</td><?php endif; ?>
									<?php if(is_array($field_array)): $i = 0; $__LIST__ = $field_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['field'] != 'name'): ?><td><?php echo ($vo["name"]); ?></td><?php endif; endforeach; endif; else: echo "" ;endif; ?>
									<td>创建人</td>
									<td>创建时间</td>
									<td>更新时间</td>
									<?php if($openrecycle == 2){ ?>
										<?php if($_GET['asc_order'] == 'update_time' and $_GET['content']!= 'resource' ): ?><td><a href="<?php echo U('customer/index','desc_order=update_time&'.$parameter);?>"><?php echo L('FROM_THE_DUE_DAY');?>&nbsp;<span class="fa fa-caret-up"></span></a></td>
										<?php elseif($_GET['desc_order'] == 'update_time' and $_GET['content']!= 'resource' ): ?>
											<td><a href="<?php echo U('customer/index','asc_order=update_time&'.$parameter);?>"><?php echo L('FROM_THE_DUE_DAY');?>&nbsp;<span class="fa fa-caret-down"></span></a></td>
										<?php elseif($_GET['content']!= 'resource'): ?>
											<td><a href="<?php echo U('customer/index','desc_order=update_time&'.$parameter);?>"><?php echo L('FROM_THE_DUE_DAY');?></a></td><?php endif; ?>
									<?php } ?>
									<?php if($_GET['content']!= 'resource'): ?><td colspan="2" style="min-width: 80px;">快捷操作</td><?php endif; ?>
								</tr>
								<?php if(is_array($customerlist)): $i = 0; $__LIST__ = $customerlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="controls_tr" rel="<?php echo ($vo['customer_id']); ?>">
										<td style="width: 30px;padding-left: 20px;">
											<div class="checkbox checkbox-primary">
												<input name="customer_id[]" class="check_list" type="checkbox" value="<?php echo ($vo["customer_id"]); ?>" rel="<?php echo ($vo['business_id']); ?>" rel1="<?php echo ($vo['set_top']); ?>" rel2="<?php echo ($vo['remind_time']); ?>" />
												<label for=""></label>
											</div>
										</td>
										<?php if($_GET['content']!= 'resource'): ?><td>
												<a href="javascript:void(0);" class="remind_view" rel="<?php echo ($vo['customer_id']); ?>">
													<span class="fa fa-bell-o <?php echo ($vo['remind_time'] > time() ? '':'hide'); ?>" id="remind_view_<?php echo ($vo['customer_id']); ?>" style="font-size:16px;color:orange"></span>
												</a>
											</td><?php endif; ?>
										<?php if(is_array($name_field_array)): $i = 0; $__LIST__ = $name_field_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><td style="min-width:100px;">
												<?php
 $call_setting = C('CALL_SETTING'); $call_status = M('User')->where(array('role_id'=>session('role_id')))->getField('call_status'); ?>
												<?php if ($call_setting['CENTER'] == 1 && $call_status == 1): ?>
													<a href="javascript:void(0);" class="tel_list" title="点击呼叫该客户联系人" phone="<?php echo ($vo["contacts_telephone"]); ?>" rel="<?php echo ($vo['customer_id']); ?>" model="customer" style="font-size: 18px;" >
														<i class="fa fa-phone"></i>
													</a>&nbsp;&nbsp;
												<?php endif; ?>
												<?php if($_GET['content']!= 'resource'): if($vo['set_top'] == 1): ?><a href="<?php echo U('customer/view', 'id='.$vo['customer_id']);?>" title="<?php echo ($vo['custome_title_name']); ?>" target="_blank" style="border-left: 5px solid #ffb07b;padding-left: 5px;line-height: 24px;" >
													<?php else: ?>
														<a href="<?php echo U('customer/view', 'id='.$vo['customer_id']);?>" title="<?php echo ($vo['custome_title_name']); ?>" target="_blank" style="padding-left: 5px;line-height: 24px;" ><?php endif; ?>
												<?php else: ?>
													<a href="<?php echo U('customer/view', 'content=resource&id='.$vo['customer_id']);?>" title="<?php echo ($vo['custome_title_name']); ?>" target="_blank"><?php endif; ?>
													<span style="max-width: 300px; overflow: hidden;white-space: nowrap;text-overflow: ellipsis;">
														<?php echo ($vo['name']); ?>
													</span>
												</a>
											</td><?php endforeach; endif; else: echo "" ;endif; ?>
										<?php if($_GET['content']!= 'resource'): ?><td style="width:30px;border-right:1px solid #e7eaec;">
												<a class="log_customer" href="javascript:void(0);" rel="<?php echo ($vo['customer_id']); ?>">
													<i class="fa fa-toggle-right" id="fa_log_<?php echo ($vo['customer_id']); ?>" title="沟通日志" style="float:right;color:#ccc;"></i></a>
											</td>
										<?php else: ?>
											<td style="width:30px;border-right:1px solid #e7eaec;">
												<a class="log_history" href="javascript:void(0);" rel="<?php echo ($vo['customer_id']); ?>"><i class="fa fa-list" title="跟进记录" style="float:right;color:#ccc;"></i></a>
											</td><?php endif; ?>
										<?php if($cn_is_show == 1): ?><td><a href="<?php echo U('contacts/view','id='.$vo['contacts_id']);?>"><?php echo ($vo['contacts_name']); ?></a></td><?php endif; ?>
										<?php if($ct_is_show == 1): ?><td><?php echo ($vo['contacts_telephone']); ?></td><?php endif; ?>
										<?php if(is_array($field_array)): $i = 0; $__LIST__ = $field_array;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; if($v['field'] != 'name'): ?><td>
													<span style="color:#<?php echo ($v['color']); ?>">
														<?php if($v['form_type'] == 'datetime'): if($vo[$v['field']]): echo (date('Y-m-d H:i',$vo[$v['field']])); endif; ?>
														<?php elseif($v['field'] == 'customer_owner_id'): ?>
															<a class="role_info" rel="<?php echo ($vo['owner_role_id']); ?>" href="javascript:void(0)"><?php echo ($vo['owner_role_name']); ?></a>
														<?php elseif($v['field'] == 'grade'): ?>
															<?php $start = $vo['grade']+1 <= 6 ? $vo['grade']+1 : 0; $end = 6-$vo['grade'] < 0 ? 6 : 6-$vo['grade']; ?>
														    <span style="cursor:pointer;color:#D0D0D0;">
														        <?php $__FOR_START_14035__=1;$__FOR_END_14035__=$start;for($i=$__FOR_START_14035__;$i < $__FOR_END_14035__;$i+=1){ ?><i class="fa fa-star star-orange"></i>&nbsp;<?php } ?>
														        <?php $__FOR_START_21008__=1;$__FOR_END_21008__=$end;for($i=$__FOR_START_21008__;$i < $__FOR_END_21008__;$i+=1){ ?><i class="fa fa-star"></i>&nbsp;<?php } ?>
														    </span>
														<?php else: ?>
														<?php echo ($vo[$v['field']]); endif; ?>
													</span>
												</td><?php endif; endforeach; endif; else: echo "" ;endif; ?>
										<td>
											<a class="role_info" rel="<?php echo ($vo['creator_role_id']); ?>" href="javascript:void(0)"><?php echo ($vo['create_role_name']); ?></a>
										</td>
										<td><?php echo (date('Y-m-d',$vo['create_time'])); ?></td>
										<td><?php echo (date('Y-m-d',$vo['update_time'])); ?></td>
										<?php if($_GET['content']!= 'resource'): if($openrecycle == 2): ?><td>
													<?php if(checkPerByAction('customer','customerlock')){ ?>
														<a style="margin-left:0px;" href="<?php echo U('customer/customerlock','customer_id='.$vo['customer_id']);?>">
														 	<?php if($vo['is_locked']): ?><img style="width:19px;" title="<?php echo L('UNLOCK_TITLE');?>" src="__PUBLIC__/img/locking.png"/>
														 	<?php else: ?>
														 		<img title="<?php echo L('LOCK_TITLE');?>" style="width:19px;" src="__PUBLIC__/img/unlocking.png"/><?php endif; ?>
														</a>
													<?php } ?>
													<?php if(!$vo['is_locked']): ?><font color="#7486A8" title="<?php echo ($vo['days']); ?>天未跟进或<?php echo ($vo['c_days']); ?>天未签合同，将自动回归客户池"><?php echo ($vo['days']); ?>/<?php echo ($vo['c_days']); ?></font>
													<?php else: ?>
														<font color="#7486A8">已锁定</font><?php endif; ?>
												</td><?php endif; ?>
											<td class="detail-right" style="width:30px;">
												<a data-toggle="tooltip" data-placement="top" <?php if($vo['set_top'] == 1): ?>data-original-title="取消置顶" <?php else: ?>data-original-title="置顶"<?php endif; ?> href="<?php echo U('customer/settop','module=customer&module_id='.$vo['customer_id']);?>">
													<i class="fa fa-x fa-thumb-tack" style="font-size:16px;"></i>
												</a>
											</td>
											<td class="detail-right" style="width:30px;">
												<a data-toggle="tooltip" data-placement="top" class="addproduct" data-original-title="添加商机" rel="<?php echo ($vo['customer_id']); ?>">
													<i class="fa fa-cube" style="font-size:16px;"></i>
												</a>
											</td><?php endif; ?>
									</tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
						</table>
						</div>
						<div id="tfoot_div" class="clearfix">
							<div class="clearfix" id="tfoot_page">
								<?php if($fields_search || $_GET['field']): ?><span class="pull-left" style="margin-left:25px;margin-top:10px;">本次搜索结果<span style="color:#F8AC59"> <?php echo ($count); ?> </span>条数据<a href="<?php echo U('customer/index');?>" class="btn" style="background:#fff;border:1px solid #ccc;margin-left:10px;color:#999;" id="clearnumber">清除搜索条件</a></span><?php endif; ?>
								<?php echo ($page); ?><div class="pull-right" style="width:auto;">
	<select style="width:auto;display:inline-block;" id="listrows" name="listrows" rel="<?php echo ($listrows); ?>" class="form-control input-sm">
		<option value="10">10</option>
		<option value="15" checked="true">15</option>
		<option value="20">20</option>
		<option value="30">30</option>
		<option value="40">40</option>
		<option value="50">50</option>
		<option value="60">60</option>
		<option value="70">70</option>
		<option value="80">80</option>
		<option value="90">90</option>
		<option value="100">100</option>
	</select>
</div>
<script type="text/javascript">
function changeURLArg(url,arg,arg_val){ 
	var pattern=arg+'=([^&]*)'; 
	var replaceText=arg+'='+arg_val; 
	if(url.match(pattern)){ 
	var tmp='/('+ arg+'=)([^&]*)/gi'; 
	        tmp=url.replace(eval(tmp),replaceText); 
	return tmp; 
	    }else{ 
	if(url.match('[?]')){ 
	return url+'&'+replaceText; 
	        }else{ 
	return url+'?'+replaceText; 
	        } 
	    } 
	return url+'\n'+arg+'\n'+arg_val; 
} 
var list_rows = $("#listrows").attr('rel');
$("#listrows").val(list_rows);
$("#listrows").change(function(){
	var every_listrows = $(this).val();
	var this_url = window.location.search;
	if(this_url.indexOf("listrows") > 0){
		window.location = changeURLArg(this_url,'listrows',every_listrows);
	}else{
		window.location = this_url+"&listrows="+every_listrows;
	}
});
</script>
							</div>
						</div>
					</form>
				</div>
            </div>
        </div>
    </div>
</div>
<div style="display:none" id="dialog-import" title="<?php echo L('IMPORT_DATE');?>">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<div style="display:none" id="dialog-role-info" title="<?php echo L('EMPLOYEE_INFORMATION');?>">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<div style="display:none" id="dialog-fenpei" title="<?php echo L('DISTRIBUTION_OF_CUSTOMERS');?>">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<div style="display:none" id="dialog-assign" title="<?php echo L('DISTRIBUTION_OF_CUSTOMERS');?>">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<div style="display:none" id="dialog-log" title="添加跟进记录">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<div style="display:none" id="dialog-remind" title="提醒">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<div style="display:none" id="dialog-remind_view" title="提醒内容">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<div style="display:none" id="dialog-advance" title="阶段变更">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<div style="display:none;" id="dialog-map" title="<?php echo L('MAP');?>">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<div style="display:none" id="dialog-transform" title="客户转移">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<div style="display:none" id="dialog-sendsms" title="发送短信">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<!-- <script type="text/javascript" src="http://api.map.baidu.com/getscript?v=2.0&ak=Z0Fo0ib1GUgWlylCWeLvQh2U"></script> -->

<div style="display:none" id="dialog-field-search" title="高级搜索">
	<form class="form-inline" id="searchForm" action="" method="get">
		<div id="search_add" style="width:650px;float:left;">
			<input type="hidden" name="scene_id" value="<?php echo ($_GET['scene_id']); ?>"/>

				<input type="hidden" name="content" <?php if($_GET['content']): ?>value="resource"<?php endif; ?> />
				<input type="hidden" name="this_page" value="<?php echo ($this_page); ?>" />
				
				<input type="hidden" name="m" value="customer"/>
				<input type="hidden" name="act" id="act" value="index"/>
				<input type="hidden" name="daochu" id="daochu"/>
				<input type="hidden" name="selectexcelxport" id="selectexcelxport"/>
				<input type="hidden" name="current_page" id="current_page" value=""/>
				<input type="hidden" name="export_limit" id="export_limit" value=""/>
				<?php if($_GET['by']!= null): ?><input type="hidden" name="by" value="<?php echo ($_GET['by']); ?>"/><?php endif; ?>

			<?php if(empty($fields_search)): ?><div id="con_search_1" style="float:left;width:650px;margin:0 10px 0 10px;">
					<ul class="nav pull-left" style="margin:0px 0 0 23px;width:650px;">
						<li class="pull-left">
							<select id="field_1" style="width:auto" class="form-control input-sm field_name new-select" onchange="changeCondition(1)" >
								<option class="" value="name">--<?php echo L('PLEASE_SELECT_A_FILTER_CONDITION');?>--</option>
								<?php if(is_array($field_list)): $i = 0; $__LIST__ = $field_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; if($v['field'] != 'customer_owner_id'): ?><option class="<?php echo ($v['form_type']); ?>" value="<?php echo ($v[field]); ?>" rel="customer" <?php if($_GET['field'] == '' && $v['field'] == 'name'): ?>selected="selected"<?php endif; ?>><?php echo ($v[name]); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
								<?php if($_GET['content']!= 'resource'): ?><option class="role" value="owner_role_id"><?php echo L('PRINCIPAL');?></option><?php endif; ?>
								<option class="date" value="create_time"><?php echo L('CREATION_TIME');?></option>
								<option class="date" value="update_time"><?php echo L('MODIFICATION_TIME');?></option>
								<option class="text" value="contacts_name">首要联系人姓名</option>
								<option class="mobile" value="contacts_telephone">首要联系人电话</option>
								<!-- <option class="business_status" value="status_id">客户进度</option> -->
							</select>&nbsp;&nbsp;
						</li>
						<li class="pull-left" id="conditionContent_1">
							<select id="condition_1" style="width:auto" class="form-control input-sm new-select" onchange="changeSearch()" name="name[condition]">
								<option value="contains"><?php echo L('INCLUDE');?></option>
								<option value="not_contain"><?php echo L('EXCLUSIVE');?></option>
								<option value="is"><?php echo L('YES');?></option>
								<option value="isnot"><?php echo L('ISNOT');?></option>						
								<option value="start_with"><?php echo L('BEGINNING_CHARACTER');?></option>
								<option value="end_with"><?php echo L('TERMINATION_CHARACTER');?></option>
								<option value="is_empty"><?php echo L('MANDATORY');?></option>
								<option value="is_not_empty"><?php echo L('ISNOTEMPTY');?></option>
							</select>&nbsp;&nbsp;
						</li>
						<li class="pull-left" id="searchContent_1">
							<input id="search_1" type="text" style="width:160px;" class="input-medium form-control input-sm search-query" name="name[value]"/>&nbsp;&nbsp;
						</li>
					</ul>
				</div>
				<?php $max_key = 1;?>
			<?php else: ?>
				<?php if(is_array($fields_search)): $key1 = 0; $__LIST__ = $fields_search;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key1 % 2 );++$key1;?><div id="con_search_<?php echo ($key1); ?>" style="float:left;width:650px;margin:10px 10px 0 10px;">
						<div  id="rem_<?php echo ($key1); ?>" class="pull-left" style="line-height:30px;"><a href="javascript:void(0);" class="rem_search" rel="<?php echo ($key1); ?>" title="移除"><span class="fa fa-times-circle"></span></a></div>&nbsp;
						<ul class="nav pull-left" style="margin:0px 0 0 5px;width:620px;">
							<li class="pull-left">
								<select id="field_<?php echo ($key1); ?>" style="width:160px" class="form-control input-sm field_name" onchange="changeCondition(<?php echo ($key1); ?>)" >
									<option class="" value="name">--<?php echo L('PLEASE_SELECT_A_FILTER_CONDITION');?>--</option>
									<?php if(is_array($field_list)): $i = 0; $__LIST__ = $field_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; if($v['field'] != 'customer_owner_id'): ?><option class="<?php echo ($v['form_type']); ?>" value="<?php echo ($v['field']); ?>" rel="customer" <?php if($vo['field'] == $v['field']): ?>selected="selected"<?php endif; ?>><?php echo ($v[name]); ?>
											</option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
									<?php if($_GET['content'] != 'resource'): ?><option class="role" value="owner_role_id" <?php if($vo['field'] == 'owner_role_id'): ?>selected="selected"<?php endif; ?>><?php echo L('PRINCIPAL');?></option><?php endif; ?>
									<option class="date" value="create_time" <?php if($vo['field'] == 'create_time'): ?>selected="selected"<?php endif; ?>><?php echo L('CREATION_TIME');?></option>
									<option class="date" value="update_time" <?php if($vo['field'] == 'update_time'): ?>selected="selected"<?php endif; ?>><?php echo L('MODIFICATION_TIME');?></option>
									<option class="text" value="contacts_name" <?php if($vo['field'] == 'contacts_name'): ?>selected="selected"<?php endif; ?>>首要联系人姓名</option>
									<option class="mobile" value="contacts_telephone" <?php if($vo['field'] == 'contacts_telephone'): ?>selected="selected"<?php endif; ?>>首要联系人电话</option>
								</select>&nbsp;&nbsp;
							</li>
							<li class="pull-left" id="conditionContent_<?php echo ($key1); ?>">
								<?php if($vo["form_type"] == 'number'): ?><select style="width:100px;" class="form-control input-sm" name="<?php echo ($vo['field']); ?>[condition]">
										<option value="gt" <?php if($fields_search[$vo['field']][condition] == 'gt'): ?>selected="selected"<?php endif; ?>><?php echo L('GT');?></option>
										<option value="lt" <?php if($fields_search[$vo['field']][condition] == 'lt'): ?>selected="selected"<?php endif; ?>><?php echo L('LT');?></option>
										<option value="eq" <?php if($fields_search[$vo['field']][condition] == 'eq'): ?>selected="selected"<?php endif; ?>><?php echo L('EQ');?></option>
										<option value="neq" <?php if($fields_search[$vo['field']][condition] == 'neq'): ?>selected="selected"<?php endif; ?>><?php echo L('NEQ');?></option>
									</select>
								<?php elseif($vo["field"] == 'owner_role_id'): ?>
									<select style="width:100px;" class="form-control input-sm" name="<?php echo ($vo['field']); ?>[condition]">
										<option value="is" <?php if($_GET[$vo['field']][condition] == 'is'): ?>selected="selected"<?php endif; ?>>是</option>
										<option value="isnot" <?php if($_GET[$vo['field']][condition] == 'isnot'): ?>selected="selected"<?php endif; ?>>不是</option>
									</select>
								<?php elseif($vo["field"] == 'status_id' || $vo["form_type"] == 'datetime'): ?>
								<?php elseif($vo["form_type"] == 'box'): ?>
									<span id="<?php echo ($vo['field']); ?>"><?php echo ($vo["value"]); ?></span>
									<script type="text/javascript">
										var b = '<?php echo ($vo[field]); ?>';
										var c = 'customer';
										var value_str = $("#<?php echo ($vo['field']); ?>").html();
										$.ajax({
											type:'get',
											url:'index.php?m=setting&a=boxfield&model='+c+'&field='+b,
											async:false,
											success:function(data){
												options = '';
												$.each(data.data, function(k, v){
													if(value_str == v){
														select = 'selected';
													}else{
														select = '';
													}
													options += "<option value='"+v+"' "+select+">"+v+"</option>";
												});
												$("#<?php echo ($vo['field']); ?>").html('<select class="<?php echo ($vo["field"]); ?> form-control input-sm" style="width:auto" name="<?php echo ($vo["field"]); ?>[value]" ><option value="">--请选择--</option>' + options + '</select>&nbsp;&nbsp;');	
											},
											dataType:'json'
										});	
										// <?php if(!empty($_GET[$vo['field']])): ?>// 	$(".<?php echo ($vo['field']); ?> option[value='<?php echo ($_GET[$vo['field']]); ?>']").attr('selected','selected');
										//<?php endif; ?>
									</script>	
								<?php elseif($vo["form_type"] == 'address'): ?>
									<select style="width:auto;margin-top: 13px;" class="form-control input-sm" name="<?php echo ($vo['field']); ?>[condition]">
										<option value="contains" <?php if($fields_search[$vo['field']][condition] == 'contains'): ?>selected="selected"<?php endif; ?>><?php echo L('IN');?></option>
										<option value="not_contain" <?php if($fields_search[$vo['field']][condition] == 'not_contain'): ?>selected="selected"<?php endif; ?>><?php echo L('NOTIN');?></option>
									</select>
									<select name="<?php echo ($vo['field']); ?>[state]" class="form-control input-sm" id="state" style="width:135px;margin-top: 13px;"></select>
									<select name="<?php echo ($vo['field']); ?>[city]" class="form-control input-sm" id="city" style="width:110px;margin-top: 13px;"></select>
									<select name="<?php echo ($vo['field']); ?>[area]" class="form-control input-sm" id="area" style="width:110px;margin-top: 13px;"></select>
									<input type="text" id="search" name="<?php echo ($vo['field']); ?>[value]" value="<?php echo ($fields_search[$vo['field']][value]); ?>" class="form-control input-sm" style="margin-top: 13px;" placeholder="<?php echo L('THE_STREET_INFORMATION');?>" class="input-large">
									<script type="text/javascript">
										 new PCAS("<?php echo ($vo['field']); ?>[state]","<?php echo ($vo['field']); ?>[city]","<?php echo ($vo['field']); ?>[area]","<?php echo $fields_search[$vo['field']]['state']; ?>","<?php echo $fields_search[$vo['field']]['city']; ?>","<?php echo $fields_search[$vo['field']]['area']; ?>");
									</script>
								<?php else: ?>
									<select style="width:auto" class="form-control input-sm" name="<?php echo ($vo['field']); ?>[condition]">
										<option value="contains" <?php if($fields_search[$vo['field']][condition] == 'contains'): ?>selected="selected"<?php endif; ?>><?php echo L('INCLUDE');?></option>
										<option value="not_contain" <?php if($fields_search[$vo['field']][condition] == 'not_contain'): ?>selected="selected"<?php endif; ?>><?php echo L('EXCLUSIVE');?></option>
										<option value="is" <?php if($fields_search[$vo['field']][condition] == 'is'): ?>selected="selected"<?php endif; ?>><?php echo L('YES');?></option>
										<option value="isnot" <?php if($fields_search[$vo['field']][condition] == 'isnot'): ?>selected="selected"<?php endif; ?>><?php echo L('NO');?></option>					
										<option value="start_with" <?php if($fields_search[$vo['field']][condition] == 'start_with'): ?>selected="selected"<?php endif; ?>><?php echo L('BEGINNING_CHARACTER');?></option>
										<option value="end_with" <?php if($fields_search[$vo['field']][condition] == 'end_with'): ?>selected="selected"<?php endif; ?>><?php echo L('TERMINATION_CHARACTER');?></option>
										<option value="is_empty" <?php if($fields_search[$vo['field']][condition] == 'is_empty'): ?>selected="selected"<?php endif; ?>><?php echo L('MANDATORY');?></option>
										<option value="is_not_empty" <?php if($fields_search[$vo['field']][condition] == 'is_not_empty'): ?>selected="selected"<?php endif; ?>><?php echo L('ISNOTEMPTY');?></option>
									</select><?php endif; ?>
							</li>
							<li class="pull-left" id="searchContent_<?php echo ($key1); ?>" style="margin-left:5px;">
								<?php if($vo['form_type'] != 'box' && $vo['form_type'] != 'address'): if($vo['form_type'] == 'datetime'): ?><input id="start_<?php echo ($vo['field']); ?>" type="text" autocomplete="off" class="form-control input-sm search-query" name="<?php echo ($vo['field']); ?>[start]" onclick="WdatePicker()" value="<?php echo ($fields_search[$vo['field']][start]); ?>" rel="customer"/> 至 <input id="end_<?php echo ($vo['field']); ?>" type="text" autocomplete="off" class="form-control input-sm search-query" name="<?php echo ($vo['field']); ?>[end]" onclick="WdatePicker()" value="<?php echo ($fields_search[$vo['field']][end]); ?>" rel="customer"/>
									<?php elseif($vo['field'] == 'owner_role_id'): ?>
										<span id="owner_role_search" rel="<?php echo ($key1); ?>" rel1="<?php echo ($vo['field']); ?>[value]" rel2="<?php echo ($fields_search[$vo['field']][value]); ?>"/>
										<script type="text/javascript">
											var key_id = $('#owner_role_search').attr('rel');
											var search_owner_role_id = $('#owner_role_search').attr('rel1');
											var owner_roleid = $('#owner_role_search').attr('rel2');
											$.ajax({
												type:'get',
												url:'index.php?m=user&a=getrolelist&module=customer&action=index',
												async:false,
												success:function(data){
													options = '';
													$.each(data.data, function(k, v){
														options += '<option value="'+v.role_id+'" checkedit>'+v.user_name+' ['+v.department_name+'-'+v.role_name+'] </option>';
													});
													$("#searchContent_"+key_id+"").html('<select class="selectpicker show-tick form-control input-sm" data-live-search="true" id="search_'+key_id+'" name="'+search_owner_role_id+'" style="width:auto">' + options + '</select>');
													var owner_roleid = "<?php echo ($fields_search[$vo['field']][value]); ?>";
													$('#search_'+key_id+' option[value='+owner_roleid +']').prop('selected',true);
													
												},
												dataType:'json'
											});		
										</script>
									<?php elseif($vo['field'] == 'status_id'): ?>
										<span id="bus_status_id" rel="<?php echo ($key1); ?>" rel1="<?php echo ($vo['field']); ?>[value]" rel2="<?php echo ($fields_search[$vo['field']][value]); ?>"/>
										<script type="text/javascript">
											var key_id = $('#bus_status_id').attr('rel');
											var search = $('#bus_status_id').attr('rel1');
											var search_roleid = $('#bus_status_id').attr('rel2');
											$.ajax({
												type:'get',
												url:'index.php?m=setting&a=getbusinessstatuslist',
												async:false,
												success:function(data){
													options = '';
													$.each(data.data, function(k, v){
														select = '';
														if(v.status_id == search_roleid){
															select = 'selected';
														}
														options += '<option value="'+v.status_id+'" '+select+'>'+v.name+'</option>';
													});

													$("#searchContent_"+key_id+"").html('<select class="form-control input-sm" id="search_'+key_id+'" style="width:auto" name="'+search+'">' + options + '</select>');
													var owner_roleid = "<?php echo ($fields_search[$vo['field']][value]); ?>";
													$('#search_'+key_id+' option[value='+owner_roleid +']').prop('selected',true);
												},
												dataType:'json'
											});	
										</script>			
									<?php else: ?>	
										<input name="<?php echo ($vo['field']); ?>[value]" type="text" class="form-control input-sm search-query" class="<?php echo ($vo['form_type']); ?>" value="<?php echo ($fields_search[$vo['field']][value]); ?>" rel="customer"><?php endif; endif; ?>
							</li>
						</ul>
					</div>
					<?php $max_key = $key1; endforeach; endif; else: echo "" ;endif; endif; ?>
		</div>
		<div class="clearfix"></div>
		<div style="margin-left: 35px;margin-top:10px;"><a href="javascript:void(0);" style="display: -moz-stack;margin: 30px 0px 0px; font-size: 12px; color: rgb(62, 133, 233);" id="add_btn">+添加筛选条件</a>
		</div>
	</form>
</div>
<div style="display:none" id="dialog-addbusiness" title="添加商机">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<div style="display:none" id="dialog-excelexport" title="导出客户">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<div style="display:none" id="dialog-share" title="分享客户">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<div style="display:none" id="dialog-addproduct" title="添加商机">
    <div class="spiner-example">
        <div class="sk-spinner sk-spinner-three-bounce">
            <div class="sk-bounce1"></div>
            <div class="sk-bounce2"></div>
            <div class="sk-bounce3"></div>
        </div>
    </div>
</div>
<div style="display:none" id="dialog-product_view" title="产品详情">
    <div class="spiner-example">
        <div class="sk-spinner sk-spinner-three-bounce">
            <div class="sk-bounce1"></div>
            <div class="sk-bounce2"></div>
            <div class="sk-bounce3"></div>
        </div>
    </div>
</div>
<div style="display:none;" id="dialog-add_scene" title="新建场景">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<div style="display:none;" id="dialog-setting_scene" title="场景管理">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<div style="display:none" id="dialog-log_history" title="跟进记录">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>

<div class="modal inmodal fade" id="call_list" aria-hidden="true" aria-labelledby="call_list" role="dialog" tabindex="-1" style="overflow:auto; border:1px solid #000000;">
    <div class="modal-dialog">
        <div class="modal-content" id="call_modal">
        </div>
    </div>
</div>
<script type="text/javascript">
//呼出电话
$(document).on('click','.tel_list',function(){
	var model = $(this).attr('model');
	var model_id = $(this).attr('rel');
	var url = "<?php echo U('call/teldata','model=');?>"+model+'&model_id='+model_id;
	$('#call_list').modal('show');
    $('#call_modal').load(url);
});

//dropdown menu 不关闭
$('#dropdown_order').on('click', function(event){
	event.stopPropagation();
});
//客户转移
$("#change").click(function(){
	var id_array = new Array();
	$("input[class='check_list']:checked").each(function(){
		if ($('#oDivL_tab_Test3').length > 0) {
			if ($(this).parents('#oDivL_tab_Test3').length == 1) {
				id_array.push($(this).val());
			}
		} else {
			id_array.push($(this).val());
		}
	});
	if(id_array.length == 0){
		alert("<?php echo L('YOU_HAVE_NOT_CHOSEN_ANY_CUSTOMERS');?>");
	}else{
		var customer_ids = id_array.join(",");
		$('#dialog-transform').dialog('open');
		// $('#dialog-transform').load("<?php echo U('customer/transfer_edit');?>","customer_id="+customer_ids);
		$('#dialog-transform').load("<?php echo U('customer/transfer_edit');?>", () => {
			$('#form_transfer [name="customer_id"]').val(customer_ids);
		});
	}
});
//客户分享
$("#share").click(function(){
	var id_array = new Array();
	$("input[class='check_list']:checked").each(function(){
		id_array.push($(this).val());
	});
	if(id_array.length == 0){
		alert("<?php echo L('YOU_HAVE_NOT_CHOSEN_ANY_CUSTOMERS');?>");
	}else{
		var customer_ids = id_array.join(",");
		$('#dialog-share').dialog('open');
		$('#dialog-share').load("<?php echo U('customer/share');?>","customer_id="+customer_ids);
	}
});
//取消客户分享
$("#close_share").click(function(){
	var id_array = new Array();
	$("input[class='check_list']:checked").each(function(){
		id_array.push($(this).val());
	});
	if(id_array.length == 0){
		alert("<?php echo L('YOU_HAVE_NOT_CHOSEN_ANY_CUSTOMERS');?>");
	}else{
		var customer_ids = id_array.join(",");
		swal({
			title: "您确定要取消客户共享信息吗？",
			text: "移除后将无法恢复，请谨慎操作！",
			type: "warning",   
			showCancelButton: true,   
			confirmButtonColor: "#DD6B55",   
			confirmButtonText: "是的，取消！",
			cancelButtonText:'让我再考虑一下…',
			closeOnConfirm:false,
			closeOnCancel:false
		},
		function(isConfirm){
			if (isConfirm) {
				window.location.href = "<?php echo U('customer/close_share','customer_ids=');?>"+customer_ids;
			} else {
				swal("已取消","您取消了此操作！","error");
			}
		});
	}
});
$("#dialog-sendsms").dialog({
	autoOpen: false,
	width: 650,
	maxHeight: 500,
	position: ["center", 100],
	modal: true,
	buttons: {
		"发送": function () {
			var sms_con = $('#sendsms_div>form textarea').val();
			sms_con = $.trim(sms_con);
			if (sms_con == '') {
				swal({
					title: "短信内容不能为空！",
					text: "",
					type: "warning",
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "确认",
					closeOnConfirm: false
				}, function () {
					swal.close();
					$('#sendsms_div>form textarea').focus();
				});
				return false;
			}
			$('#sendsms_div>form').submit();
			$(this).dialog("close");
		},
		"取消": function () {
			$(this).dialog("close");
		}
	},
	close: function () {
		$(this).html('');
	}
});
$('.sendsms').on('click', function(){
	var where = $(this).attr('where');
	if (where == 'ids') {
		var id_array = new Array();
		$("input[class='check_list']:checked").each(function () {
			id_array.push($(this).val());
		});
		where = '&ids=' + id_array.join(",");
	} else {
		where = '&' + '<?php echo ($_where); ?>';
	}
	$("#dialog-sendsms").dialog('open');
	$("#dialog-sendsms").load('<?php echo U("customer/sendsms");?>' + where);
});
$("#dialog-transform").dialog({
    autoOpen: false,
	width: 530,
	maxHeight: 600,
	position: ["center",100],
	buttons: {
		"确认转移": function () {
			$('#form_transfer').submit();
			$(this).dialog("close");
		},
		"取消": function () {
			$(this).dialog("close");
		}
	},
	close: function() {
    	$(this).html('');
    }
});
$("#dialog-share").dialog({
    autoOpen: false,
    modal: true,
	width: 720,
	maxHeight: 600,
	position: ["center",100],
	buttons: {
		"确认分享": function () {
			$('#share_form').submit();
			$(this).dialog("close");
		},
		"取消": function () {
			$(this).dialog("close");
		}
	},
	close: function() {
    	$(this).html('');
    }
});
//自定义排序
$('#dropdown_order').on('click','.save_order',function(){
	var by = "<?php echo ($_GET['by']); ?>";
	var scene_id = "<?php echo ($_GET['scene_id']); ?>";
	var order_field = $("input[name='order_field']:checked").val();
	var order_type = $("input[name='order_type']:checked").val();
	var order_parameter = '<?php echo ($order_parameter); ?>';

	if (order_field != '' && order_type != '') {
		if (order_type == 'cancel_order') {
			window.location.href = "<?php echo U('customer/index');?>"+'&'+order_parameter;
		} else {
			window.location.href = "<?php echo U('customer/index');?>"+'&'+order_parameter+'&order_type='+order_type+'&order_field='+order_field;
		}
	}
});

/*让复选框默认取消选择*/
$(':checkbox').prop('checked', false);

$('[data-toggle="tooltip"]').tooltip({html:true});

$('#status_id').val("<?php echo ($_GET['status_id']); ?>"); 
$('#status_id').change(function(){ 
	$('#customer_search').submit(); 
});

/*添加筛选条件*/
var m = <?php echo ($max_key); ?>;
$('#add_btn').click(function(){
	m += 1;
	$('#search_add').append('<div id="con_search_'+m+'" style="float:left;width:650px;padding-top:10px;margin:0px 10px 0 10px;"><div  id="rem_'+m+'" class="pull-left" style="line-height:30px;"><a href="javascript:void(0);" class="rem_search" rel="'+m+'" title="移除"><span class="fa fa-times-circle"></span></a></div>&nbsp;<ul class="nav pull-left" style="margin:0px 0 0 5px;width:620px"><li class="pull-left"><select id="field_'+m+'"  style="width:auto" class="form-control input-sm field_name new-select" onchange="changeCondition('+m+')" name=""><option class="" value="name">--<?php echo L('PLEASE_SELECT_A_FILTER_CONDITION');?>--</option><?php if(is_array($field_list)): $i = 0; $__LIST__ = $field_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; if($v[field] != 'customer_owner_id'): ?><option class="<?php echo ($v['form_type']); ?>" value="<?php echo ($v[field]); ?>" rel="customer"><?php echo ($v[name]); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; if($_GET['content']!= 'resource'): ?><option class="role" value="owner_role_id"><?php echo L('PRINCIPAL');?></option><?php endif; ?><option class="date" value="create_time"><?php echo L('CREATION_TIME');?></option><option class="date" value="update_time"><?php echo L('MODIFICATION_TIME');?></option><option class="text" value="contacts_name">首要联系人姓名</option><option class="mobile" value="contacts_telephone">首要联系人电话</option></select>&nbsp;&nbsp;</li><li class="pull-left" id="conditionContent_'+m+'"><select id="condition_'+m+'" style="width:99px" class="form-control input-sm new-select" name="condition" onchange="changeSearch()"><option value="contains"><?php echo L('INCLUDE');?></option><option value="not_contain"><?php echo L('EXCLUSIVE');?></option><option value="is"><?php echo L('YES');?></option><option value="isnot"><?php echo L('ISNOT');?></option><option value="start_with"><?php echo L('BEGINNING_CHARACTER');?></option><option value="end_with"><?php echo L('TERMINATION_CHARACTER');?></option><option value="is_empty"><?php echo L('Mandatory');?></option><option value="is_not_empty"><?php echo L('ISNOTEMPTY');?></option></select>&nbsp;&nbsp;</li><li class="pull-left" id="searchContent_'+m+'"><input id="search_'+m+'" type="text" style="width:160px;" class="input-medium form-control input-sm search-query" name="value"/>&nbsp;&nbsp;</li></ul></div>');  
});
$(document).on('click','.rem_search',function(){
	var num = $(this).attr('rel');
	$('#con_search_'+num).remove();
});

// 筛选重复判断
var dosearch = 1;
function doh(){
	var ary = new Array();
	var field_name = '';
	var is_submit = 1;
	$('.field_name').each(function(k, v){
		field_name = $(this).find("option:selected").val();
		if(jQuery.inArray(field_name,ary) >= 0){
			is_submit = 0;
			swal({
				title: "筛选条件中有重复项！",
				text: "",
				type: "error"
			});
			dosearch = 0;
			return false;
		}
		ary[k] = field_name;
	})
	if(is_submit == 1){
		$("#searchForm").submit();
	}
}


$("#dialog-product_view").dialog({
    autoOpen: false,
    modal: true,
    minWidth: 800,
    maxHeight: 600,
    position: ["center",100]
});
//产品详情
$(".product_view").click(function(){
    var business_id = $(this).attr('rel');
    var customer_id = $(this).attr('rel1');
    var code = $(this).attr('rel2');
    //判断是否有商机编号，如果没有则提示添加产品
    if(!code){
    	swal({
	        title: "请先添加产品信息",
	        text: "",
	        type: "warning",
	        showCancelButton: true,
	        confirmButtonColor: "#DD6B55",
	        confirmButtonText: "添加产品",
	        closeOnConfirm: true,
	    }, function () {
	    	// $('#dialog-addbusiness').dialog('open');
			$('#dialog-addproduct').dialog('open');
			$('#dialog-addproduct').load('<?php echo U("product/mutildialog_product","customer_id=");?>'+customer_id);
	    });
    }else{
    	$('#dialog-product_view').dialog('open');
    	$('#dialog-product_view').load('<?php echo U("business/product_view","id=");?>'+business_id);
    }
});
/*营销阶段*/
$("#dialog-advance").dialog({
    autoOpen: false,
    modal: true,
    minWidth: 320,
    maxHeight: 800,
    position: ["center",100]
});
//推进
$(".advance").click(function(){
    var business_id = $(this).attr('rel');
    var customer_id = $(this).attr('rel1');
    var code = $(this).attr('rel2');
    //判断是否有商机编号，如果没有则提示添加产品
    if(!code){
    	swal({
	        title: "请先添加产品信息",
	        text: "添加产品信息后，才可以推进",
	        type: "warning",
	        showCancelButton: true,
	        confirmButtonColor: "#DD6B55",
	        confirmButtonText: "添加产品",
	        closeOnConfirm: true,
	    }, function () {
			// $('#dialog-addbusiness').dialog('open');
			$('#dialog-addproduct').dialog('open');
			$('#dialog-addproduct').load('<?php echo U("product/mutildialog_product","customer_id=");?>'+customer_id);
	    });
    }else{
    	$('#dialog-advance').dialog('open');
    	$('#dialog-advance').load('<?php echo U("business/advance","id=");?>'+business_id);
    }
});

if ("<?php echo C('isMobile');?>" == "1") {
	width = $('.container').width() * 0.9;
} else {
	width = 800;
}

$("#dialog-add_scene").dialog({
    autoOpen: false,
    modal: true,
	width: 750,
	height: 500,
	position: ["center",100],
	buttons: {
		"确定": function () {
			if ($('#scene_name').val() == '') {
				alert_crm('请填写场景名称！');
				return false;
			}
			dohDialog();
			if(dosearchDialog == 1){
				var dialog = $('#scene_dialog').val();
				var temp = '';	
				$.ajax({
		            type:'post',
		            url: "<?php echo U('system/scene_add');?>",
		            data:$("#searchFormDialog").serialize(),
		            async: false,
		            success: function (data) {
						if (data.status == 1) {
							swal("温馨提示！", "创建成功！", "success");
							if (dialog == 1) {
								temp += '<tr class="controls_tr" id="tr_"+data.data.id>\
									<td><input type="hidden" class="check_list_scene" name="id[]" value="'+data.data.id+'"/></td>\
									<td>'+data.data.name+'</td>\
									<td>\
										<div class="radio radio-info radio-inline" style="padding-left:20px;">';
								if (data.data.is_default == 1) {
									temp += '<input type="radio" class="is_default" name="is_default"  rel="'+data.data.id+'" checked /><label for=""></label>';
								} else {
									temp += '<input type="radio" class="is_default" name="is_default"  rel="'+data.data.id+'"/><label for=""></label>';
								}
								temp += '</div>\
									</td>\
									<td>';
								if (data.data.is_hide == 0) {
									temp += '<a class="is_hide" href="javascript:void(0);" rel="'+data.data.id+'" title="不启用" ><span class="fa fa-toggle-on" id="hide_span_'+data.data.id+'" style="color:#ccc;font-size:20px;"></span></a>';
								} else {
									temp += '<a class="is_hide" href="javascript:void(0);" rel="'+data.data.id+'" title="启用" ><span class="fa fa-toggle-off" id="hide_span_'+data.data.id+'" style="color:#ccc;font-size:20px;"></span></a>';
								}
								temp += '</td>\
										<td>\
											<a class="edit_scene" style="float:left;" href="javascript:void(0);" rel="'+data.data.id+'" title="编辑"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;\
											<a class="delete" style="float:left;margin-left:10px;" href="javascript:void(0);" rel="'+data.data.id+'" title="删除"><i class="fa fa-times"></i></a>\
										</td>\
									</tr>';
								$('#tab_Test3_scene').append(temp);
							} else {
								location.reload();
							}
						}else{
							swal({
								title: "操作失败！",
								text:data.info,
								type: "error"
							})
							return false;
						}
		            },
		            dataType: 'json'
		        });
		        $(this).dialog("close");
	        }
		},
		"取消": function () {
			$(this).dialog("close");
		}
	},
	close:function(){
		scene_list();
		$(this).html(''); 
	}
});

$("#add_scene").click(function(){
	$('#dialog-add_scene').dialog('open');
	$('#dialog-add_scene').load('<?php echo U("system/scene_add","module=customer");?>');
});

$("#dialog-setting_scene").dialog({
    autoOpen: false,
    modal: true,
	width: 900,
	height: 500,
	position: ["center",100],
	buttons: {
		"确定": function () {
			$(this).dialog("close");
		},
		"取消": function () {
			$(this).dialog("close");
		}
	},
	close:function(){ 
		scene_list();
		$(this).html(''); 
	}
});

$("#setting_scene").click(function(){
	$('#dialog-setting_scene').dialog('open');
	$('#dialog-setting_scene').load('<?php echo U("system/scene_setting","module=customer");?>');
});

//场景刷新
function scene_list(){
	var scene_temp = '';
	var url = '';
	$.ajax({
        type:'post',
        url: "<?php echo U('system/sceneListAjax');?>",
        data:{module:'customer'},
        async: false,
        success: function (data) {
			if (data.status == 1) {
				$(data.data).each(function(k, v){
					if (v.type == 1) {
						url = "<?php echo U('customer/index','by=');?>"+v.by;
						scene_temp += '<a href="'+url+'" style="width:100%;color:#676a6c;padding:5px !important;float:left;">\
							<div style="padding:0 10px;">\
						    	<span class="pull-left" style="line-height:26px;">&nbsp;&nbsp;'+v.name+'</span>\
						    </div>\
						</a>';
					} else {
						url = "<?php echo U('customer/index','scene_id=');?>"+v.id;
						scene_temp += '<a href="'+url+'" style="width:100%;color:#676a6c;padding:5px !important;float:left;">\
							<div style="padding:0 10px;">\
						    	<span class="pull-left" style="line-height:26px;">&nbsp;&nbsp;'+v.name+'</span>\
						    </div>\
						</a>'; 
					}
				});
				$('#scene_list').html(scene_temp);
			}
		},
		dataType: 'json'
	});
}

$("#dialog-map").dialog({
    autoOpen: false,
    modal: true,
	width: 800,
	minHeight: 600,
	position: ["center",100]
});

$(".getMap").click(function(){
	var map = $(this).attr('rel');
	$('#dialog-map').dialog('open');
	$('#dialog-map').load('<?php echo U("setting/mapdialog","map=");?>'+map);
});

$("#log_customer").click(function(){
	$('#dialog-log').dialog('open');
	$('#dialog-log').load('<?php echo U("log/add","r=RCustomerLog&module=customer&id=");?>'+$(this).attr('rel'));
});

$('#form1').on('click','.log_customer',function(){
	$('#dialog-log').dialog('open');
	$('#dialog-log').load('<?php echo U("log/add","r=RCustomerLog&module=customer&id=");?>'+$(this).attr('rel'));
});

$("#dialog-log").dialog({
    autoOpen: false,
    // modal: true,
	width: 750,
	maxHeight: 500,
	position: ["center",100],
	buttons: { 
		"确定": function () {
			var log_content = $('#log_content').val();
			if(log_content == ""){
				alert_crm("请填写沟通记录！");
				$("#log_content").focus();
				return false;
			}
			var status_id = $('#status_id option:selected').val();
			if ($('#save_reply').is(':checked') && status_id == '') {
				alert_crm("请选择跟进类型！");
				$("#status_id").focus();
				return false;
			}
			var grade = $('#dialog_form1').find('[name="grade"]').val();
			var customer_id = $('#dialog_form1').find('[name="id"]').val();
        	$.ajax({
	            type:'post',
	            url: "<?php echo U('Log/add');?>",
	            data:$('#dialog_form1').serialize(),
	            async: false,
	            success: function (data) {
					if (data.status == 1) {
						swal("操作成功！", "沟通日志添加成功！", "success");
						$("#dialog-log").dialog("close");
						// location.reload();
						$('[rel='+ customer_id +']').find('.fa-star').removeClass("star-orange");
						$('[rel='+ customer_id +']').find('.fa-star:lt(' + grade + ')').addClass("star-orange");
					} else {
						swal({
							title: "操作失败！",
							text:data.info,
							type: "error"
						});
						return false;
					}
	            },
	            dataType: 'json'
	        });
		},
		"取消": function () {
			$(this).dialog("close");
		}
	},
	close: function() {
    	$(this).html('');
    }
});

$("#dialog-role-info").dialog({
    autoOpen: false,
    modal: true,
	width: width,
	maxHeight: 550,
	position: ["center",100]
});

$("#dialog-import").dialog({
	autoOpen: false,
	modal: true,
	width: width,
	maxHeight: 500,
	position: ["center",100],
	buttons: {
		"确定": function () {
			$(this).dialog('close');
			swal({
				type: 'info',
				title: '页面刷新中...',
				showConfirmButton: false
			});
			history.go(0);
		}
	}
});

$("#dialog-field-search").dialog({
	autoOpen: false,
	modal: true,
	width: 700,
	minHeight: 350,
	maxHeight: 450,
	position: ["center",100],
	buttons: {
		"确定": function () {
			doh();
			if(dosearch == 1){
				$(this).dialog("close");
			}
		},
		"取消": function () {
			$(this).dialog("close");
		}
	}
});

$("#dialog-fenpei").dialog({
	autoOpen: false,
	modal: true,
	width: width,
	maxHeight: 400,
	position: ["center",100],
	buttons: {
		"确定": function () {
			$('#fenpei_form').submit();
			$(this).dialog("close");
		},
		"取消": function () {
			$(this).dialog("close");
		}
	},
	close: function() {
    	$(this).html('');
    }
});

$("#dialog-remind").dialog({
	autoOpen: false,
	modal: true,
	width: width,
	maxHeight: 400,
	position: ["center",100],
	buttons: {
		"确定": function () {
			if($('#dialog_content').val() == ''){
				alert_crm("请填写提醒内容！");
				$("#dialog_content").focus(); 
			}else{
				$('#remind_form').submit();
				$(this).dialog("close");
			}
		},
		"取消": function () {
			$(this).dialog("close");
		}
	},
	close: function() {
    	$(this).html('');
    }
});

$("#dialog-remind_view").dialog({
	autoOpen: false,
	modal: true,
	width: width,
	maxHeight: 400,
	position: ["center",100],
	buttons: {
		"删除": function () {
			swal({
				title: "您确认删除吗？" ,
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "确定",   
				closeOnConfirm: false 
			}, 
			function(){
				var dislog_module_id = $('#dislog_module_id').val();
				var dislog_module = $('#dislog_module').val();
				$.ajax({
		            type:'post',
		            url: "<?php echo U('remind/delete');?>",
		            data: {module_id: dislog_module_id,module: dislog_module},
		            async: false,
		            success: function (data) {
						if (data.status == 1) {
							$('#remind_view_'+dislog_module_id).addClass('hide');
							swal("操作成功！", "提醒删除成功！", "success");
							$("#dialog-remind_view").dialog("close");
						} else {
							swal({
								title: "操作失败！",
								text:data.info,
								type: "error"
							})
							return false;
						}
		            },
		            dataType: 'json'
		        });
				// $("#dialog_remind").submit();
				// $(this).dialog("close");
			});
		},
		"取消": function () {
			$(this).dialog("close");
		}
	},
	close: function() {
    	$(this).html('');
    }
});

$("#dialog-assign").dialog({
	autoOpen: false,
	modal: true,
	width: width,
	maxHeight: 400,
	position: ["center",100],
	buttons: {
		"确定": function () {
			var owner_role_id = $('input[name="owner_role_id"]').val();
			var message_alert = $('input:checkbox[name="message_alert"]:checked').val();
			var sms_alert = $('input:checkbox[name="sms_alert"]:checked').val();
			var email_alert = $('input:checkbox[name="email_alert"]:checked').val();
			
			$("#hidden_owner_id").val(owner_role_id);
			$("#hidden_message").val(message_alert);
			$("#hidden_sms").val(sms_alert);
			$("#hidden_email").val(email_alert);
			
			$("#form1").attr('action', '<?php echo U("customer/receive");?>');
			$('#operating_type').val('assign');
			$("#form1").submit();
			$(this).dialog("close");
		},
		"取消": function () {
			$(this).dialog("close");
		}
	},
	close: function() {
    	$(this).html('');
    }
});

$("#dialog-addbusiness").dialog({
	autoOpen: false,
	modal: true,
	minWidth: 850,
	maxHeight: 400,
	position: ["center",100],
	buttons: {
		"确定": function () {
			if(typeof($('.bus_product').val()) == 'undefined' ||  $('.bus_product').val() == '0'){
				alert_crm("请至少选择一个产品！");
			}else{
				$('#addbusiness_form').submit();
				$(this).dialog("close");
			}
		},
		"取消": function () {
			$(this).dialog("close");
		}
	},
	close: function() {
    	$(this).html('');
    }
});

$(".addproduct").click(function(){
    var customer_id = $(this).attr('rel');
    $('#dialog-addproduct').load('<?php echo U("product/mutildialog_product","customer_id=");?>'+customer_id); 
    $('#dialog-addproduct').dialog('open');
});

// 选择产品
$("#dialog-addproduct").dialog({
    autoOpen: false,
    modal: true,
    minWidth: 850,
    maxHeight: 600,
    position: ["center",100],
    buttons: {
        "确定": function () {
            var is_product = $('.cproduct_id').val();
            if(is_product == 'undefined' || is_product == '0'){
                alert_crm('请至少选择一个产品！');
            }else{
                $('#addbusiness_form').submit();
                $(this).dialog("close");
            }
        },
        "取消": function () {
            $(this).html('');
            $(this).dialog("close");
        }
    },
    close: function() {
    	$(this).empty();
    }
});

$(".addBusiness").click(function(){
	var customer_id = $(this).attr('rel');
	$('#dialog-addbusiness').dialog('open');
	$('#dialog-addbusiness').load('<?php echo U("product/mutildialog_business","customer_id=");?>'+customer_id);
});

function changeContent(){
	a = $("#select1 option:selected").val();
	window.location.href="<?php echo U('customer/index', 'by=');?>"+a;
}

$(function(){
	$("#edit_customer").click(function(){
		window.location.href="<?php echo U('customer/edit', '&p='.$this_page.'&id=');?>"+$(this).attr('rel');
	})
	
	$('#delete').click(function(){
		var id_array = new Array();
		$("input.check_list:checked").each(function(){  
			if ($('#oDivL_tab_Test3').length > 0) {
				if ($(this).parents('#oDivL_tab_Test3').length == 1) {
					id_array.push($(this).val());
				}
			} else {
				id_array.push($(this).val());
			}
		});
		if(id_array.length == 0){
			alert_crm('<?php echo L('YOU_HAVE_NOT_CHOSEN_ANY_CUSTOMERS');?>');
			return false;
		}
		swal({
			title: "您确定要删除客户信息吗？",
   			text: "删除后将无法恢复，且会删除相关联系人，请谨慎操作！",
			type: "warning",   
			showCancelButton: true,   
			confirmButtonColor: "#DD6B55",   
			confirmButtonText: "是的，我要删除！",
	        cancelButtonText:'让我再考虑一下…',
	        closeOnConfirm:false,
	        closeOnCancel:false
		},
		function(isConfirm){
	        if (isConfirm) {
	        	$.ajax({
		            type:'post',
		            url: "<?php echo U('customer/delete');?>",
		            data: {customer_id: id_array},
		            async: false,
		            success: function (data) {
						if (data.status == 1) {
							swal("删除成功！", "您已经永久删除了信息！", "success");
							location.reload();
						} else {
							swal({
								title: "操作失败！",
								text:data.info,
								type: "error"
							})
							return false;
						}
		            },
		            dataType: 'json'
		        });
	        } else {
	            swal("已取消","您取消了删除操作！","error");
	        }
	    });
	});
	
	$('#remove').click(function(){
		swal({   
			title: "<?php echo L('CONFIRMED_IN_THE_CUSTOMER_POOL');?>",
			type: "warning",   
			showCancelButton: true,   
			confirmButtonColor: "#DD6B55",   
			confirmButtonText: "确定",   
			closeOnConfirm: false 
		}, 
		function(){
			$("#form1").attr('action', '<?php echo U("customer/remove");?>');
			$('#operating_type').attr('value', 'remove');
			$("#form1").submit();
		});
	});

	$("#import_excel").click(function(){
		$('#dialog-import').dialog('open');
		$('.ui-dialog-buttonpane.ui-widget-content.ui-helper-clearfix').hide();
		$('#dialog-import').load('<?php echo U("customer/excelimport");?>');
	});

	$(".role_info").click(function(){
		$role_id = $(this).attr('rel');
		$('#dialog-role-info').dialog('open');
		$('#dialog-role-info').load('<?php echo U("user/dialoginfo","id=");?>'+$role_id);
	});

	$("#check_send").click(function(){
		var id_array = new Array();
		$("input.check_list:checked").each(function(){  
			id_array.push($(this).val());
		});
		if(id_array.length == 0){
			alert('<?php echo L('YOU_HAVE_NOT_CHOSEN_ANY_CUSTOMERS');?>');
		}else{
			var customer_ids = id_array.join(",");
			window.open("<?php echo U('setting/sendSms', 'model=customer&customer_ids=');?>"+customer_ids);
		}
	});

	$("#check_send_email").click(function(){
		var id_array = new Array();
		$("input.check_list:checked").each(function(){  
			id_array.push($(this).val());
		});
		
		if(id_array.length == 0){
			swal("<?php echo L('YOU_HAVE_NOT_CHOSEN_ANY_CUSTOMERS');?>");
		}else{
			var customer_ids = id_array.join(",");
			window.open("<?php echo U('setting/sendemail', 'model=customer&customer_ids=');?>"+customer_ids);
		}
	});

	$("#page_send").click(function(){
		var id_array = new Array();
		$("input[class='check_list']").each(function(){
			id_array.push($(this).val());
		});
		if(id_array.length == 0){
			swal("<?php echo L('YOU_HAVE_NOT_CHOSEN_ANY_CUSTOMERS');?>");
		}else{
			var customer_ids = id_array.join(",");
			window.open("<?php echo U('setting/sendSms', 'model=customer&customer_ids=');?>"+customer_ids);
		}
	});

	$("#page_send_email").click(function(){
		var id_array = new Array();
		$("input[class='check_list']").each(function(){
			id_array.push($(this).val());
		});
		if(id_array.length == 0){
			swal("<?php echo L('YOU_HAVE_NOT_CHOSEN_ANY_CUSTOMERS');?>");
		}else{
			var customer_ids = id_array.join(",");
			window.open("<?php echo U('setting/sendemail', 'model=customer&customer_ids=');?>"+customer_ids);
		}
	});

	$("#transform").click(function(){
		var id_array = new Array();
		$("input.check_list:checked").each(function(){
			id_array.push($(this).val());
		});
		if(id_array.length == 0){
			swal("<?php echo L('YOU_HAVE_NOT_CHOSEN_ANY_CUSTOMERS');?>");
		}else{
			var customer_ids = id_array.join(",");
			$('#dialog-transform').dialog('open');
			$('#dialog-transform').load("<?php echo U('customer/transfer_edit');?>","customer_id="+customer_ids);
		}
	});
	
	$("#all_send_email").click(function(){
		window.open("<?php echo U('setting/sendemail', 'model=customer&customer_ids=all');?>");
	});
	
	$("#all_send").click(function(){
		$("#act").val('sms');
		$("#searchForm").submit();
	});
	
	$(".fenpei").click(function(){
		var customer_id = $(this).attr('rel');
		$('#dialog-fenpei').dialog('open');
		$('#dialog-fenpei').load('<?php echo U("customer/fenpei","customer_id=");?>'+customer_id);
	});
	
	$("#remind").click(function(){
		let customer_id = new Array();
		$("input.check_list:checked").each(function () {
			customer_id.push($(this).val());
		});
		customer_ids = customer_id.join(',');
		$('#dialog-remind').dialog('open');
		$('#dialog-remind').load("<?php echo U('remind/add','module=customer&module_id=');?>"+customer_ids);
	});
	
	$(".remind_view").click(function(){
		var customer_id = $(this).attr('rel');
		$('#dialog-remind_view').dialog('open');
		$('#dialog-remind_view').load("<?php echo U('remind/view','module=customer&module_id=');?>"+customer_id);
	});
	
	$("#to_top").click(function(){
		var customer_id = $(this).attr('rel');
		$("#form1").attr('action', '<?php echo U("customer/settop","module=customer&module_id=");?>'+customer_id);
		$("#form1").submit();
	});
	$('#batch_assign').click(function(){
		$('#dialog-assign').dialog('open');
		$('#dialog-assign').load('<?php echo U("customer/fenpei");?>');
	});

	$('#batch_receive').click(function(){
		swal({   
			title: "<?php echo L('SURE_YOU_WANT_TO_BATCH_TO_RECEIVE');?>",
			type: "warning",   showCancelButton: true, 
			confirmButtonColor: "#DD6B55",   
			confirmButtonText: "确定",   
			closeOnConfirm: false 
		}, function(){  
			$("#form1").attr('action', '<?php echo U("customer/receive");?>');
			$('#operating_type').attr('value', 'receive');
			$("#form1").submit();
		});
	});

	$("#search_type").click(function(){
		$("#dialog-field-search").dialog('open');
	});

	$("#dialog-log_history").dialog({
		autoOpen: false,
		modal: true,
		width: 650,
		maxHeight: 450,
		position: ["center",100],
		close:function(){
			$(this).html("");
		},
		buttons: {
			"确定": function () {
				$(this).dialog("close");
			},
			"取消": function () {
				$(this).dialog("close");
			}
		}
	});
	$(".log_history").click(function(){
		var module = 'customer';
		var module_id = $(this).attr('rel');
		$('#dialog-log_history').dialog('open');
		$('#dialog-log_history').load('<?php echo U("log/commun_list","module=");?>'+module+'&module_id='+module_id);
	});
});	
</script>

<!-- 导出功能js -->
<script type="text/javascript">
	// 定义全局变量
	var url = "<?php echo U('customer/getcurrentstatus');?>";
	var limit_size = 1000;
	var count = <?php echo (($count)?($count):0); ?>;
	var times = 1;  

	// dialog弹窗
	$("#dialog-excelexport").dialog({
	    autoOpen: false,
	    modal: true,
	    minWidth: 550,
	    maxHeight: 400,
	    position: ["center", 100],
	    buttons: {
	        "确定": function () {
	        	var select_array = new Array();
				$("input.select_list:checked").each(function() {  
					select_array.push($(this).val());
				});

				var field = "<?php echo ($_GET['field']); ?>";
				if (field) {
					$('#selectexcelxport_field').val(select_array);
				} else {
					$('#selectexcelxport').val(select_array);
				}

				var total_count = count;
				var id_array = new Array();
				$('input[class="check_list"]:checked').each(function() {
					if ($.inArray($(this).val(), id_array) == -1) {
						// 表格左侧固定的js, 会影响取值重复，所以得加这个判断
						id_array.push($(this).val());
					}
				});
				if (id_array.length > 0) {
					total_count = id_array.length;
				}

				if(total_count > limit_size){
					swal({   
						title: "导出量过大,将自动分批导出",
						text: "导出过程中,当前窗口禁止进行任何操作,等待文件导出完毕！",
						type: "warning",   
						showCancelButton: true,   
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "确定",   
						closeOnConfirm: false, 
						animation: "slide-from-top", 
						showLoaderOnConfirm: true
					},
					function(){
						$('.sa-button-container .cancel').css('display', 'none');
						$('.sa-button-container .confirm').attr('disabled', 'disabled');
						$('.sa-button-container .confirm').html('导出中...');
						loopExport(total_count);
					});
				} else {
					loopExport(total_count);
				}
				$(this).dialog("close");
	        },
	        "取消": function () {
	            $(this).dialog("close");
	        }
	    },
	    close: function() {
	   		$(this).empty();
	    }
	});
	// 点击导出按钮
	$(".excelExport").click(function(){
		<?php session('export_status', 0); ?>

		$('#dialog-excelexport').dialog('open');
		$('#dialog-excelexport').load('<?php echo U("customer/selectexcelexport");?>');
	});

	// 循环导出
	function loopExport(total_count){
		// 判断是普通查询还是高级搜索
		var field = "<?php echo ($_GET['field']); ?>";
		// 勾选的列表数据
		var id_array = new Array();
		$("input.check_list:checked").each(function() {   
			if ($.inArray($(this).val(), id_array) == -1) {
				// 表格左侧固定的js, 会影响取值重复，所以得加这个判断
				id_array.push($(this).val());
			}
		});
		$.get(url,function(data){
			if(data.data == 0){
				// 显示导出进度
				var progress = '('+(times-1)*limit_size+'/'+total_count+')';
				$('.sa-button-container .confirm').html('导出中...'+progress);

				if((times-1)*limit_size < total_count){
					if (field) {
						$("#act_field").val('excel');	
						$("#daochu_field").val(id_array);
						$("#current_page_field").val(times);
						$("#export_limit_field").val(limit_size);
						$("#customer_search").submit();
					} else {
						$("#act").val('excel');	
						$("#daochu").val(id_array);
						$("#current_page").val(times);
						$("#export_limit").val(limit_size);
						$("#searchForm").submit();
					}		
					setTimeout("loopExport("+ total_count +")",1000); 
					times++; 
				}else{
					$("#act").val('');
					times = 1;
					$('.sa-button-container .confirm').attr('disabled', false);
					swal("数据导出成功！", "", "success");
				}
			}
		}, 'json');
	}
</script>
<?php echo W('Record');?>
	</div>
</div>
<script src="__PUBLIC__/js/animate/index.js"></script>
<script src="__PUBLIC__/js/animate/notification.js"></script>
<!-- layer -->
<script src="__PUBLIC__/js/layer/layer.js"></script>
<script src="__PUBLIC__/js/call/jquerysession.js"></script>

<!-- author by pdcrm -->
<script src="__PUBLIC__/style/js/inspinia.js"></script>
<?php
 $call_setting = C('CALL_SETTING'); $call_status = M('User')->where(array('role_id'=>session('role_id')))->getField('call_status'); $call_server_url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["SCRIPT_NAME"]; $call_server_url = str_replace('index.php','',$call_server_url); $config_call_record = M('Config')->where(array('name'=>'call_record'))->getField('value'); ?>
<?php if ($call_setting['CENTER'] == 1 && $call_status == 1): ?>
<script src="__PUBLIC__/js/call/socket.io-1.2.0.js"></script>
<script src="__PUBLIC__/js/call/call.js"></script>
<script type='text/javascript'>
$(document).ready(function(){
    var call_server_url = "<?php echo ($call_server_url); ?>";
    var call_bit = "<?php echo intval($call_setting['BIT']);?>";
    var call_upload = "<?php echo intval($call_setting['UPLOAD']);?>";
    var is_record = "<?php echo intval($config_call_record);?>";
    var sync_file_url = "<?php echo U('call/ossRecord');?>";
    if (!$.session.get('call_server_url')) {
        $.session.set('call_server_url', call_server_url);
    }
    if (!$.session.get('call_bit')) {
       $.session.set('call_bit', call_bit);
    }
    if (!$.session.get('call_upload')) {
        $.session.set('call_upload', call_upload);
    }
    if (!$.session.get('is_record')) {
        $.session.set('is_record', is_record);
    }
    if (!$.session.get('sync_file_url')) {
        $.session.set('sync_file_url', sync_file_url);
    }
});
</script>
<?php endif; ?>
<script type='text/javascript'>
$(document).ready(function(){
    /*复选框选择效果*/
    if($('.check_all').size() > 0){
        var icheck_num = 0;
        var all_check_num = 0;
        /*选择所有and取消所有*/
        $(".check_all").change(function(){
            $("input[class='check_list']").prop('checked', $(this).prop("checked"));//选中
            // if($(".check_all").prop("checked")){
            if($(this).is(":checked")){ //jQuery方式判断
                $(".single_btn").hide().children().each(function(){
                    $(this).attr('rel','');
                })
                $('#title-hide').show();
                $('#title-show').hide();
                all_check_num = $("input[class='check_list']:checked").length;
            }else{
                $('#title-hide').hide();
                $('#title-show').show();
                all_check_num = 0;
            }

            if(document.getElementById("oDivL_tab_Test3")){
                if ($(".check_all:checked").size()>0 && icheck_num % 2 == 0) {
                    icheck_num = all_check_num/2;
                    $("#icheck_num").text(all_check_num/2);
                } else {
                    icheck_num = all_check_num;
                    $("#icheck_num").text(all_check_num);
                }
            } else {
                icheck_num = all_check_num;
                // all_check_num = $("input[class='check_list']:checked").length;
                // icheck_num = all_check_num;
                $("#icheck_num").text(icheck_num);
            }
        });
        /*让隐藏标签显示*/
        $('.check_list').click(function(){
            var thiss = $(this);
            icheck_num = check_num();
            $("#icheck_num").text(icheck_num);
            //处理单选时才有的操作
            if (icheck_num == 1) {
                $(".single_btn").show().children().each(function(){
                    $(this).attr('rel',$("input.check_list:checked").val());
                    $('#log_customer').attr('rel1',$("input.check_list:checked").attr('rel'));

                    $('#log_business').attr('rel',$("input.check_list:checked").attr('rel'));
                    $('.business_btn').children().attr('rel1',$("input.check_list:checked").attr('rel'));

                    $('#examine_type').val($("input.check_list:checked").attr('rel'));
                    var next_examine_role = $("input.check_list:checked").parents('tr').find('.check_badge').attr('rel');
                    if($("input.check_list:checked").attr('rel1') == 1){
                        $('#to_top_span').html('&nbsp;取消置顶');
                        $('#to_check').html('<input type="hidden" class="is_checked" value="2">&nbsp;撤销');
                        if (next_examine_role == 1) {
                            $('#admin_examine').show();
                        } else {
                            $('#admin_examine').hide();
                        }
                        $('#user_span').html('<a id="delete" href="javascript:void(0)" onclick="del_user(2)"><i class="fa fa-check"></i>&nbsp;启用账号</a>');
                    }else{
                        if (next_examine_role == 1) {
                            $('#to_top_span').html('&nbsp;置顶');
                            var rel_name = $("input.check_list:checked").attr('rel3');
                            $('#to_check').html('<input type="hidden" class="is_checked" rel="' + rel_name + '" value="1">&nbsp;审核');
                            $('#not_admin').show();
                        } else {
                            $('#not_admin').hide();
                        }
                        $('#admin_examine').hide();
                    }
                    if ($("input.check_list:checked").attr('rel2') == 1) {
                        $('#call_span').html('<a href="javascript:void(0);" onclick="call_form(2)"><i class="fa fa-headphones"></i>&nbsp;停用呼叫中心</a>');
                    } else {
                        $('#call_span').html('<a href="javascript:void(0);" onclick="call_form(1)"><i class="fa fa-headphones"></i>&nbsp;启用呼叫中心</a>');
                    }

                    //进销存的审核、撤销
                    var do_examine = thiss.attr('do_examine');
                    var do_revoke = thiss.attr('do_revoke');
                    if (do_examine != 1) {
                        $('.do_examine').hide();
                    }
                    if (do_revoke != 1) {
                        $('.do_revoke').hide();
                    }
                    // 仓库禁用启用
                    if ($('.check_list:checked').attr('status') == 1) {
                        $('.edit_status[status="enable"]').parents('li').hide();
                        $('.edit_status[status="disable"]').parents('li').show();
                    } else if ($('.check_list:checked').attr('status') == 2) {
                        $('.edit_status[status="enable"]').parents('li').show();
                        $('.edit_status[status="disable"]').parents('li').hide();
                    } else {
                        $('.edit_status[status="enable"]').parents('li').hide();
                        $('.edit_status[status="disable"]').parents('li').hide();
                    }
                })
            } else {
                $(".single_btn").hide().children().each(function(){
                    $(this).attr('rel','');
                })
            }
            if(icheck_num <= 0){
                $('#title-hide').hide();
                $('#title-show').show();
            }else{
                $('#title-hide').show();
                $('#title-show').hide();
            }
        });
        /*点插 取消所有选中*/
        $('#back-show').click(function(){
            icheck_num = 0;
            $("#icheck_num").text(icheck_num);
            $('#title-hide').hide();
            $('#title-show').show();
            $(".check_list,.check_all").attr("checked",false);
        });

        var rows = ($('input[class="check_list"]').parents('tbody').find('tr').length / 2) - 1;
        function check_num () { 
            all_check_num = $("input[class='check_list']:checked").length;
            if (document.getElementById("oDivL_tab_Test3") && $('.check_all').is(':checked')) {
                all_check_num -= rows;
            }
            return all_check_num;
        }        
    }
    // $('form').on('keydown', function (e) { if (e.keyCode == 13) { return false; } });

    /*退出提示*/
    $('.logout').click(function () {
        swal({
            title: "确定退出登录?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "确定",
            cancelButtonText: "取消",
            closeOnConfirm: false
            },
            function (isConfirm) {
                if (isConfirm) {
                    window.location="<?php echo U('user/logout');?>";
                }
            }
        );
    });

    /*提交失败返回前一页*/
    var href = "<?php echo ($error); ?>";
    if(href != '' && href != null){
        swal({
            title: "添加失败!",type: "error",
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "返回",
            closeOnConfirm: false 
        }, 
        function(){
            location.href = 'javascript:history.back(-1)';
        });
    }

    //授权信息查询
    $('#authorize').click(function () {
        $.ajax({
            type: "POST",
            url: "<?php echo U('system/anthorize');?>",
            data:'',
            async: true,
            success: function(data) {
                if (data.status == 1) {
                    var url = "<?php echo U('system/anthorize');?>";
                    $('#Modal_anthorize').modal('show');
                    $('#anthorize_modal').load(url);
                } else {
                    alert_crm(data.info);
                }
            }
        });
    });
});

/*站内信*/
login_tips();
message_tips();

var login_show = 0;
function login_tips(){
    $.get("<?php echo U('message/logintips');?>", function(data){
        var is_login = data.data['is_login'];
        if(is_login == 0 && login_show != 1){
             $.ajax({
                type: "GET",
                url: "<?php echo U('user/loginajax');?>",
                async: true,
                success: function(data) {
                    if(data.status != 2){
                        $("#login_modal").parent().removeClass("modal-lg").addClass("modal-md");
                        $url = "<?php echo U('user/loginajax');?>";
                        $('#Modal_login').modal('show');
                        login_show = 1;
                        $('#login_modal').load($url);
                    }else{
                        login_show = 1;
                    }
                }  
            });
        }
    },'json')
    setTimeout('login_tips()',600000);
}

/*轮询*/
var mark_count = 1;//标记第一次弹出
function message_tips(){
    $.ajax({
        type: "get",
        url: "<?php echo U('message/tips');?>",
        async: true,
        success: function(data) {
            var is_lock = data.data['is_lock'];
             if(is_lock == 1){
                location.href = "<?php echo U('setting/lockscreen');?>";
            } else {
                //卡片提醒显示与隐藏
                var message_html = '';
                var new_num = data.data['message']+','+data.data['contract_count'];

                //待办事项
                $('#header_check_contract_num').html(data.data['check_contract_num']);
                $('#header_dqcontact_num').html(data.data['contract_num']);
                $('#header_examine_num').html(data.data['examine_num']);
                $('#header_receivables_num').html(data.data['receivingorder_num']);
                $('#receivables_num').html(data.data['receivables_num']);
                $('#header_follow_customer_num').html(data.data['today_customer']);
                $('#check_invoice_num').html(parseInt(data.data.check_invoice_num) > 0 ? data.data.check_invoice_num : '');
                // 进销存相关待办事项
                $('#check_purchase_num').html(parseInt(data.data.check_purchase_num) > 0 ? data.data.check_purchase_num : '');
                $('#check_purchase_return_num').html(parseInt(data.data.check_purchase_return_num) > 0 ? data.data.check_purchase_return_num : '');
                $('#check_sales_return_num').html(parseInt(data.data.check_sales_return_num) > 0 ? data.data.check_sales_return_num : '');
                $('#check_transfer_num').html(parseInt(data.data.check_transfer_num) > 0 ? data.data.check_transfer_num : '');
                // 待办总数
                $('#todo_num').html(parseInt(data.data.todo_num) > 0 ? data.data.todo_num : '');

                //导航提醒实时写入数值
                if(data.data['message'] != 0 && data.data['message'] != ''){
                    $('#message_group').show();
                    //桌面提醒
                    if(data.data['data_list']){
                        $(data.data['data_list']).each(function(k, v){
                            if (data.data['data_list_count'] < 3) {
                                animateMessage(v.role_info.thumb_path, v.role_info.full_name, v.content_msubstr);
                            }
                            aaa(v.role_info.thumb_path, v.role_info.full_name, v.content, v.url_link);
                        });
                    }
                    $('#message_num').html(data.data['message']);
                    $('#message_list').html('');
                    if(data.data['message_num'] != 0 && data.data['message_num'] != ''){
                        message_html += '<a href="<?php echo U('message/index');?>" style="width:100%;color:#676a6c;padding:5px !important;border-bottom:1px dashed #ddd;float:left;margin-bottom:5px;">\
                                    <div style="padding:0 10px;">\
                                        <img src="__PUBLIC__/img/wukong.png" title="小助手"> 您有<strong>'+data.data['message_num']+'</strong>条消息待查看\
                                        <span class="pull-right text-muted small" style="line-height:30px;">'+data.data['message_time']+'前</span>\
                                    </div>\
                                </a>';
                    }

                    if(data.data['message_announcement_count'] != 0 && data.data['message_announcement_count'] != ''){
                        message_html += '<a href="<?php echo U('message/index','by=announcement');?>" style="width:100%;color:#676a6c;padding:5px !important;border-bottom:1px dashed #ddd;float:left;margin-bottom:5px;">\
                                    <div style="padding:0 10px;">\
                                        <img src="__PUBLIC__/img/announcement.png" title="系统公告"> 您有<strong>'+data.data['message_announcement_count']+'</strong>条公告信息待查看\
                                        <span class="pull-right text-muted small" style="line-height:30px;">'+data.data['announcement_time']+'前</span>\
                                    </div>\
                                </a>';
                    }
                    if(data.data['role_message_list']){
                        $(data.data['role_message_list']).each(function(k, v){
                            message_html += '<a href="<?php echo U('message/message_view','to_role_id=');?>'+v.role_id+'" title="点击回复" style="width:100%;color:#676a6c;padding:5px !important;border-bottom:1px dashed #ddd;float:left;margin-bottom:5px;" >\
                                        <div class="dropdown-messages-box" style="padding:0 10px;">\
                                            <div class="pull-left">\
                                                <img alt="image" class="img-circle" src="'+v.thumb_path+'">&nbsp;\
                                            </div>\
                                            <div style="overflow:hidden;">\
                                                <span>'+v.full_name+'</span><span class="label label-warning pull-right" style="margin-right: 3px;border-radius:50% !important;">'+v.unread_num+'</span><br>\
                                                <span style="word-wrap: break-word;word-break: normal;float:left;margin-top:10px;width:100%;">'+v.content+'</span><br>\
                                                <small class="text-muted pull-right" style="margin-top: 5px;">'+v.send_time+'</small>\
                                            </div>\
                                        </div>\
                                    </a>';
                        });
                    }
                    $('#message_list').append(message_html);
                }else{
                    $('#message_group').hide();
                }
                //日程提醒
                var event_temp = '';
                if(data.data['event_list'] != 0 && data.data['event_list'] != ''){
                    $('#event_group').show();
                    $('#event_list').html('');
                    $('#event_num').html(data.data['event_num']);
                    $.each(data.data['event_list'], function(k, v){
                        event_temp += '<a href="<?php echo U('event/index');?>" title="点击查看" style="padding:5px !important;border-bottom:1px dashed #ddd;float:left;margin-bottom:5px;width:100%;">\
                                            <div style="overflow:hidden;padding:0 10px;">\
                                                <span class="pull-left" style="color:'+v.color+';line-height:26px;"><i class="fa fa-circle"></i>&nbsp;&nbsp;'+v.subject+'</span><br>\
                                                <small class="text-muted pull-right" style="margin-top: 5px;">'+v.between_date+'</small>\
                                            </div>\
                                        </a>';
                    });
                    $('#event_list').append(event_temp);
                }else{
                    $('#event_group').hide();
                }
            }
        },
        dataType:'json'
    });
    setTimeout('message_tips()',500000);
}

function aaa(icon, name, content, url_link){
    var t = new Date().toLocaleString();
    var options={
        dir: "ltr",
        lang: "utf-8",
        icon: icon,
        body: content
    };
    if(Notification && Notification.permission === "granted"){
        var n = new Notification(name + t, options);
        //5秒后自动关闭 
        n.onshow = function(){
            setTimeout(function () {
                n.close();
            }, 5000)
        };
        n.onclick = function() {
            window.location = url_link;
            n.close();
        };
        n.onclose = function(){
            console.log("notification closed!");
        };        
        n.onerror = function() {
            console.log("An error accured");
        }        
    }
}
    // overflow_hide
    // 时间选择禁用浏览器缓存
    $('input.Wdate').attr('autocomplete', 'off');
</script>
</body>
</html>