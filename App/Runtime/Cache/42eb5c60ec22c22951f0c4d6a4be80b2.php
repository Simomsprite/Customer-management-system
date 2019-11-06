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

<!-- nice-scroll -->
<script src="__PUBLIC__/style/js/plugins/nice-scroll/jquery.nicescroll.min.js" type="text/javascript"></script>
<script type="text/javascript" src="__PUBLIC__/style/js/TableFreeze.js"></script>
<script src="__PUBLIC__/js/pdcrm_more.js" type="text/javascript"></script>
<style>
	body{
		overflow-y: hidden;
	}
	.option{padding-left:-30px;}
	#right-sidebar-log{
		width: 50% !important;
		right: -60%;
		background-color: #fff;
	    overflow: hidden;
	    position: fixed;
	    top: 60px;
	    z-index: 1009;
	    bottom: 0;
	    box-shadow: 0px 2px 1px #888888;
	}
</style>
<script>
$(function(){
	var scroll_width = 10;
	$("#table_div").height(window.innerHeight-$("#table_div").offset().top-$("#tfoot_div").height()-parseInt($("#table_container").css("padding-bottom").replace("px",""))-10);
	$(window).resize(function(){
		$("#table_div").height(window.innerHeight-$("#table_div").offset().top-$("#tfoot_div").height()-parseInt($("#table_container").css("padding-bottom").replace("px",""))-10);
		$("#oDivL_tab_Test3").height($("#table_div").height()-scroll_width-1).width($("#oTableLH_tab_Test3").width());
		$("#oDivH_tab_Test3").height($("#oTableLH_tab_Test3").height()).width($("#table_div").width()-scroll_width);
	});
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
	if ($('#tab_Test3').length > 1) {
		$("#tab_Test3").FrozenTable(1, 0, 3);
	}
	$("#oDivL_tab_Test3").height($("#table_div").height()-scroll_width).width($("#oTableLH_tab_Test3").width());
	$("#oDivH_tab_Test3").height($("#oTableLH_tab_Test3").height()).width($("#table_div").width()-scroll_width);
	//$("#oDivH_tab_Test3").css('top','-7px');
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
							<li class="single_btn"><a href="javascript:void(0)" id="edit_log"><i class="fa fa-pencil"></i>&nbsp;编辑</a></li>
							<li><a id="delete" href="javascript:void(0)"><i class="fa fa-times"></i>&nbsp;删除</a></li>
							<li class="last_li"><big><a class="fa fa-times pull-right" id="back-show"></a></big></li>
						</ul>
					</div>
					<div class="row " id="title-show">
						<ul class="nav pull-left" style="margin:2px 0 0 15px;">
							<a href="<?php echo U('log/mylog_add');?>" class="btn btn-primary btn-sm pull-left" style="margin-right:8px"><i class="fa fa-plus-circle"></i>&nbsp; 新建日报</a>
						</ul>
						<form class="form-inline" id="" action="" method="get">
							<ul class="breadcrum pull-right" style="margin-bottom: 0px">
								<li>
									<span>搜索员工：</span>
									<div class="input-group" style="margin-top: -6px; width: 80px;">
										<select class="form-control select2 hidden" name="role_id" id="role_id" onchange="window.open(this.options[this.selectedIndex].value,target='_self')">
											<option value="">--请选择--</option>
											<?php if(is_array($role_list)): $i = 0; $__LIST__ = $role_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo U('log/index','&role_id='.$vo['role_id'].'&'.$parameter);?>" <?php if($_GET['role_id'] == $vo['role_id']): ?>selected<?php endif; ?>><?php echo ($vo['full_name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>	
										</select>
									</div>
								</li>
								<li>
									<span style="">类型：</span>
									<div class="input-group" style="margin-top: -6px;">
										<select class="form-control" onchange="window.open(this.options[this.selectedIndex].value,target='_self')" >
											<option value="<?php echo U('log/index');?>" <?php if($_GET['type'] == ''): ?>selected="selected"<?php endif; ?>>全部</option>
											<option value="<?php echo U('log/index','type=4');?>" <?php if($_GET['type'] == 4): ?>selected="selected"<?php endif; ?>>日报</option>
											<option value="<?php echo U('log/index','type=3');?>" <?php if($_GET['type'] == 3): ?>selected="selected"<?php endif; ?>>周报</option>
											<option value="<?php echo U('log/index','type=2');?>" <?php if($_GET['type'] == 2): ?>selected="selected"<?php endif; ?>>月报</option>
										</select>
									</div>
								</li>
								<li>
									<div class="input-group">
										<input type="hidden" name="m" value="log">
										<input type="hidden" name="field" value="subject"/>
										<input id="short_search" type="text" style="width:160px;" placeholder="请输入标题或内容" onkeydown='if(event.keyCode==13) {$("#short_search_btn").trigger("click");return false;}' class="form-control input-sm" name="search" <?php if($_GET['field'] == 'subject'): ?>value="<?php echo ($_GET['search']); ?>"<?php endif; ?>/>
										<span class="input-group-btn">
											<button class="btn btn-default btn-search" id="short_search_btn" type="submit">
												<i class="fa fa-search"></i>
											</button>
										</span>
									</div>
									<a title="高级搜索" href="javascript:void(0)" id="search_type" class="btn btn-white btn-bitbucket" style="margin: -6px 0 0 10px;">
										<i class="fa fa-filter" style="color: #D8E3EF;"></i>
									</a>
								</li>
							</ul>
						</form>
					</div>
				</div>
				<div class="row" style="margin: 0px">
					<div class="ibox-content" style="padding:0px;">
						<div class="ibox-content clearfix" id="table_container" style="padding:0px;border: none;z-index: 1;">
							<input type="hidden" id="is_show" value="0"/>
							<form id="form1" action="" method="Post" style="position:relative;"> 
								<div id="table_div" class="nicescroll" style="left:0px;top:0px;">
								<?php if($list == null): ?><div style="background-color:#fff;"><div style="text-align:center;color:#E4E4E4;font-size:22px;font-weight:700;padding:15px 0px;">
	<img src="__PUBLIC__/img/exclamation_mark.png" style="margin-top:-3px;">&nbsp;&nbsp;暂无数据
</div></div>
								<?php else: ?>
									<table class="table table-hover table-striped table_thead_fixed" id="tab_Test3">
										<tr class="tabTh" >
											<td style="width:80px;">
												<div class="checkbox checkbox-primary">
													<input type="checkbox" class="check_all"/>
													<label for=""></label>
												</div>
											</td>
											<td><?php echo L('SUBJECT');?></td>
											<td><?php echo L('ROLE_NAME');?></td>
											<td>类型</td>
											<td>附件</td>
											<td>概要</td>
											<td>点评状态</td>
											<td>
												<?php if($_GET['desc_order'] == 'create_date'): ?><a href="<?php echo U('log/index','asc_order=create_date&'.$parameter);?>" title="点击按时间正序排列">
														<?php echo L('CREATE_TIME');?>&nbsp;<span class="fa fa-caret-down"></span>
													</a>
												<?php elseif($_GET['asc_order'] == 'create_date'): ?>
													<a href="<?php echo U('log/index','desc_order=create_date&'.$parameter);?>"  title="点击按时间倒序排列">
														<?php echo L('CREATE_TIME');?>&nbsp;<span class="fa fa-caret-up"></span>
													</a>
												<?php else: ?>
													<a href="<?php echo U('log/index','asc_order=create_date&'.$parameter);?>" title="点击按时间正序排列"><?php echo L('CREATE_TIME');?>&nbsp;<span class="fa fa-caret-down"></a><?php endif; ?>
											</td>
										</tr>
										<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="controls_tr">
												<td style="width:80px;">
													<div class="checkbox checkbox-primary">
														<input name="log_id[]" class="check_list" type="checkbox" value="<?php echo ($vo["log_id"]); ?>"/>
														<label for=""></label>
													</div>
												</td>
												<td>
													<a class="right-sidebar-toggle-log" href="javascript:void(0)" rel="<?php echo ($vo['log_id']); ?>">
														<?php if($vo['subject'] ): echo ($vo["subject"]); ?>
														<?php else: ?>
															<?php echo (msubstr($vo['content'],0,15)); endif; ?>
													</a>
												</td>
												<td>
													<a class="role_info" rel="<?php echo ($vo["creator"]["role_id"]); ?>" href="javascript:void(0)"><?php echo ($vo['creator']['full_name'] ?: $vo['creator']['login_name']); ?></a>
												</td>
												<td><?php echo ($vo['category_name']); ?></td>
												<td class="">
													<?php if($vo['file_count']): ?><i class="fa fa-paperclip"></i>&nbsp;(<?php echo ($vo['file_count']); ?>)<?php endif; ?>
												</td>
												<td><?php echo (msubstr($vo['content'],0,35)); ?></td>
												<td>
													<?php if($vo['status'] == 2): ?><span style="border: 1px solid #00D842;padding: 2px 10px;border-radius: 10px;color:#00D842;">已点评</span>
													<?php else: ?>
														<span style="border: 1px solid #FFA856;padding: 2px 10px;border-radius: 10px;color:#FFA856;">未点评</span><?php endif; ?>
												</td>
												<td><?php echo (date("Y-m-d H:i",$vo["create_date"])); ?></td>
											</tr><?php endforeach; endif; else: echo "" ;endif; ?>
									</table><?php endif; ?>
								</div>
								<div id="tfoot_div" class="clearfix">
									<div class="clearfix" id="tfoot_page">	
										<?php if($fields_search || $_GET['field']): ?><span class="pull-left" style="margin-left:25px;margin-top:10px;">本次搜索结果
												<span style="color:#F8AC59"> <?php echo ($count); ?> </span>条数据
												<a href="<?php echo U('log/index');?>" class="btn" style="background:#fff;border:1px solid #ccc;margin-left:10px;color:#999;"
												 id="clearnumber">清除搜索条件</a>
											</span><?php endif; ?>
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
    </div>
</div>
<link href="__PUBLIC__/css/litebox.css" rel="stylesheet" type="text/css">
<script src="__PUBLIC__/js/litebox.min.js" type="text/javascript"></script>
<script src="__PUBLIC__/js/images-loaded.min.js" type="text/javascript"></script>
<div style="display:none;" id="dialog-role-info" title="<?php echo L('DIALOG_USER_INFO');?>">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<div style="display:none" id="dialog-field-search" title="高级搜索">
	<form class="form-inline" id="searchForm" action="" method="get">
		<div id="search_add" style="width:650px;float:left;">
			<input type="hidden" name="m" value="log" />
			<input type="hidden" name="a" value="index" />
			<input type="hidden" name="search" value="true" />
			<input type="hidden" name="listrows" value="<?php echo ($_GET['listrows']); ?>"/>
			<?php if(empty($fields_search)): ?><div id="con_search_1" style="float:left;width:650px;margin:0 10px 0 10px;">
					<ul class="nav pull-left" style="margin:0px 0 0 23px;width:650px;">
						<li class="pull-left">
							<select id="field_1" style="width:auto" class="form-control input-sm field_name new-select" onchange="changeCondition(1)">
								<option class="text" value="subject" rel="log">标题</option>
								<option class="text" value="content" rel="log">内容</option>
								<option class="role" value="role_id" rel="log">作者</option>
								<option class="date" value="create_date" rel="log">创建时间</option>
								<option class="box" value="category_id" rel="log">类型</option>
							</select>&nbsp;&nbsp;
						</li>
						<script>
							$(function() { $('#field_1').change(); })
						</script>
						<li class="pull-left" id="conditionContent_1"></li>
						<li class="pull-left" id="searchContent_1"></li>
					</ul>
				</div>
				<?php $max_key = 1;?>
			<?php else: ?>
				<?php if(is_array($fields_search)): $key1 = 0; $__LIST__ = $fields_search;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key1 % 2 );++$key1;?><div id="con_search_<?php echo ($key1); ?>" style="float:left;width:650px;margin:10px 10px 0 10px;">
						<div id="rem_<?php echo ($key1); ?>" class="pull-left" style="line-height:30px;">
							<a href="javascript:void(0);" class="rem_search" rel="<?php echo ($key1); ?>" title="移除">
								<span class="fa fa-times-circle"></span>
							</a>
						</div>&nbsp;
						<ul class="nav pull-left" style="margin:0px 0 0 5px;width:620px;">
							<li class="pull-left">
								<select id="field_<?php echo ($key1); ?>" style="width:160px" class="form-control input-sm field_name" onchange="changeCondition(<?php echo ($key1); ?>)">
									<option class="role" value="role_id" rel="log"
									<?php if($vo['field'] == role_id): endif; ?>>作者</option>
									<option class="text" value="subject" rel="log" <?php if($vo['field'] == subject): endif; ?>>标题</option>
									<option class="text" value="content" rel="log" <?php if($vo['field'] == subject): endif; ?>>内容</option>
									<option class="date" value="create_date" rel="log" <?php if($vo['field'] == create_date): endif; ?>>创建时间</option>
									<option class="box" value="category_id" rel="log" <?php if($vo['field'] == type): endif; ?>>类型</option>
								</select>&nbsp;&nbsp;
							</li>
							<script>
								$(function(){
									$('#field_<?php echo ($key1); ?>').val('<?php echo ($vo["field"]); ?>');
									$('#field_<?php echo ($key1); ?>').change();
									if ('<?php echo ($vo["field"]); ?>' == 'role_id' || '<?php echo ($vo["field"]); ?>' == 'category_id') {
										$('#conditionContent_<?php echo ($key1); ?>').find('select').val('<?php echo ($vo["condition"]); ?>');
										$('#searchContent_<?php echo ($key1); ?>').find('select').val('<?php echo ($vo["value"]); ?>');
										$('#searchContent_<?php echo ($key1); ?>').find('select').change();
									} else if ('<?php echo ($vo["field"]); ?>' == 'create_date') {
										$('#searchContent_<?php echo ($key1); ?>').find('#start_<?php echo ($key1); ?>').val('<?php echo ($vo["start"]); ?>');
										$('#searchContent_<?php echo ($key1); ?>').find('#end_<?php echo ($key1); ?>').val('<?php echo ($vo["end"]); ?>');
									} else if ('<?php echo ($vo["field"]); ?>' == 'subject' || '<?php echo ($vo["field"]); ?>' == 'content') {
										$('#conditionContent_<?php echo ($key1); ?>').find('select').val('<?php echo ($vo["condition"]); ?>');
										$('#conditionContent_<?php echo ($key1); ?>').find('select').change();
										$('#searchContent_<?php echo ($key1); ?>').find('input').val('<?php echo ($vo["value"]); ?>');
									}
								});
							</script>
							<li class="pull-left" id="conditionContent_<?php echo ($key1); ?>"></li>
							<li class="pull-left" id="searchContent_<?php echo ($key1); ?>" style="margin-left:5px;"></li>
						</ul>
					</div>
					<?php $max_key = $key1; endforeach; endif; else: echo "" ;endif; endif; ?>
		</div>
		<div class="clearfix"></div>
		<div style="margin-left: 35px;margin-top:10px;">
			<a href="javascript:void(0);" style="display: -moz-stack;margin: 30px 0px 0px; font-size: 12px; color: rgb(62, 133, 233);"
			 id="add_btn">+添加筛选条件</a>
		</div>
	</form>
</div>
<script type="text/javascript">
/*让复选框默认取消选择*/
$(':checkbox').prop('checked', false);
$("#dialog-role-info").dialog({
    autoOpen: false,
    modal: true,
	width: 600,
	maxHeight: 550,
	position: ["center",100]
});
$(function(){
	$('#delete').click(function(){
		var id_array = new Array();
		$("input.check_list:checked").each(function(){  
			id_array.push($(this).val());
		});
		if(id_array.length == 0){
			alert_crm('您没有选中任何日志！');
			return false;
		}
		swal({
			title: "您确定要删除日志信息吗？",
   			text: "删除后将无法恢复，请谨慎操作！",
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
		            url: "<?php echo U('log/log_delete');?>",
		            data: {log_id: id_array},
		            async: false,
		            success: function (data) {
						if (data.status == 1) {
							swal("删除成功！", "您已经永久删除了信息！", "success");
							location.reload();
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
	        } else {
	            swal("已取消","您取消了删除操作！","error");
	        }
	    });
	});

	$(".role_info").click(function(){
		$role_id = $(this).attr('rel');
		$('#dialog-role-info').dialog('open');
		$('#dialog-role-info').load('<?php echo U("user/dialoginfo","id=");?>'+$role_id);
	});
})
function changeContent(){
	$module = $('#module').val();
	if($module == 6){
		window.location = "<?php echo U('Log/index','by='.$_GET['by'].'&type=4');?>";
	}else if($module == 1){
		window.location = "<?php echo U('Log/index','r=rCustomerLog&module=customer&by='.$_GET['by'].'&type=1');?>";
	}else if($module == 2){
		window.location = "<?php echo U('Log/index','r=rContactsLog&module=contacts&by='.$_GET['by'].'&type=1');?>";
	}else if($module == 3){
		window.location = "<?php echo U('Log/index','r=rBusinessLog&module=business&by='.$_GET['by'].'&type=1');?>";
	}else if($module == 4){
		window.location = "<?php echo U('Log/index','r=rLogTask&module=task&by='.$_GET['by'].'&type=1');?>";
	}else if($module == 5){
		window.location = "<?php echo U('Log/index','r=rEventLog&module=event&by='.$_GET['by'].'&type=1');?>";
	}
}
<?php if($_GET['field']!= null): ?>$("#field option[value='<?php echo ($_GET['field']); ?>']").prop("selected", true);changeCondition();
	$("#condition option[value='<?php echo ($_GET['condition']); ?>']").prop("selected", true);changeSearch();
	$("#search").prop('value', '<?php echo ($_GET['search']); ?>');<?php endif; ?>
	$(function(){
		$("#edit_log").click(function(){
			window.location.href="<?php echo U('log/mylog_edit','&id=');?>"+$(this).attr('rel');
		})
	});
	/*日志详情 加载的圈圈效果*/
	var detail_html = '<div class="spiner-example">\
						<div class="sk-spinner sk-spinner-fading-circle">\
							<div class="sk-circle1 sk-circle"></div>\
							<div class="sk-circle2 sk-circle"></div>\
							<div class="sk-circle3 sk-circle"></div>\
							<div class="sk-circle4 sk-circle"></div>\
							<div class="sk-circle5 sk-circle"></div>\
							<div class="sk-circle6 sk-circle"></div>\
							<div class="sk-circle7 sk-circle"></div>\
							<div class="sk-circle8 sk-circle"></div>\
							<div class="sk-circle9 sk-circle"></div>\
							<div class="sk-circle10 sk-circle"></div>\
							<div class="sk-circle11 sk-circle"></div>\
							<div class="sk-circle12 sk-circle"></div>\
						</div>\
					</div>';

	//鼠标点击空白处，隐藏右侧滑出
	document.onmousedown=function(event){
		if($(".litebox-overlay").length > 0) return;
		if(event.target.className == 'right-sidebar-toggle-log') return;

	    var div = document.getElementById("right-sidebar-log");
	    var x=event.clientX;
	    var y=event.clientY;
	    var divx1 = div.offsetLeft;
	    var divy1 = div.offsetTop;
	    var divx2 = div.offsetLeft + div.offsetWidth;
	    var divy2 = div.offsetTop + div.offsetHeight;
	    if( x < divx1 || x > divx2 || y < divy1 || y > divy2){
	        if($("#right-sidebar-log").css('right') == '0px'){
	            $("#right-sidebar-log").animate({right:'-60%'}, 400);
	            //$('#is_show').val('0');
	        }
	    }
	}
	//日志详情弹出
	$(document).on('click','.right-sidebar-toggle-log',function(){
		var log_id = $(this).attr('rel');
		// var is_show = document.getElementById("is_show").value;
		$(".emoji_container").remove();


        $(".check_all").attr("checked",false);
		$('input[name="log_id[]"]').each(function(){
            if ($(this).val() == log_id && $(this).is(":checked") == false) {
				$(this).click();
			} else if ($(this).val() != log_id && $(this).is(":checked")){
                $(this).click();
            }
		}); 

		if($("#right-sidebar-log").css('right') != '0px'){
			$("#right-sidebar-log").animate({right:'0px'}, 600);
		}
			
	    $('#log-content').html(detail_html);
	    $('#sidebar-container').load("<?php echo U('log/viewAjax','log_id=');?>"+log_id);
		
	});

	$("#dialog-field-search").dialog({
		autoOpen: false,
		modal: true,
		width: 700,
		minHeight: 350,
		maxHeight: 450,
		position: ["center", 100],
		buttons: {
			"确定": function () {
				doh();
				if (dosearch == 1) {
					$(this).dialog("close");
				}
			},
			"取消": function () {
				$(this).dialog("close");
			}
		}
	});
	// 筛选重复判断
	var dosearch = 1;
	function doh() {
		var ary = new Array();
		var field_name = '';
		var is_submit = 1;
		$('.field_name').each(function (k, v) {
			field_name = $(this).find("option:selected").val();
			if (jQuery.inArray(field_name, ary) >= 0) {
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
		if (is_submit == 1) {
			$("#searchForm").submit();
		}
	}

	/*添加筛选条件*/
	var m = <?php echo ($max_key); ?>;
	$('#add_btn').click(function () {
		m += 1;
		$('#search_add').append('<div id="con_search_' + m + '" style="float:left;width:650px;margin:10px 10px 0 10px;"><div id="rem_' + m + '" class="pull-left" style="line-height:30px;"><a href="javascript:void(0);" class="rem_search" rel="' + m + '" title="移除"><span class="fa fa-times-circle"></span></a></div><ul class="nav pull-left" style="margin:0 0 0 5px; width:600px;"><li class="pull-left"><select id="field_' + m + '" style="width:auto" class="form-control input-sm field_name new-select" onchange="changeCondition(' + m + ')"><option class="text" value="subject" rel="log">标题</option><option class="text" value="content" rel="log">内容</option><option class="role" value="role_id" rel="log">作者</option><option class="date" value="create_date" rel="log">创建时间</option><option class="box" value="category_id" rel="log">类型</option></select>&nbsp;&nbsp;</li><li class="pull-left" id="conditionContent_' + m + '"></li><li class="pull-left" id="searchContent_' + m + '"></li></ul></div>');
		$('#field_' + m ).change();
	});
	$(document).on('click', '.rem_search', function () {
		var num = $(this).attr('rel');
		$('#con_search_' + num).remove();
	});
	
	$(function () {
		$('#search_type').on('click', function () {
			$("#dialog-field-search").dialog('open');
		});
	});
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
<div id="right-sidebar-log">
	<!--the css for jquery.mCustomScrollbar-->
	<link rel="stylesheet" href="__PUBLIC__/emoji/css/jquery.mCustomScrollbar.min.css"/>
	<!--the css for this plugin-->
	<link rel="stylesheet" href="__PUBLIC__/emoji/css/jquery.emoji.css"/>
	<!--(Optional) the js for jquery.mCustomScrollbar's addon-->
	<script src="__PUBLIC__/emoji/js/jquery.mousewheel-3.0.6.min.js"></script>
	<!--the js for jquery.mCustomScrollbar-->
	<script src="__PUBLIC__/emoji/js/jquery.mCustomScrollbar.min.js"></script>
	<!--the js for this plugin-->
	<script src="__PUBLIC__/emoji/js/jquery.emoji.js"></script>

	<div class="sidebar-container" id="sidebar-container">
		
	</div>
</div>