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




<div class="wrap-box">
  <header class="abe-header am-header-default"> <a href="javascript:history.go(-1);" class="return iconfont">&#xe612;</a>
    <h1 class="abe-header-title">线索详情</h1>
  </header>
<div id="pagetwo" style="display: block">
    <ul class="cust-form">
        <li class="titbox"><span class="tit">来源：</span>
            <div class="txtbox">
                <input type="text" class="txt ptxt100p" readonly="readonly" value="<?php echo ($leads['source']); ?>">
            </div>
        </li>
        <li class="titbox"><span class="tit">职位：</span>
            <div class="txtbox">
                <input type="text" class="txt ptxt100p" readonly="readonly" value="<?php echo ($leads["crm_gzcqgt"]); ?>">
            </div>
        </li>

        <li class="titbox"><span class="tit">联系人姓名：</span>
            <div class="txtbox">
                <input type="text" class="txt ptxt100p" readonly="readonly" value="<?php echo ($leads["contacts_name"]); ?>">
            </div>
        </li>

        <li class="titbox"><span class="tit">下次联系时间：</span>
            <div class="txtbox">
                <input type="text" class="txt ptxt100p" readonly="readonly" value="<?php if($leads["nextstep_time"] != '0'): echo (date('Y-m-d H:i',$leads["nextstep_time"])); ?>"<?php else: ?>无<?php endif; ?>">
            </div>
        </li>
        <li class="titbox"><span class="tit">公司名：</span>
            <div class="txtbox">
                <input type="text" class="txt ptxt100p" readonly="readonly" value="<?php echo ($leads["name"]); ?>">
            </div>
        </li>
        <li class="titbox"><span class="tit">尊称：</span>
            <div class="txtbox">
                <input type="text" class="txt ptxt100p" readonly="readonly" value="<?php echo ($leads["saltname"]); ?>">
            </div>
        </li>
        <li class="titbox"><span class="tit">手机：</span>
            <div class="txtbox">
                <input type="text" class="txt ptxt100p" readonly="readonly" value="<?php echo ($leads["mobile"]); ?>">
            </div>
        </li>
        <li class="titbox"><span class="tit">邮箱：</span>
            <div class="txtbox">
                <input type="text" class="txt ptxt100p" readonly="readonly" value="<?php echo ($leads["email"]); ?>">
            </div>
        </li>
        <li class="titbox"><span class="tit">地址：</span>
            <div class="txtbox">
                <input type="text" class="txt ptxt100p" readonly="readonly" value="<?php echo ($leads["address"]); ?>">
            </div>
        </li>
        <li class="titbox"><span class="tit">下次联系内容：</span>
            <div class="txtbox">
                <input type="text" class="txt ptxt100p" readonly="readonly"  value="<?php echo ($leads["nextstep"]); ?>">
            </div>
        </li>
    </ul>
</div>
</div>
<link type="text/css" rel="stylesheet" href="__PUBLIC__/waps/style/style.css">
<script src="__PUBLIC__/waps/js/mobiscroll_date.js" charset="gb2312"></script>
<script src="__PUBLIC__/waps/js/mobiscroll.js"></script>

</body>
</html>