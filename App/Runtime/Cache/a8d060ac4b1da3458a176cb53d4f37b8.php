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

<style>
	body {
		overflow-y: hidden;
	}

	.img-circle {
		width: 35px;
		height: 35px;
		border-radius: 50%;
		padding-left: 0px;
	}

	.main_time {
		width: 100%;
		text-align: center;
		margin-bottom: 10px;
		padding-top: 10px;
		clear: both;
	}

	.span_time {
		font-size: 12px;
		background: #ccc;
		border-radius: 5px;
		padding: 1px 5px;
	}

	.receiver {
		width: 100%;
		float: left;
		height: auto;
	}

	.receiver div:first-child img {
		width: 30px;
		height: 30px;
		border-radius: 15px;
	}

	.img_con img {
		width: 30px;
		height: 30px;
		border-radius: 15px;
	}

	.img_con {
		float: left;
		width: 95%;
	}

	.text_con {
		float: left;
		width: 70%;
		background-color: #fff;
		margin: 0 10px 10px 40px;
		padding: 5px;
		border-radius: 5px;
		line-height: 30px;
	}

	.p_name {
		font-size: 12px;
		position: relative;
		left: 10px;
		font-weight: 900;
	}

	.text_con span {
		word-break: break-all;
	}

	.text_con a {
		color: #066AFF;
	}

	.loading {
		width: 100%;
		height: 200px;
		float: left;
	}

	/*任务相关*/

	.dropdown,
	.dropup {
		position: relative;
	}

	.dropdown-menu.bullet {
		margin-top: 12px;
	}

	.dropdown-menu {
		padding: 1px 0;
		margin-top: 3px;
		border-radius: 0;
		-webkit-box-shadow: 0 3px 12px rgba(0, 0, 0, .05);
		box-shadow: 0 3px 12px rgba(0, 0, 0, .05);
		-webkit-transition: .25s;
		-o-transition: .25s;
		transition: .25s;
		position: absolute;
		top: 100%;
		left: 0;
		z-index: 1200;
		display: none;
		float: left;
		min-width: 160px;
		padding: 5px 0;
		margin: 2px 0 0;
		font-size: 14px;
		text-align: left;
		list-style: none;
		background-color: #fff;
		-webkit-background-clip: padding-box;
		background-clip: padding-box;
		border: 1px solid #ccc;
		border: 1px solid #e4eaec;
		border-radius: 3px;
		-webkit-box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
	}

	.dropdown-menu>li {
		padding: 0 3px;
		margin: 2px 0;
	}

	.taskboard-stage-header .dropdown-menu>li>a {
		padding: 6px 10px;
		-webkit-transition: background-color .25s;
		-o-transition: background-color .25s;
		transition: background-color .25s;
		display: block;
		padding: 3px 20px;
		clear: both;
		font-weight: 400;
		line-height: 1.6;
		color: #76838f;
		white-space: nowrap;
	}

	.dropdown-menu li .icon:first-child,
	.dropdown-menu li>a .icon:first-child {
		width: 1em;
		margin-right: .5em;
		text-align: center;
	}

	#right-sidebar-task {
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

	.agile-list li {
		margin-bottom: -1px;
		border: none;
	}

	.taskboard-stage {
		height: 90% !important;
	}

	.taskboard-stages {
		overflow-y: hidden !important;
	}

	.color-selector>li {
		position: relative;
		display: inline-block;
		width: 24px;
		height: 24px;
		margin: 0 5px 0 0;
		border-radius: 100%;
	}

	.bg-blue-600 label:before {
		background-color: #62a8ea !important;
	}

	.bg-green-600 label:before {
		background-color: #46be8a !important;
	}

	.bg-cyan-600 label:before {
		background-color: #57c7d4 !important;
	}

	.bg-orange-600 label:before {
		background-color: #f2a654 !important;
	}

	.bg-red-600 label:before {
		background-color: #f96868 !important;
	}

	.bg-blue-grey-600 label:before {
		background-color: #526069 !important;
	}

	.bg-purple-600 label:before {
		background-color: #926dde !important;
	}

	.bg-blue-600 label:after {
		background-color: #62a8ea !important;
	}

	.bg-green-600 label:after {
		background-color: #46be8a !important;
	}

	.bg-cyan-600 label:after {
		background-color: #57c7d4 !important;
	}

	.bg-orange-600 label:after {
		background-color: #f2a654 !important;
	}

	.bg-red-600 label:after {
		background-color: #f96868 !important;
	}

	.bg-blue-grey-600 label:after {
		background-color: #526069 !important;
	}

	.bg-purple-600 label:after {
		background-color: #926dde !important;
	}

	.radio label:before {
		width: 24px;
		height: 24px;
	}

	.radio label:after {
		left: 6px;
		top: 4px;
		font-family: "FontAwesome";
		content: "\f00c";
		color: #fff;
	}

	.img-circle {
		margin-right: 0px;
	}

	.list-group-item:hover {
		background-color: #fafafb !important;
	}

	.taskboard-list .list-group-item .task-members {
		float: left;
	}

	.action-wrap {
		float: left;
		width: 100%;
		height: 10%;
		min-height: 40px;
		background-color: #fff;
	}

	#right_market_view {
		width: 50% !important;
		right: -60%;
		background-color: #fff;
		overflow: hidden;
		position: fixed;
		top: 0px;
		z-index: 1009;
		bottom: 0;
		box-shadow: 0px 2px 1px #888888;
	}
</style>
<!-- nice-scroll -->
<script src="__PUBLIC__/style/js/plugins/nice-scroll/jquery.nicescroll.min.js" type="text/javascript"></script>
<script>
	$(function () {
		var scroll_width = 7;
		$(".message_left").height(window.innerHeight - $("#message_table").offset().top - 10);
		$("#message_div").height(window.innerHeight - $("#message_table").offset().top - $("#tfoot_div").height() - 5);
		$(window).resize(function () {
			$(".message_left").height(window.innerHeight - $("#message_table").offset().top - 10);
			$("#message_div").height(window.innerHeight - $("#message_table").offset().top - $("#tfoot_div").height() - 5);
		});
		$(".nicescroll").niceScroll({
			cursorcolor: "#ccc",//#CC0071 光标颜色
			cursoropacitymax: 1, //改变不透明度非常光标处于活动状态（scrollabar“可见”状态），范围从1到0
			touchbehavior: false, //使光标拖动滚动像在台式电脑触摸设备
			cursorwidth: scroll_width + "px", //像素光标的宽度
			cursorborder: "0", //     游标边框css定义
			cursorborderradius: "5px",//以像素为光标边界半径
			autohidemode: false, //是否隐藏滚动条
			zindex: 100,
			background: "#F3F3F5",//滚动条背景色
		});
	});	
</script>
<input type="hidden" id="dialog_open" value="0">
<div class="wrapper wrapper-content animated fadeIn">
	<div class="row">
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
		<div class="col-lg-12">
			<script type="text/javascript">
$(function(){
	$("#message_table_left").height(window.innerHeight-$("#message_table_left").offset().top-32);
	$(window).resize(function(){
		$("#message_table_left").height(window.innerHeight-$("#message_table_left").offset().top-32);
	});
})
</script>
<style>
	.left_content{float:left;}
	.right_content{float:right;}
	.pull-left{margin-bottom: 10px;}
	.con_left{color:#000;font-size: 13px;font-weight: 700;}
</style>
<div class="col-lg-3" style="padding:0px;">
	<div class="ibox-content clearfix" style="padding:0px;">
		<div class="table-content clearfix" style="padding-bottom:5px;">	
			<div class="ibox-title" style="border: none;line-height:40px;">
				<div class="nav pull-left" >
					<span style="font-weight:900;">我的消息</span>
				</div>
				<div class="nav pull-right" >
					<a href="javascript:void(0)" id="to_role" title="选择收件人"><img src="__PUBLIC__/img/icon.png" /></a>
				</div>
			</div>
		</div>
		<div class="ibox-content" id="message_table_left"  style="padding:0px;border-width: 1px 0px;">
			<div class="message_left full-height-scroll">
				<div class="table-content clearfix" style="background-color:#fff;min-height:70px;border: none;" >
					<div class="ibox-title clearfix" style="padding-top: 4px;border-width:1px 0 0;border: none;">
						<a href="<?php echo U('message/index');?>">
			            <div class="detail-title clearfix">
			                <div class="pull-left all-inline">
			                    <img src="__PUBLIC__/img/wukong.png" alt="">&nbsp;
			                    <div class="con_left">
			                        <span>小助手</span>
			                    </div>
			                </div>
			            </div>
			            </a>
					</div>
					<div class="ibox-title clearfix" style="padding-top: 4px;border-width:1px 0 0;">
						<a href="<?php echo U('message/index','by=remind');?>">
			            <div class="detail-title clearfix">
			                <div class="pull-left all-inline">
			                    <img src="__PUBLIC__/img/remind.png" alt="">&nbsp;
			                    <div class="con_left">
			                        <span>提醒助手</span>
			                    </div>
			                </div>
			            </div>
			            </a>
					</div>
					<div class="ibox-title clearfix" style="padding-top: 4px;border-width:1px 0 0;">
						<a href="<?php echo U('message/index','by=announcement');?>">
			            <div class="detail-title clearfix" style="border: none;">
			                <div class="pull-left all-inline">
			                    <img src="__PUBLIC__/img/announcement.png" alt="">&nbsp;
			                    <div class="con_left">
			                        <span>系统公告</span>
			                    </div>
			                </div>
							<div class="right_content">
			                    <div style="float:right;"><?php if($no_counts > 0): ?><i class="fa fa-circle text-danger"></i><?php endif; ?></div>
			                </div>
			            </div>
			            </a>
					</div>
					<?php if(is_array($send_list)): $i = 0; $__LIST__ = $send_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="ibox-title clearfix" style="padding-top: 4px;border-width:1px 0 0 0;">
							<a class="message_view" href="<?php echo U('message/message_view','to_role_id='.$vo['user_info']['role_id']);?>">
			                <div class="detail-title clearfix">
			                    <div class="pull-left all-inline" style="width: 55px;">
			                    	<div class="left_content">
			                    		<?php if($vo['user_info']['thumb_path'] != ''): ?><img src="<?php echo ($vo['user_info']['thumb_path']); ?>" class="img-circle" alt="<?php echo ($vo['user_info']['full_name']); ?>">&nbsp;
			                    		<?php else: ?>
			                    			<img src="__PUBLIC__/img/avatar_default.png" class="img-circle" alt="<?php echo ($vo['user_info']['full_name']); ?>">&nbsp;<?php endif; ?>
			                    	</div>
			                    </div>
		                        <div class="pull-left all-inline" style="width: 70%;">
		                        	<div class="left_content">
			                        	<span class="con_left" style="float:left;"><?php echo ($vo['user_info']['full_name']); ?></span>
			                        </div>
			                        <div class="right_content">
			                            <div style="float:right;"><?php if($vo['noread_count'] > 0): ?><i class="fa fa-circle text-danger"></i><?php endif; ?></div>
			                        </div>
		                        </div>
		                        <div class="pull-left all-inline" style="width: 70%;">
		                        	<div class="left_content">
			                        	<span style="float:left;color:#9D9FA2;margin-top: 0px;">
			                        	<?php echo ($vo['message']['content']); ?></span>
			                        </div>
		                        </div>
			                </div>
			                </a>
						</div><?php endforeach; endif; else: echo "" ;endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

			<div class="col-lg-9" style="padding-right: 0px;">
				<div class="ibox-content clearfix" style="padding:0px;border: none;">
					<div class="table-content clearfix" style="padding-top:0px;padding-left:0px;border: none;">
						<div class="ibox-title" style="min-height: 70px;">
							<div class="nav pull-left">
								<?php if($by == 'announcement'): ?><img src="__PUBLIC__/img/announcement.png" alt="">&nbsp;
									<span style="font-weight:900;line-height:40px;">系统公告</span>
									<?php elseif($by == 'remind'): ?>
									<img src="__PUBLIC__/img/remind.png" alt="">&nbsp;
									<span style="font-weight:900;line-height:40px;">提醒助手</span>
									<?php else: ?>
									<img src="__PUBLIC__/img/wukong.png" alt="">&nbsp;
									<span style="font-weight:900;line-height:40px;">小助手</span><?php endif; ?>
							</div>
						</div>
					</div>
					<div class="ibox-content" id="message_table" style="padding:0px;border-width:0px 1px;">
						<div class="nicescroll" id="message_div">
							<input type="hidden" id="message_type" value="<?php echo ($by); ?>" />
							<input type="hidden" id="p" value="2" />
							<input type="hidden" id="load" value="1" />
							<div class="panel-body" id="list" style="margin-left:30px;margin-top:20px;">
								<?php if($by == 'announcement'): if(is_array($announcement_list)): $k = 0; $__LIST__ = $announcement_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><div class="receiver">
											<div class="img_con">
												<img width="70px" height="70px" class="yh_head" src="__PUBLIC__/img/announcement.png" />
												<span class="p_name">系统公告&nbsp;&nbsp;&nbsp;&nbsp;</span>
												<span class="p_name" style="color: #aaa;font-weight: 300;"><?php echo (date("m/d H:i",$vo["create_time"])); ?></span>
												<span class="p_name" style="color: #aaa;font-weight: 300;margin-left:25px;">
													<?php if($vo['read']): ?>阅读时间 : <?php echo (date("Y-m-d",$vo['read']['read_time'])); ?>
														<?php else: ?>
														<span style="color:red">未读</span><?php endif; ?>
												</span>
											</div>
											<div class="text_con">
												<div class="right_triangle"></div>
												<span>
													<td>
														<div style="font-size:13px">
															<?php echo ($vo["content"]); ?>
														</div>
													</td>
												</span>
											</div>
										</div><?php endforeach; endif; else: echo "" ;endif; ?>
									<?php elseif($by == 'remind'): ?>
									<?php if(is_array($remind_list)): $k = 0; $__LIST__ = $remind_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><div class="receiver">
											<div class="img_con">
												<img width="70px" height="70px" class="yh_head" src="__PUBLIC__/img/remind.png" />
												<span class="p_name">提醒助手&nbsp;&nbsp;&nbsp;&nbsp;</span>
												<span class="p_name" style="color: #aaa;font-weight: 300;"><?php echo (date("m/d H:i",$vo["remind_time"])); ?></span>
											</div>
											<div class="text_con">
												<div class="right_triangle"></div>
												<span>
													<td>
														<div style="font-size:13px">
															<?php echo ($vo["content"]); ?>
														</div>
													</td>
												</span>
											</div>
										</div><?php endforeach; endif; else: echo "" ;endif; ?>
									<?php else: ?>
									<?php if(is_array($receive_list)): $k = 0; $__LIST__ = $receive_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><div class="receiver">
											<div class="img_con">
												<img width="70px" height="70px" class="yh_head" src="__PUBLIC__/img/wukong.png" />
												<span class="p_name">小助手&nbsp;&nbsp;&nbsp;&nbsp;</span>
												<span class="p_name" style="color: #aaa;font-weight: 300;"><?php echo (date("m/d H:i",$vo["send_time"])); ?></span>
											</div>
											<div class="text_con">
												<div class="right_triangle"></div>
												<span>
													<td>
														<div id="content_<?php echo ($vo['message_id']); ?>" style="font-size:13px;<?php if( $vo[ 'is_mark'] == 1 ): ?>color: #a6a6a6;<?php else: ?>color: #000;<?php endif; ?>">
															<a href="javascript:void(0)" class="is_mark" id="is_mark_<?php echo ($vo['message_id']); ?>" rel="<?php echo ($vo['message_id']); ?>" rel1="<?php echo ($vo['is_mark']); ?>"
															 style="color: #667b8f;" <?php if($vo['is_mark'] == 1): ?>title="标记为未读"
																<?php else: ?>title="标记为已读"<?php endif; ?>>
								<span id="mark_icon_<?php echo ($vo['message_id']); ?>">
									<?php if($vo['is_mark'] == 1): ?><i class="fa fa-flag"></i>
										<?php else: ?>
										<i class="fa fa-flag-o"></i><?php endif; ?>
								</span>
								</a>
								<?php echo ($vo["content"]); ?>
								</div>
								</td>
								</span>
								</div>
								</div><?php endforeach; endif; else: echo "" ;endif; endif; ?>
							</div>
						</div>
					</div>
					<div style="clear: both;" id="clearfix"></div>
					<div id="tfoot_div" class="clearfix">
						<div class="clearfix" id="tfoot_page">
							<div class="ibox-content">
								<div class="col-sm-offset-2">&nbsp;</div>
							</div>
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
<div id="right_market_view">
	<!--the css for jquery.mCustomScrollbar-->
	<link rel="stylesheet" href="__PUBLIC__/emoji/css/jquery.mCustomScrollbar.min.css" />
	<!--the css for this plugin-->
	<link rel="stylesheet" href="__PUBLIC__/emoji/css/jquery.emoji.css" />
	<!--(Optional) the js for jquery.mCustomScrollbar's addon-->
	<script src="__PUBLIC__/emoji/js/jquery.mousewheel-3.0.6.min.js"></script>
	<!--the js for jquery.mCustomScrollbar-->
	<script src="__PUBLIC__/emoji/js/jquery.mCustomScrollbar.min.js"></script>
	<!--the js for this plugin-->
	<script src="__PUBLIC__/emoji/js/jquery.emoji.js"></script>

	<div class="sidebar-container-market" id="sidebar-container-market">

	</div>
</div>
<div style="display:none;" id="dialog-message-send" title="<?php echo L('WRITE_LETTER_IN');?>">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<div id="dialog-role-list2" title="收件人">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<div style="display:none;" id="dialog-role-info" title="<?php echo L('USER_INFO');?>">
	<div class="spiner-example">
		<div class="sk-spinner sk-spinner-three-bounce">
			<div class="sk-bounce1"></div>
			<div class="sk-bounce2"></div>
			<div class="sk-bounce3"></div>
		</div>
	</div>
</div>
<script>
	$(function () {
		$('#to_role').click(function () {
			$('#dialog-role-list2').dialog('open');
			$('#dialog-role-list2').load('<?php echo U("user/listDialog","by=message");?>');
			return false;
		});
		$("#dialog-role-list2").dialog({
			autoOpen: false,
			modal: true,
			width: 800,
			maxHeight: 400,
			buttons: {
				"确定": function () {
					var to_role_id = $('input:radio[name="owner"]:checked').val();
					window.location.href = "<?php echo U('message/message_view','to_role_id=');?>" + to_role_id;
					$(this).dialog("close");
				},
				"取消": function () {
					$(this).dialog("close");
				}
			},
			position: ["center", 100],
			close: function () {
				$(this).html('');
			}
		});
		$('.market_view').on('click', function () {
			var market_id = $(this).attr('rel');
			if ($("#right_market_view").css('right') != '0px') {
				$("#right_market_view").animate({ right: '0px' }, 600);
			}
			$('#sidebar-container-market').load("<?php echo U('market/view','market_id=');?>" + market_id);
		});
	})

</script>
<script type="text/javascript">
	$(document).ready(function () {
		var loading = false;  //状态标记
		var deviation = 1; //事件偏移量
		var nScrollHight = 0; //滚动距离总长(注意不是滚动条的长度)
		var nScrollTop = 0;   //滚动到的当前位置
		var nDivHight = $("#message_div").height();
		var scrollTop = $("#message_div").scrollTop();

		$("#message_div").scroll(function () {
			var type = $('#message_type').attr('value') ? $('#message_type').attr('value') : 'index';
			var p = $('#p').val();
			var load = $('#load').val();
			nScrollHight = $(this)[0].scrollHeight;
			nScrollTop = $(this)[0].scrollTop;
			if (loading) {
				return false;
			}
			if (nScrollTop + nDivHight + deviation >= nScrollHight && load == 1) {
				loading = true;
				//alert("滚动条到底部了");
				temp_loading = "";
				temp_loading = "<div class='loading' id='loading'><div class='spiner-example'><div class='sk-spinner sk-spinner-three-bounce'><div class='sk-bounce1'></div><div class='sk-bounce2'></div><div class='sk-bounce3'></div></div></div></div>";
				$("#message_div").scrollTop(nScrollTop + deviation + 200);
				$("#list").append(temp_loading);
				var temp = "";
				var no_data = "";
				$.ajax({
					type: 'get',
					url: 'index.php?m=message&a=index_data&p=' + p + '&by=' + type,
					async: false,
					success: function (data) {
						$('#no_data').remove();
						if (data.status == 1) {
							$.each(data.data, function (k, v) {
								var type_name = '小助手';
								var type_img = '__PUBLIC__/img/wukong.png';
								if (v.message_type == 'announcement') {
									type_img = '__PUBLIC__/img/announcement.png';
									type_name = '系统公告';
								} else if (v.message_type == 'remind') {
									type_img = '__PUBLIC__/img/remind.png';
									type_name = '提醒助手';
								}

								if (v.message_type == 'index') {
									temp += '<div class="receiver">\
											<div class="img_con">\
												<img width="70px" height="70px" class="yh_head" src="'+ type_img + '"/>\
												<span class="p_name">'+ type_name + '&nbsp;&nbsp;&nbsp;&nbsp;' + v.message_time + '</span>\
											</div>\
											<div class="text_con">\
												<div class="right_triangle"></div>\
												<span>';
									if (v.is_mark == 1) {
										temp += '<div id="content_' + v.message_id + '" style="font-size:13px;color: #a6a6a6;">';
									} else {
										temp += '<div id="content_' + v.message_id + '" style="font-size:13px;color: #000;">';
									}

									if (v.is_mark == 1) {
										temp += '<a href="javascript:void(0)" class="is_mark" id="is_mark_' + v.message_id + '" rel="' + v.message_id + '" rel1="' + v.is_mark + '" style="color: #667b8f;" title="标记为未读">';
									} else {
										temp += '<a href="javascript:void(0)" class="is_mark" rel="' + v.message_id + '" rel1="' + v.is_mark + '" style="color: #667b8f;" title="标记为已读">';
									}
									temp += '<span id="mark_icon_' + v.message_id + '">';
									if (v.is_mark == 1) {
										temp += '<i class="fa fa-flag"></i>';
									} else {
										temp += '<i class="fa fa-flag-o"></i>';
									}
									temp += '</span>\
												</a>\
												'+ v.content + '\
											</div>\
											</span>\
											</div>\
										</div>';
								} else {
									temp += "<div class='receiver'><div class='img_con'><img width='70px' height='70px' class='yh_head' src='" + type_img + "'/><span class='p_name'>" + type_name + "&nbsp;&nbsp;&nbsp;&nbsp;" + v.message_time + "</span></div><div class='text_con'><div class='right_triangle'></div><span><td><div style='font-size:13px'>" + v.content + "</div></td></span></div></div>";
								}
							})
							p++;
							$('#p').val(p);
						} else {
							no_data = "<p id='no_data' style='margin-top: 20px;text-align: center;'>没有更多数据</p>";
							$('#load').val('0');
							// alert_crm(data.data);
						}
					}
				});
				if (no_data == '') {
					setTimeout(function () {
						$("#message_div").scrollTop(nScrollTop + deviation - 200);
						$('#loading').remove();
						$("#list").append(temp);
						loading = false;
					}, 1500);   //模拟延迟
				} else {
					$('#loading').remove();
					$("#list").append(no_data);
					loading = false;
				}
			}
			if (nScrollTop == 0) {  //滚动到头部执行事件
				//alert("我到头部了");
			}
		});
	});
	$("#dialog-message-send").dialog({
		autoOpen: false,
		modal: true,
		width: 800,
		maxHeight: 600,
		position: ["center", 100]
	});
	$("#dialog-role-info").dialog({
		autoOpen: false,
		modal: true,
		width: 600,
		maxHeight: 600,
		position: ["center", 100]
	});
	$(function () {
		$("#send").click(function () {
			$('#dialog-message-send').dialog('open');
			$('#dialog-message-send').load('<?php echo U("message/send");?>');
		});
		$('#delete_receive').click(function () {
			if (confirm('<?php echo L('ARE_YOU_DELETE');?>')) {
				$("#form1").attr('action', '<?php echo U("message/delete", "model=receive");?>');
				$("#form1").submit();
			}
		});
		$('#delete_role').click(function () {
			if (confirm('<?php echo L('ARE_YOU_DELETE');?>')) {
				$("#form2").attr('action', '<?php echo U("message/message_delete");?>');
				$("#form2").submit();
			}
		});
		$('#set_read_receive').click(function () {
			if (confirm('<?php echo L('CONFIRM_TO_SET_MESAAGE_READ');?>')) {
				$("#form1").attr('action', '<?php echo U("message/setRead");?>');
				$("#form1").submit();
			}
		});
		$('#set_read_send').click(function () {
			if (confirm('<?php echo L('CONFIRM_TO_SET_MESAAGE_READ');?>')) {
				$("#form2").attr('action', '<?php echo U("message/setRead");?>');
				$("#form2").submit();
			}
		});
		$(".role_info").click(function () {
			$role_id = $(this).attr('rel');
			$('#dialog-role-info').dialog('open');
			$('#dialog-role-info').load('<?php echo U("user/dialoginfo","id=");?>' + $role_id);
		});
	});
	//状态标记,type=1已读，type=2未读
	// $('.is_mark').click(function(){
	$(document).on('click', '.is_mark', function () {
		var message_id = $(this).attr('rel');
		var is_mark = $(this).attr('rel1');
		$.ajax({
			type: 'get',
			url: "<?php echo U('message/message_mark','message_id=');?>" + message_id,
			async: false,
			success: function (data) {
				if (data.status == 1) {
					// var temp = '';
					if (is_mark == 1) {
						// 标记为未读
						// $('#content_'+message_id).css('text-decoration','none');
						$('#is_mark_' + message_id).attr('rel1', '0');
						$('#is_mark_' + message_id).attr('title', '标记为已读');
						$('#content_' + message_id).css('color', '#000');
						$('#mark_icon_' + message_id).html('<i class="fa fa-flag-o"></i>');
					} else {
						//标记为已读
						// $('#content_'+message_id).css('text-decoration','line-through');
						$(this).attr('rel1', '1');
						$(this).attr('title', '标记为未读');
						$('#content_' + message_id).css('color', '#a6a6a6');
						$('#mark_icon_' + message_id).html('<i class="fa fa-flag"></i>');
					}
					// swal("操作成功！", "站内信标记成功！", "success");
				} else {
					swal({
						title: "操作失败！",
						text: data.info,
						type: "error"
					})
					return false;
				}
			},
			dataType: 'json'
		});
	});

	//任务
	/*任务详情 加载的圈圈效果*/
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
	document.onmousedown = function (event) {
		if ($(".litebox-overlay").length > 0) return;
		if ($("#dialog_open").val() == 1) return;
		if (event.target.className == 'right-sidebar-toggle-task') return;

		var div = document.getElementById("right-sidebar-task");
		var x = event.clientX;
		var y = event.clientY;
		var divx1 = div.offsetLeft;
		var divy1 = div.offsetTop;
		var divx2 = div.offsetLeft + div.offsetWidth;
		var divy2 = div.offsetTop + div.offsetHeight;
		if (x < divx1 || x > divx2 || y < divy1 || y > divy2) {
			if ($("#right-sidebar-task").css('right') == '0px') {
				$("#right-sidebar-task").animate({ right: '-60%' }, 400);
			}
		}
		div = document.getElementById("right_market_view");
		x = event.clientX;
		y = event.clientY;
		divx1 = div.offsetLeft;
		divy1 = div.offsetTop;
		divx2 = div.offsetLeft + div.offsetWidth;
		divy2 = div.offsetTop + div.offsetHeight;
		if (x < divx1 || x > divx2 || y < divy1 || y > divy2) {
			if ($("#right_market_view").css('right') == '0px') {
				$("#right_market_view").animate({ right: '-60%' }, 400);
			}
		}
	}
	//任务详情弹出
	$(document).on('click', '.task_view', function () {
		var task_id = $(this).attr('rel');
		// var is_show = document.getElementById("is_show").value;
		$(".emoji_container").remove();
		if ($("#right-sidebar-task").css('right') != '0px') {
			$("#right-sidebar-task").animate({ right: '0px' }, 600);
		}

		$('#task-content').html(detail_html);
		$('#sidebar-container').load("<?php echo U('task/view','task_id=');?>" + task_id);
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
<div id="right-sidebar-task">
	<!--the css for jquery.mCustomScrollbar-->
	<link rel="stylesheet" href="__PUBLIC__/emoji/css/jquery.mCustomScrollbar.min.css" />
	<!--the css for this plugin-->
	<link rel="stylesheet" href="__PUBLIC__/emoji/css/jquery.emoji.css" />
	<!--(Optional) the js for jquery.mCustomScrollbar's addon-->
	<script src="__PUBLIC__/emoji/js/jquery.mousewheel-3.0.6.min.js"></script>
	<!--the js for jquery.mCustomScrollbar-->
	<script src="__PUBLIC__/emoji/js/jquery.mCustomScrollbar.min.js"></script>
	<!--the js for this plugin-->
	<script src="__PUBLIC__/emoji/js/jquery.emoji.js"></script>

	<div class="sidebar-container" id="sidebar-container">

	</div>
</div>