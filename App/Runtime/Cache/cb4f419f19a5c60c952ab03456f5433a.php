<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<title></title>
	<link type="text/css" rel="stylesheet" href="__PUBLIC__/waps/style/reset.css">
	<link type="text/css" rel="stylesheet" href="__PUBLIC__/waps/style/index.css">
	<link type="text/css" rel="stylesheet" href="__PUBLIC__/waps/style/crm.css">
	<link type="text/css" rel="stylesheet" href="__PUBLIC__/waps/style/calendar.css">
	<link type="text/css" rel="stylesheet" href="__PUBLIC__/waps/style/swiper.min.css">
	<link type="text/css" rel="stylesheet" href="__PUBLIC__/waps/style/mobiscroll.css">
	<link type="text/css" rel="stylesheet" href="__PUBLIC__/waps/style/mobiscroll_date.css">
	<link href="__PUBLIC__/style/font-awesome/css/font-awesome.css" rel="stylesheet">
	<link href="__PUBLIC__/css/font-awesome.min.css" rel="stylesheet">
	<script src="__PUBLIC__/waps/js/jquery-1.7.1.min.js"></script>
	<script src="__PUBLIC__/waps/js/jquery.circliful.js"></script>
	<script src="__PUBLIC__/waps/js/echarts.common.min.js"></script>
	<script src="__PUBLIC__/js/jquery.base64.js"></script>
	<script src="__PUBLIC__/js/jquery.md5.js"></script>
	<script src="__PUBLIC__/waps/js/PCASClass.js"></script>
	<script type="text/javascript" src="__PUBLIC__/waps/js/tab2.js"></script>
	<script type="text/javascript" src="__PUBLIC__/waps/js/swiper3.06.jquery.min.js"></script>
</head>
<script>
	function showtab(a,b){
        $("#success").hide();
        $("#error").hide();
        $("#warn").hide();

        if(a == 1){
            $("#title").html('成功');
            $("#success").show();
        }else if (a == 2){
            $("#title").html('错误');
            $("#error").show();
        }else if (a == 3){
            $("#title").html('提示');
            $("#warn").show();
        }
        if(b){
            $("#content").html(b);
        }
        $("#warning").show();
        $("#popup-bg").show();
    }

    function closestab() {
        $("#warning").hide();
        $("#popup-bg").hide();
    }


    function showtab1(a,b){
        $("#success1").hide();
        $("#error1").hide();
        $("#warn1").hide();

        if(a == 1){
            $("#title1").html('成功');
            $("#success1").show();
        }else if (a == 2){
            $("#title1").html('错误');
            $("#error1").show();
        }else if (a == 3){
            $("#title1").html('提示');
            $("#warn1").show();
        }
        if(b){
            $("#content1").html(b);
        }
        $("#warning1").show();
        $("#popup-bg1").show();
    }

    function closestab1() {
        $("#warning1").hide();
        $("#popup-bg1").hide();
    }

</script>
<body>
<div id="tab">
	<div class="warning" id="warning" style="display:none;">
		<a href="javascript:void(0);" onclick="closestab()" class="close iconfont">&#xe6cb;</a>
		<div class="tit-ico" id="success" style="display: none"><i class="iconfont ok">&#xe6a1;</i></div>
		<div class="tit-ico" id="error" style="display: none"><i class="iconfont error">&#xe6dc;</i></div>
		<div class="tit-ico" id="warn" style="display: none"><i class="iconfont noun">&#xe690;</i></div>
		<div class="tit" id="title">提示</div>
		<div class="winfo" id="content"></div>
		<div><input type="button" onclick="closetab()" class="pbtn pb-gray pbtn-sm" value="确定"></div>
	</div>
	<div class="popup-bg" id="popup-bg" style="display:none;"></div>

	<div class="warning" id="warning1" style="display:none;">
		<a href="javascript:void(0);" onclick="closestab1()" class="close iconfont">&#xe6cb;</a>
		<div class="tit-ico" id="success1" style="display: none"><i class="iconfont ok">&#xe6a1;</i></div>
		<div class="tit-ico" id="error1" style="display: none"><i class="iconfont error">&#xe6dc;</i></div>
		<div class="tit-ico" id="warn1" style="display: none"><i class="iconfont noun">&#xe690;</i></div>
		<div class="tit" id="title1">提示</div>
		<div class="winfo" id="content1"></div>
		<div><input type="button" onclick="closestab1()" class="pbtn pb-gray pbtn-sm" value="确定"></div>
	</div>
	<div class="popup-bg" id="popup-bg1" style="display:none;"></div>
</div>




<head>
    <!-- 新 Bootstrap 核心 CSS 文件 -->
    <link href="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
    <script src="https://cdn.staticfile.org/jquery/2.1.1/jquery.min.js"></script>

    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
    <script src="https://cdn.staticfile.org/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<style type="text/css">
    .tabone {
        width: 100%;
        height: 30%;
        text-align: center;
        border: 1px deepskyblue;
        padding: 50px;
        margin: 10px;
        font-size: 13px;
        color: powderblue;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 10px;
    }

    .tabone td {
        border-radius: 10px;
    }

    .tabone th {
        font-size: 15px;
        border-radius: 10px;
    }

    .abe-header-title {
        margin-top: 15px;
        margin-right: 0px;
        margin-bottom: 0px;
        margin-left: 0px;
    }

</style>
<div class="wrap-box">
    <header class="abe-header am-header-default"><a href="javascript:history.go(-1);"
                                                    class="return iconfont">&#xe612;</a>
        <h1 class="abe-header-title">简报</h1>
    </header>

        <table border="1px" class="tabone  table-hover">

            <thead class="th">
            <tr>
                <th>简报</th>
                <th>新增客户</th>
                <th>新增联系人</th>
                <th>新增商机</th>
                <th>访客计划</th>
                <th>新增沟通日志</th>
                <th>工作日志</th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td>本日新增</td>
                <td><?php echo ($anly_count['customer_count'][0]); ?>个</td>
                <td><?php echo ($anly_count['contacts_count'][0]); ?>个</td>
                <td><?php echo ($anly_count['business_count'][0]); ?>个</td>
                <td><span title="已完成访客计划"><?php echo ($anly_count['done_visitor_plan_count'][0]); ?></span> /
                    <span title="待完成访客计划"><?php echo ($anly_count['visitor_plan_count'][0]); ?></span></td>
                <td><?php echo ($anly_count['log_count'][0]); ?>篇</td>
                <td>
                    <?php if($mylog_count_today > 0): ?><span style="font-size:15px;"><i class="fa fa-check-circle" style="color:#19AA8D;"></i>&nbsp;&nbsp;今日已完成</span>
                        <?php else: ?>
                        <span style="font-size:15px;"><i class="fa fa-exclamation-circle" style="color:#FFCC00;"></i>&nbsp;&nbsp;今日未填写</span><?php endif; ?>
                </td>
            </tr>
            <tr>
                <td>本周新增</td>
                <td><?php echo ($anly_count['customer_count'][1]); ?>个</td>
                <td><?php echo ($anly_count['contacts_count'][1]); ?>个</td>
                <td><?php echo ($anly_count['business_count'][1]); ?>个</td>
                <td>
                    <div>
                        <span title="已完成访客计划"><?php echo ($anly_count['done_visitor_plan_count'][1]); ?></span> /
                        <span title="待完成访客计划"><?php echo ($anly_count['visitor_plan_count'][1]); ?></span>
                    </div>
                <td><span style="font-size:15px;"><?php echo ($anly_count['log_count'][1]); ?>篇</span></td>
                <td><?php echo ($anly_count['log_count'][1]); ?>篇</td>
            </tr>
            <tr>
                <td>本月新增</td>
                <td><?php echo ($anly_count['customer_count'][2]); ?>个</td>
                <td><?php echo ($anly_count['contacts_count'][2]); ?>个</td>
                <td><?php echo ($anly_count['business_count'][2]); ?>个</td>
                <td>
                    <div>
                        <span title="已完成访客计划"><?php echo ($anly_count['done_visitor_plan_count'][2]); ?></span> /
                        <span title="待完成访客计划"><?php echo ($anly_count['visitor_plan_count'][2]); ?></span>
                    </div>
                </td>
                <td><span style="font-size:15px;"><?php echo ($anly_count['log_count'][2]); ?>篇</span></td>
                <td><?php echo ($anly_count['log_count'][2]); ?>篇</td>
            </tr>
            <tr>
                <td>本年新增</td>
                <td><?php echo ($anly_count['customer_count'][3]); ?>个</td>
                <td><?php echo ($anly_count['contacts_count'][3]); ?>个</td>
                <td><?php echo ($anly_count['business_count'][3]); ?>个</td>
                <td>
                    <div>
                        <span title="已完成访客计划"><?php echo ($anly_count['done_visitor_plan_count'][3]); ?></span> /
                        <span title="待完成访客计划"><?php echo ($anly_count['visitor_plan_count'][3]); ?></span>
                    </div>
                </td>
                <td><span style="font-size:15px;"><?php echo ($anly_count['log_count'][3]); ?>篇</span></td>
                <td><?php echo ($anly_count['log_count'][3]); ?>篇</td>
            </tr>
            </tbody>
        </table>

</div>