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

<script type="text/javascript" src="__PUBLIC__/js/PCASClass.js" ></script>
<style>
    .a{
        display:block;
    }
    .form-p{line-height: 25px;height: 25px;}
    .form-p-owner{line-height: 25px;height: 25px;padding-top: 5px;font-size: 14px;color: #000;}
    .product-a-line{border-left: 3px solid #19aa8d !important;}
    .product-list:hover{background-color: #f3f3f4;}
    .all_business{background-color: #fff;color: #00aaef;}
    .all_business_a{background-color: #00aaef;color: #fff !important;}
    .form-horizontal .control-label{color: #999;}
    .modal-backdrop{z-index:-1 !important;}
</style>
<div class="wrapper wrapper-content animated fadeIn" >
    <div class="ibox-content" style="padding-top:9px;padding-bottom:4px;">
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
		<input type="hidden" name="content" id="content" value="<?php echo ($content); ?>">
        <input type="hidden" id="bid" value="<?php echo ($_GET['bid']); ?>">
        <div class="row border-bottom">
            <div class="col-md-9">
                <div class="all-inline">
                    <span><img src="__PUBLIC__/img/customer_view_icon.png" style="margin-bottom:9px;" alt=""></span>
                    <!-- <h2 class="h2-customer-name" style="font-weight:400;color: #000;"> -->
                    <span class="h2-customer-name" title="<?php echo ($customer['name']); ?>" style="overflow: hidden;white-space: nowrap;text-overflow: ellipsis; font-size: 2rem; font-weight:600;color: #555; line-height: 21px; max-width: 500px;">
                        <?php echo ($customer['name']); ?>
                    </span>
                    <!-- </h2> -->
                    <span style="font-size:20px">
					<?php if(checkPerByAction('customer','customerlock')): ?><a style="margin-left:10px;" href="<?php echo U('customer/customerlock','customer_id='.$customer['customer_id']);?>">
                        <?php if($customer['is_locked']): ?><img  title="<?php echo L('UNLOCK_TITLE');?>" src="__PUBLIC__/img/locking.png"/>
                        <?php else: ?>
                            <img title="<?php echo L('LOCK_TITLE');?>" src="__PUBLIC__/img/unlocking.png"/><?php endif; ?>
                        </a><?php endif; ?>
                    </span>
					<?php if($share_num != 1 && $content != 'resource'): ?><a href="javascript:void(0);" id="share_list" rel="<?php echo ($customer['customer_id']); ?>" style="color:#ffb173"><i class="fa fa-share"></i>当前共享给<span id="share_count"><?php if($share_counts): echo ($share_counts); else: ?>0<?php endif; ?></span>名成员</a><?php endif; ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="btn-group pull-right">
                    <button data-toggle="dropdown" class="btn btn-outline btn-default dropdown-toggle" aria-expanded="false">操作 <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo U('customer/edit','id='.$customer['customer_id']);?>" style="margin-right: 15px;"><i class="fa fa-pencil"></i>&nbsp;&nbsp;编辑</a></li>
						<?php if($content != 'resource'): ?><li><a href="javascript:void(0);" id="transfer_name" rel="<?php echo ($customer['customer_id']); ?>" style="margin-right: 15px;"><i class="fa fa-exchange"></i>&nbsp;&nbsp;转移</a></li><?php endif; ?>
                        <li>
                            <a href="javascript:void(0);" id="remind" rel="<?php echo ($customer['customer_id']); ?>">
                               <i class="fa fa-bell-o"></i>&nbsp;&nbsp;添加提醒 
                            </a>
                        </li>
                        <li>
                            <?php if($invoice_info == ''): ?><a class="add_invoice" href="javascript:void(0);">
                                    <i class="fa fa-plus-circle"></i>&nbsp;&nbsp;添加发票
                                </a>
                            <?php else: ?>
                                <a class="view_invoice" rel="<?php echo ($invoice_info['id']); ?>" href="javascript:void(0);">
                                    <i class="fa fa-bookmark"></i>&nbsp;&nbsp;查看发票
                                </a><?php endif; ?>
                        </li>
                    </ul>
                </div>
                <div class="pull-right" style="padding-right: 15px;">
                    <?php if($customer['remind_info']): ?><a href="javascript:void(0);" id="remind_view" rel="<?php echo ($customer['customer_id']); ?>" title="查看提醒">
                            <span class="fa fa-bell-o" style="font-size:16px;color:orange;line-height: 32px;"></span>
                        </a><?php endif; ?>
                </div>
            </div>
        </div>
        <div class="row" style="padding-top: 20px;">
            <div class="col-md-9">
            <input type="hidden" id="contacts_id" value="<?php echo ($customer['contacts_id']); ?>" />
                <form role="form" class="form-horizontal view-group" method="post">
                    <div class="form-group">
                        <label class="col-lg-2 control-label">客户名称</label>
                        <div class="col-lg-4">
                            <p class="form-p color-a-edit">
                                <span title="<?php echo ($customer['name']); ?>" style="cursor:pointer; overflow: hidden; white-space: nowrap;text-overflow: ellipsis; display:block; width: 200px; max-width: 500px;">
                                    <?php echo ($customer['name']); ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-lg-3 hide">
                            <input type="text" class="form-control" name="name" value="<?php echo ($customer['name']); ?>" id="customer_name">
                        </div>
                        <div class="col-lg-1 hide"></div>
                        <label class="col-lg-2 control-label">联系人姓名</label>
                        <div class="col-lg-4">
                            <p class="form-p">
                                <span style="cursor:pointer"><?php echo ($customer['contacts_name']); ?></span><a href="javascript:void(0);" class="fa fa-pencil pencil-size field-edit hide"></a>
                            </p>
                        </div>
                        <div class="col-lg-3 hide">
                            <input type="text" class="form-control" name="contacts_name" value="<?php echo ($customer['contacts_name']); ?>" id="">
                        </div>
                        <div class="col-lg-1 hide"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label"><?php echo ($field['customer_code']); ?></label>
                        <div class="col-lg-4">
                            <p class="form-p">
                                <span style="cursor:pointer"><?php echo ($customer['customer_code']); ?></span><a href="javascript:void(0);" class="fa fa-pencil pencil-size field-edit hide"></a>
                            </p>
                        </div>
                        <div class="col-lg-3 hide">
                            <input type="text" class="form-control" name="customer_code" value="<?php echo ($customer['customer_code']); ?>" id="">
                        </div>
                        <div class="col-lg-1 hide"></div>

                        <label class="col-lg-2 control-label">联系电话</label>
                        <div class="col-lg-4">
                            <p class="form-p">
                                <span style="cursor:pointer"><?php echo ($customer['contacts_telephone']); ?></span><a href="javascript:void(0);" class="fa fa-pencil pencil-size field-edit hide"></a>
                            </p>
                        </div>
                        <div class="col-lg-3 hide">
                            <input type="text" class="form-control" name="contacts_phone" value="<?php echo ($customer['contacts_telephone']); ?>" id="" />
                        </div>
                        <div class="col-lg-1 hide"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">客户等级</label>
                        <div class="col-lg-4">
                            <p class="form-p">
                                <!-- 星星 -->
                                <?php $start = $customer['grade']+1; $end = 6-$customer['grade']; ?>
                                <span style="cursor:pointer;color:#D0D0D0;">
                                    <?php $__FOR_START_16148__=1;$__FOR_END_16148__=$start;for($i=$__FOR_START_16148__;$i < $__FOR_END_16148__;$i+=1){ ?><i class="fa fa-star star-orange"></i>&nbsp;<?php } $__FOR_START_9149__=1;$__FOR_END_9149__=$end;for($i=$__FOR_START_9149__;$i < $__FOR_END_9149__;$i+=1){ ?><i class="fa fa-star"></i>&nbsp;<?php } ?>
                                </span>
                                <a href="javascript:void(0);" class="fa fa-pencil pencil-size field-edit hide"></a>
                            </p>
                        </div>
                        <div class="col-lg-3 hide">
							<select name="grade" class="form-control" id="edit-grade">
								<option value="1">一星</option>
								<option value="2">二星</option>
								<option value="3">三星</option>
								<option value="4">四星</option>
								<option value="5">五星</option>
							</select>
                        </div>
                        <div class="col-lg-1 hide"></div>
                        <label class="col-lg-2 control-label">尊称</label>
                        <div class="col-lg-4">
                            <p class="form-p">
                                <span style="cursor:pointer"><?php echo ($customer['contacts_saltname']); ?></span>
                                <a href="javascript:void(0);" class="fa fa-pencil pencil-size field-edit hide"></a>
                            </p>
                        </div>
                        <div class="col-lg-3 hide">
                            <div class="radio radio-info radio-inline" style="margin-left: 20px;">
                                <input type="radio" name="contacts_saltname" id="saltname" <?php if($customer['contacts_saltname'] == '先生'): ?>checked<?php endif; ?> value="先生">
                                <label for="saltname">先生</label>
                            </div> &nbsp; 
                            <div class="radio radio-info radio-inline">
                                <input type="radio" name="contacts_saltname" id="saltname1" <?php if($customer['contacts_saltname'] == '女士'): ?>checked<?php endif; ?> value="女士">
                                <label for="saltname1">女士</label>
                            </div>
                        </div>
                        <div class="col-lg-1 hide"></div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">客户负责人</label>
                        <div class="col-lg-4">
                            <p class="form-p-owner" style="margin-bottom: 0px;">
                                <a class="role_info" rel="<?php echo ($customer['owner']['role_id']); ?>" href="javascript:void(0)"><?php echo ($customer['owner']['full_name']); ?></a>
                            </p>
                        </div>
                        <label class="col-lg-2 control-label">创建时间</label>
                        <div class="col-lg-4">
                            <p class="form-p-owner" style="margin-bottom: 0px;"><?php echo (date("Y-m-d H:i:s",$customer['create_time'])); ?></p>
                        </div>
                    </div>
                    <div id="list-show" style="display:none;" rel="false">
                        <?php if(is_array($field_list)): $k = 0; $__LIST__ = $field_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k; if($vo['form_type'] == 'datetime' && $customer[$vo['field']] != 0){ $customer[$vo['field']] = date('Y-m-d H:i', $customer[$vo['field']]); }elseif ($vo['form_type'] == 'datetime' && $customer[$vo['field']] == 0) { $customer[$vo['field']] = ' '; } ?>
							<?php if ($k%2 == 0): ?>
								<label class="col-lg-2 control-label"><?php echo ($vo['name']); ?></label>
								<div class="col-lg-4">
									<?php if ($vo['field'] != null): ?>
									<p class="form-p" <?php if($vo['form_type'] == 'textarea'): ?>style="word-break:break-all;height:auto;"<?php endif; ?>>
										<span style="cursor:pointer;color:#<?php echo ($vo['color']); ?>"><?php echo ($customer[$vo['field']]); ?></span><a href="javascript:void(0);" class="fa fa-pencil pencil-size field-edit hide"></a>
									</p>
									<?php endif; ?>
								</div>
								<div class="col-lg-3 hide">
									<?php echo ($vo['html']); ?>
								</div>
								<div class="col-lg-1 hide"></div>                            
							</div>
							<?php else: ?>
							<div class="form-group">
								<label class="col-lg-2 control-label"><?php echo ($vo['name']); ?></label>
								<div class="col-lg-4">    
									<p class="form-p" <?php if($vo['form_type'] == 'textarea'): ?>style="word-break:break-all;height:auto;"<?php endif; ?>>
										<span style="cursor:pointer;color:#<?php echo ($vo['color']); ?>"><?php echo ($customer[$vo['field']]); ?></span><a href="javascript:void(0);" class="fa fa-pencil pencil-size field-edit hide"></a>
									</p>
								</div>
								<div class="col-lg-3 hide">
									<?php echo ($vo['html']); ?>
								</div>
								<div class="col-lg-1 hide"></div>                            
							<?php endif; endforeach; endif; else: echo "" ;endif; ?>
                    </div>
                </form>
            </div>
            <div class="col-md-3">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 ">
                <div class="text-right">
                    <a href="javascript:void(0);" id="geng-d">更多&nbsp;<span class="fa fa-sort-amount-asc"></span></a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="wrapper wrapper-content animated fadeIn" style="padding-top:0px;">
    <div class="row" style="margin: 0">
        <div class="pull-left" style="width:26.222%;margin-bottom: 10px;">
            <div class="ibox-title clearfix">
                <div class="detail-title clearfix">
                    <div class="pull-left all-inline">
                        <img src="__PUBLIC__/img/chanpxx.png" alt="">&nbsp;
                        <div class="text-tag" style="top: 2px;color: #676A6C;">
                            <span>相关商机</span>
                        </div>
                    </div>
                    <div class="pull-left text-center" style="margin-left: 20px;">
                        <input type="hidden" id="maodian" />
                        <button class="btn btn-outline btn-success all_business_a" id="customer-relation"  onclick="customer_relation(this);" style="border-radius: 6px;width: 100%;line-height:30px;padding: 0px;width: 80px;" type="button">全部</button>
                    </div>
					<?php if($share_num != 1): ?><!-- 分享不显示 -->
						<div class="pull-right detail-right">
							<span rel="<?php echo ($customer['customer_id']); ?>">
								<?php if($content != 'resource'): ?><a data-toggle="tooltip" data-placement="top" class="addproduct" rel="<?php echo ($customer['customer_id']); ?>" title="" data-original-title="添加商机" href="javascript:void(0);">
										<span class="plus-num">+</span>
									</a><?php endif; ?>
							</span>
						</div><?php endif; ?>
                </div>
            </div>
            <div class="ibox-content" style="padding: 0px 0px 20px;border-top:none;min-height:500px;">
                <?php if(is_array($customer['business'])): $i = 0; $__LIST__ = $customer['business'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="product-list" style="cursor:pointer;" rel="<?php echo ($vo['business_id']); ?>">
                        <!--竖线 -->
                        <div class="row ping-p">
                            <div class="col-md-1">
                                <div class="pull-left color-a">
                                    <a href="javascript:void(0);" rel="<?php echo ($vo['business_id']); ?>" style="font-size: 13px" class="product-a"><i class="fa fa-bookmark"></i></a>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <p><?php echo date('Y-m-d H:i', $vo['create_time']);?></p>
                            </div>
    						<?php if($content != 'resource' && $share_num != 1): ?><div class="col-md-1">
                                <div class="pull-right social-action dropdown">
                                    <span class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-angle-down" style="font-size:20px;cursor:pointer"></i>
                                    </span>
                                    <ul class="dropdown-menu m-t-xs">
                                        <li>
                                            <a href="<?php echo U('business/view','id='.$vo['business_id']);?>" >详情</a>
                                            <a href="<?php echo U('business/edit','id='.$vo['business_id']);?>" >编辑</a>
                                            <a href="javascript:void(0);" class="business_delete" rel="<?php echo ($vo['business_id']); ?>" >删除</a>
                                        </li>
                                    </ul>
                                </div>
                            </div><?php endif; ?>
                        </div>
                        <div class="row ping-p">
                            <div class="col-md-3">
                                <p>商机名称</p>
                            </div>
                            <div class="col-md-7">
                                <p class="p-333"><?php echo ($vo['name']); ?></p>
                            </div>
                            <div class="col-md-1"></div>
                        </div>
                        <div class="row ping-p edit-show">
                            <!-- <div class="col-md-1">
                                <span class="sq-tag"></span>
                            </div> -->
                            <div class="col-md-3">
                                <p>相关产品</p>
                            </div>
                            <div class="col-md-7">
                                <p class="p-333" ><?php echo ($vo['product_name']); ?>（<?php echo ($vo['product_count']); ?>）</p>
                            </div>
    						<?php if($content != 'resource' && $share_num != 1): ?><div class="col-md-1 color-a-edit"  style="font-size: 15px">
                                <a style="display:none" rel="<?php echo ($vo['business_id']); ?>" class="editproduct" href="javascript:void(0);"><i class="fa fa-pencil"></i></a>
                            </div><?php endif; ?>
                        </div>
                        <div class="row ping-p">
                            <!-- <div class="col-md-1"></div> -->
                            <div class="col-md-3">
                                <p>营销阶段</p>
                            </div>
                            <div class="col-md-4">
                                <div class="progress progress-mini" style="cursor:pointer;background-color: #DDD;" data-toggle="tooltip" data-placement="top" class="" data-original-title="<?php echo ($vo['status']); ?>">
                                    <div class="progress-bar <?php if($vo['status_id']==99){echo 'progress-bar-danger';}else{if($vo['progress']<=30){echo 'progress-bar-danger';}elseif($vo['progress']<=60){echo 'progress-bar-warning';}elseif($vo['progress']<99){echo '';}else{echo 'progress-bar-success';}} ?>" style="width: <?php echo ($vo['progress']); ?>%;"></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <p class="p-333" ><?php echo ($vo['status']); ?></p>
                            </div>
    						<?php if($content != 'resource' && $share_num != 1): ?><div class="col-md-1 color-a" style="font-size: 15px">
                                    <?php if($vo['status_id'] == 99): ?><i class="fa fa-times-circle" style="color: #ED5565;"></i>
                                    <?php else: ?>
                                        <a href="javascript:void(0);" data-toggle="tooltip" data-placement="right"  data-original-title="推进进度" rel="<?php echo ($vo['business_id']); ?>" class="advance">
                                            <i class="fa fa-forward"></i>
                                        </a><?php endif; ?>
                                </div><?php endif; ?>
                        </div>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
        <div class="pull-right" style="width:72.7%;margin-bottom: 10px;">
            <div class="tabs-container ibox-content product-content" style="min-height:608px;">
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="share_num" value="<?php echo ($share_num); ?>">
<div style="display:none" id="dialog-remind_view" title="提醒内容">
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
<link href="__PUBLIC__/css/litebox.css" rel="stylesheet" type="text/css">
<script src="__PUBLIC__/js/litebox.min.js" type="text/javascript"></script>
<script src="__PUBLIC__/js/images-loaded.min.js" type="text/javascript"></script>
<div style="display:none" id="dialog-editproduct" title="编辑产品">
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
<div  id="dialog-addproduct" title="添加商机">
    <div class="spiner-example">
        <div class="sk-spinner sk-spinner-three-bounce">
            <div class="sk-bounce1"></div>
            <div class="sk-bounce2"></div>
            <div class="sk-bounce3"></div>
        </div>
    </div>
</div>
<div  id="dialog-share-list" title="客户共享成员列表">
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
<script type="text/javascript" language="javascript" > 
//客户转移
$("#transfer_name").click(function(){
	var id_array = new Array();
	id_array.push($(this).attr('rel'));
	if(id_array.length == 0){
		alert("<?php echo L('YOU_HAVE_NOT_CHOSEN_ANY_CUSTOMERS');?>");
	}else{
		var customer_ids = id_array.join(",");
		$('#dialog-transform').dialog('open');
		$('#dialog-transform').load("<?php echo U('customer/transfer_edit');?>","customer_id="+customer_ids);
	}
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
	}
});
//共享列表
$("#share_list").click(function(){
    var customer_id = $(this).attr('rel');
    $('#dialog-share-list').load('<?php echo U("customer/share_list","customer_id=");?>'+customer_id); 
    $('#dialog-share-list').dialog('open');
});
$("#dialog-share-list").dialog({
    autoOpen: false,
    modal: true,
    minWidth: 700,
    maxHeight: 500,
    position: ["center",100],
	buttons: {
        "关闭": function () {
            $(this).dialog("close");
        }
    }
});

var customer_status = $('#customer_status').val();
if(customer_status == '已成交客户'){
	$('#customer_status').attr('disabled',true);
}
/**
 * 如果是图片时 双击可查看大图
 */
$('.litebox_file').liteBox({
    revealSpeed: 400,
    background: 'rgba(0,0,0,.8)',
    overlayClose: true,
    escKey: true,
    navKey: true,
    errorMessage: '图片加载失败.'
});
$(document).ready(function(){
    /*默认的 星星下拉框*/
    $('#edit-grade').val("<?php echo ($customer['grade']); ?>");

    /* 非ajax 提交后跳转 到指定的产品 */
    var business_id ="<?php echo $_GET['bid']; ?>";
    business_id = Number(business_id);
    if(business_id >= 1){
        $(".product-list[rel="+business_id+"]").trigger('click'); 
    }else{
        $('#customer-relation').trigger('click');
    }

});
/*添加商机*/
$("#dialog-addbusiness").dialog({
    autoOpen: false,
    modal: true,
    minWidth: 850,
    maxHeight: 500,
    position: ["center",100],
    buttons: {
        "确定": function () {
            if(typeof($('.bus_product').val()) == 'undefined' ||  $('.bus_product').val() == '0'){
                alert_crm('请至少选择一个产品！');    
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
        $(this).empty();
    }
});
/*选择产品*/
$("#dialog-addproduct").dialog({
    autoOpen: false,
    modal: true,
    minWidth: 850,
    maxHeight: 500,
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
$(".addproduct").click(function(){
    var customer_id = $(this).attr('rel');
    $('#dialog-addproduct').load('<?php echo U("product/mutildialog_product","customer_id=");?>'+customer_id); 
    $('#dialog-addproduct').dialog('open');
});

$(".editproduct").click(function(){
    var business_id = $(this).attr('rel');
    $('#dialog-editproduct').dialog('open');
    $('#dialog-editproduct').load('<?php echo U("product/mutildialog_product","business_id=");?>'+business_id);
});
/*编辑商机*/
$("#dialog-editproduct").dialog({
    autoOpen: false,
    modal: true,
    minWidth: 850,
    maxHeight: 400,
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
        /*关闭清空数据*/
        $(this).empty();
    }
});

/*修改商机的 鼠标放上 效果*/
$('.edit-show').mouseover(function(){
	$(this).find('a').show();
});

$('.edit-show').mouseout(function(){
    $(this).find('a').hide();
});

/*营销阶段*/
$("#dialog-advance").dialog({
    autoOpen: false,
    modal: true,
    minWidth: 320,
    maxHeight: 800,
    position: ["center",100]
});
$(".advance").click(function(){
    var business_id = $(this).attr('rel');
    $('#dialog-advance').dialog('open');
    $('#dialog-advance').load('<?php echo U("business/advance","id=");?>'+business_id);
});

/* "更多" 的收起 展开*/
$('#geng-d').click(function(){
    var rel = $('#list-show').attr('rel');
    var html = '';
    if(rel == 'false'){
        $('#list-show').attr('rel', 'true');
        $('#list-show').slideToggle("3000");
        html = '收起&nbsp;<span class="fa fa-sort-amount-desc"></span>';
        $(this).html(html);
    }else{
        $('#list-show').attr('rel', 'false');
		$('#list-show').slideToggle("3000");
        html = '展开&nbsp;<span class="fa fa-sort-amount-asc"></span>';
        $(this).html(html);
        
    }
});

/*添加商机的 提示框*/
$('[data-toggle="tooltip"]').tooltip({html:true});


/*客户修改 鼠标放上显示编辑*/
$('.form-p').mouseover(function(){
	var content = $('#content').val();
	if(content != 'resource'){
		$(this).find('a').removeClass('hide');
	}
});

$('.form-p').mouseout(function(){
    $(this).find('a').addClass('hide');
});
/*product list 点击效果*/
$('.product-list').click(function(){
    // var rel = $(this).find('.product-a');
    var rel = $(this);
    var business_id = rel.attr('rel');
    $('#bid').val(business_id);
    var customer_id = "<?php echo ($customer['customer_id']); ?>";
    var tab = window.location.hash;
    product_detail(rel);

    //url追加business_id
    var title = '';
    var url = './index.php?m=customer&a=view&id='+customer_id+'&bid='+business_id+tab;
    var state = {
        title: title,
        url: url
    };
    history.pushState(state, '', './index.php?m=customer&a=view&id='+customer_id+'&bid='+business_id+tab);
});

// 商机删除
$('.business_delete').click(function(){
    var business_id = $(this).attr('rel');
    swal({
        title: "您确定要删除这条商机吗？",
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
                type:'get',
                url: "<?php echo U('business/delete','id=');?>"+business_id,
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

/*客户修改的逻辑
* 分为 多个下拉框 时间 单选框 复选框, 
* 需要优化的是:
* 多个下拉框 单选框 复选框 的提交是根据"form-group 捕捉的, 体验不好
* ajax提交可以封装成一个函数 
* 
*/
$('.form-p').click(function(){
    var ori_name = null;
    var ori_val = null;
    var name = null;
    var val = null;
    var cus_id = "<?php echo ($customer['customer_id']); ?>";
    var con_id = $('#contacts_id').val();
    var col_4 = $(this).parent();
    var input_div = col_4.next();
    var input = col_4.next().children();
    var sel = input_div.find("[input_type='address']");
    var chec = input_div.find("input[type='checkbox']");
    var radio = input_div.find("input[type='radio']");
    var time = input_div.find("input[input_type='time']");
    var no_click = null
    if(sel.length>2 || chec.length || radio.length || time.length){
        col_4.hide();
        input_div.removeClass('hide');
        input_div.next().removeClass('hide');
        /*复选框*/
        if(chec.length){
            no_click = chec.parent().parent().parent().attr('no_click','1');
            $('.form-group').bind('click', function(){
                var ret = $(this).attr('no_click');
                if(ret){

                }else{
                    var check_val = '';
                    input_div.find("input[type='checkbox']:checked").each(function(){
                        check_val += $(this).val()+',';
                    });
                    ori_name = chec.attr('name');
                    ori_val = col_4.find('span').text();
                    $.post("<?php echo U('edit_ajax');?>", { field: ori_name, val: check_val, customer_id: cus_id, contacts_id: con_id }, function(data){
                        if(data.status == 1){
                            col_4.find('span').text(check_val);
                            col_4.show();
                            input_div.addClass('hide');
                            input_div.next().addClass('hide');
                        }else{
                            input.val(ori_val);
                            col_4.show();
                            input_div.addClass('hide');
                            input_div.next().addClass('hide');
                            alert_crm(data.data);
                        }
                        if(data.data != '' && con_id == ''){
                            $('#contacts_id').val(data.data);
                        }
                        $('.form-group').unbind();
                        chec.parent().parent().parent().removeAttr('no_click');
                    } );
                }
            });
        } else if(radio.length){
            /*单选框*/
            no_click = radio.parent().parent().parent().attr('no_click','1');
            ori_name = radio.attr('name');
            ori_val = radio.filter(':checked').val();
            $('.form-group').bind('click', function(){
                var ret = $(this).attr('no_click');
                if(!ret){
                    val = radio.filter(':checked').val();
                    if(ori_val != val){
                        $.post("<?php echo U('edit_ajax');?>", { field: ori_name, val: val, customer_id: cus_id, contacts_id: con_id }, function(data){
                            if(data.status == 1){
                                col_4.find('span').text(val);
                                col_4.show();
                                input_div.addClass('hide');
                                input_div.next().addClass('hide');
                            }else{
                                input.val(ori_val);
                                col_4.show();
                                input_div.addClass('hide');
                                input_div.next().addClass('hide');
                                alert_crm(data.data);
                            }
                            if(data.data != '' && con_id == ''){
                                $('#contacts_id').val(data.data);
                            }
                            $('.form-group').unbind();
                            radio.parent().parent().parent().removeAttr('no_click');
                        } );
                    }else{
                        col_4.show();
                        input_div.addClass('hide');
                        input_div.next().addClass('hide');
                    }
                }
            });
        } else if(time.length){
            /*时间*/
            no_click = time.parent().parent().attr('no_click','1');
            ori_name = time.attr('name');
            ori_val = time.val();
            $('.form-group').bind('click', function(){
                var ret = $(this).attr('no_click');
                if(!ret){
                    val = time.val();
                    if(ori_val != val){
                        var new_val = Date.parse(new Date(val));
                        new_val = new_val / 1000;
                        $.post("<?php echo U('edit_ajax');?>", { field: ori_name, val: new_val, customer_id: cus_id, contacts_id: con_id }, function(data){
                            if(data.status == 1){
                                col_4.find('span').text(val);
                                col_4.show();
                                input_div.addClass('hide');
                                input_div.next().addClass('hide');
                            }else{
                                input.val(ori_val);
                                col_4.show();
                                input_div.addClass('hide');
                                input_div.next().addClass('hide');
                                alert_crm($data.data);
                            }
                            $('.form-group').unbind();
                            $('.form-group').removeAttr('no_click');
                        } );
                    }else{
                        col_4.show();
                        input_div.addClass('hide');
                        input_div.next().addClass('hide');
                    }
                }
            });
        } else if(sel.length>2){
            /*多个单选框*/
            no_click = sel.parent().parent().attr('no_click','1');
            ori_name = sel.attr('name');
            ori_val = col_4.find('span').text();
            $('.form-group').bind('click', function(){
                var ret = $(this).attr('no_click');
                if(ret){

                }else{
                    var sel_val = '';
                    sel.each(function(){
                        sel_val += $(this).val()+',';
                    });
                    $.post("<?php echo U('edit_ajax');?>", { field: ori_name, val: sel_val, customer_id: cus_id, contacts_id: con_id }, function(data){
                        if(data.status == 1){
                            col_4.find('span').text(sel_val);
                            col_4.show();
                            input_div.addClass('hide');
                            input_div.next().addClass('hide');
                        }else{
                            input.val(ori_val);
                            col_4.show();
                            input_div.addClass('hide');
                            input_div.next().addClass('hide');
                            alert_crm(data.data);
                        }
                        if(data.data != '' && con_id == ''){
                            $('#contacts_id').val(data.data);
                        }
                        $('.form-group').unbind();
                        sel.parent().parent().removeAttr('no_click');
                    } );
                }
            });
        }
        return false;
    }
    ori_val = input.val();
    ori_name = input.attr('name');
    col_4.hide();
    input_div.removeClass('hide');
    input_div.next().removeClass('hide');
    input.focus();
    $(input).focusout(function(){
        /* input框 */
        val = input.val().trim();
        if(ori_val != val){
            $.post("<?php echo U('edit_ajax');?>", { field: ori_name, val: val, customer_id: cus_id, contacts_id: con_id }, function(data){
                if(data.status == 1){
                    if(ori_name == 'grade'){
                        /*星星*/
                        var star_html = '';
                        for (var i=0;i<val;i++){
                            star_html += '<i class="fa fa-star star-orange"></i>&nbsp;';
                        }
                        for (var i=0;i<5-val;i++){
                            star_html += '<i class="fa fa-star"></i>&nbsp;';
                        }
                        col_4.find('span').html(star_html);
                    }else{
                        input.val(val);
                        col_4.find('span').text(val);
                    }
                    if(ori_name == 'name'){
                        $('.h2-customer-name').text(val);
                    }
                    col_4.show();
                    input_div.addClass('hide');
                    input_div.next().addClass('hide');
                }else{
                    input.val(ori_val);
                    col_4.show();
                    input_div.addClass('hide');
                    input_div.next().addClass('hide');
                    alert_crm(data.data);
                }
                if(data.data != '' && (con_id == '' || con_id == 0)){
                    $('#contacts_id').val(data.data);
                }
                $(input).unbind();
            });
        }else{
            col_4.show();
            input_div.addClass('hide');
            input_div.next().addClass('hide');
        }
    });
});

/*商机详情 加载的圈圈效果*/
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
/*单个商机*///obj为要打开的标签页
var content_id = 0;
function product_detail(e,obj){
    var business_id = $(e).attr('rel');
	var content = $('#content').val();
	var share_num = $('#share_num').val();
    if (content_id == business_id) {
        return false;
    }
    content_id = business_id;
    $('.product-list').removeClass('product-a-line');
    $('#customer-relation').removeClass('all_business_a');
    $('#customer-relation').addClass('all_business');
    $(e).addClass('product-a-line');
    $('.product-list').css('background-color','#fff');
    $(e).css('background-color','#f3f3f4');
    $('.product-content').html(detail_html);
    $('.product-content').load("<?php echo U('business/view_ajax');?>", {id: business_id,type:1,content:content,share_num:share_num, module: 'customer'}, function(){
        $(obj).trigger('click');
    });
}
/*所有商机*///obj为要打开的标签页
function customer_relation(e,obj){
    var maodian = $('#maodian').val();
	var share_num = $('#share_num').val();
    customer_url_jump(maodian);
    $('.product-a').removeClass('product-a-color');
    $('.product-list').removeClass('product-a-line');
    $('.product-list').css('background-color','#fff');
    var customer_id = "<?php echo ($customer['customer_id']); ?>";
	var content = $('#content').val();
    content_id = 0;
    $('.product-content').html(detail_html);
    $('.product-content').load("<?php echo U('business/view_ajax');?>", {customer_id: customer_id,content:content,share_num:share_num, module: 'customer'}, function(){
        $(obj).trigger('click');
    });
}
function customer_url_jump(maodian){
    var content = "<?php echo ($_GET['content']); ?>";
    var customer_id = "<?php echo ($customer['customer_id']); ?>";
    var url = "<?php echo U('customer/view','id=');?>"+customer_id+maodian+'&content='+content;
    $('#bid').val('');
    window.history.replaceState({},0,'http://'+window.location.host+url);
}

$("#remind").click(function(){
    var customer_id = "<?php echo ($customer['customer_id']); ?>";
    $('#dialog-remind').dialog('open');
    $('#dialog-remind').load("<?php echo U('remind/add','module=customer&module_id=');?>"+customer_id);
});

$("#remind_view").click(function(){
    var customer_id = "<?php echo ($customer['customer_id']); ?>";
    $('#dialog-remind_view').dialog('open');
    $('#dialog-remind_view').load("<?php echo U('remind/view','module=customer&module_id=');?>"+customer_id);
});
$("#dialog-remind").dialog({
    autoOpen: false,
    modal: true,
    width: 600,
    maxHeight: 400,
    position: ["center",100],
    buttons: {
        "确定": function () {
            if($('#dialog_content').val() == ''){
                alert_crm("请填写提醒内容！")
                $("#dialog_content").focus(); 
            }else{
                $('#remind_form').submit();
                $(this).dialog("close");
            }
        },
        "取消": function () {
            $(this).dialog("close");
        }
    }
});
$("#dialog-remind_view").dialog({
    autoOpen: false,
    modal: true,
    width: 500,
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
            });
        },
        "取消": function () {
            $(this).dialog("close");
        }
    }
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