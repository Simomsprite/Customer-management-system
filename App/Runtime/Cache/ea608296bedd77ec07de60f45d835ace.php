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
    <header class="abe-header am-header-default"><a href="#" class="return iconfont">&#xe612;</a>
        <h1 class="abe-header-title">登录</h1>
    </header>
    <div class="login-box">
        <div class="abe-txtc abe-ft32 vi-blue pdb_30 pdt_40">用户登录</div>
        <form class="formtheme" id="form3">
            <ul class="login-form">
                <li class="txtbox"><i class="iconfont">&#xe63b;</i>
                    <input type="text" class="txt" name="name" id="name"  placeholder="请输入账号">
                </li>
                <li class="txtbox"><i class="iconfont">&#xe626;</i>
                    <input type="password" class="txt" name="password" id="password" placeholder="请输入密码">
                </li>
                <li class="abe-txtc pdt_10">
                    <input id="loginsub" value="登录" class="pbtn ptxt100p">
                </li>
                <li class="clearfix pdt_10 pdl_10 pdr_10"><a href="#" class="abe-fr">忘记密码?</a></li>
            </ul>
        </form>
    </div>
</div>
<script>
    $('#loginsub').click(function() {
        $.base64.utf8encode = true;
        if($('#name').val() == ''){
            showtab('3','账号不能为空');
            return false;
        }
        if($('#password').val() == ''){
            showtab('3','密码不能为空');
            return false;
        }
        if($('#password').val()){
            $('#password').val($.md5($.trim($('#password').val())));
        }

        $.ajax({
            url : "<?php echo U('Userm/login');?>",
            type: "POST",
            data:$("#form3").serialize(),
            async: true,
            success: function(data) {
                if(data == 1){
                    //showtab('1',"登录成功");
                    //window.location.reload();
                    window.location.href="<?php echo U('Indexm/index');?>";
                }else if (data == 2){
                    $('#password').val("");
                    showtab('2',"请正确输入用户名和密码");
                    return false;
                }else if (data == 3){
                    $('#password').val("");
                    showtab('2',"您的账号未通过审核，请联系管理员");
                    return false;
                }else if (data == 4){
                    $('#password').val("");
                    showtab('2',"您的账号正在审核中，请耐心等待");
                    return false;
                }else if (data == 5){
                    $('#password').val("");
                    showtab('2',"此账号已停用");
                    return false;
                }else if (data == 6){
                    $('#password').val("");
                    showtab('2',"系统没有给您分配任何岗位，请联系管理员");
                    return false;
                }else if (data == 7){
                    $('#password').val("");
                    showtab('2',"用户名或密码错误");
                    return false;
                }
            }
        });
    });
</script>
</body>
</html>