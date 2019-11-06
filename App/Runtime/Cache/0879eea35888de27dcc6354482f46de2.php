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
    <header class="abe-header am-header-default"> <a href="javascript:history.go(-1)" class="return iconfont">&#xe612;</a>
        <h1 class="abe-header-title">修改密码</h1>
    </header>
    <form class="form-inline" id="form3" method="post">
        <input type="hidden" name="user_id" value="<?php echo ($user["user_id"]); ?>"/>
        <ul class="cust-form">
            <li class="titbox"><span class="tit">旧密码：</span>
                <div class="txtbox">
                <input type="password" name="old_password" id="old_password" class="txt ptxt100p" placeholder="">
                </div>
            </li>
            <li class="titbox"><span class="tit">新密码：</span>
                <input type="password" name="new_password" id="new_password" class="txt ptxt100p" placeholder="">
            </li>
            <li class="titbox"><span class="tit">重复新密码：</span>
                <input type="password" name="confirm_password" id="confirm_password" class="txt ptxt100p" placeholder="">
            </li>
        </ul>
        <div class="sub-box pdt_20">
            <input type="button"  id="submit_password" value="保存修改" class="pbtn pbtnw80p">
        </div>
    </form>

    <script>

        $('#new_password').blur(function(){
            var new_pass = $(this).val();
            if(new_pass.length < 6 || new_pass.length > 16){
                showtab1('2',"密码长度应为6-16位字符！");
                return false;
            }else{
                return true;
            }
        });

        $('#confirm_password').blur(function(){
            var two_pass = $(this).val();
            if(two_pass){
                var new_pass = $('#new_password').val();
                if(two_pass != new_pass){
                    showtab1('2',"两次输入的密码不一致！");
                    $('confirm_password').focus();
                    return false;
                }
            }
        });

        $('#submit_password').click(function() {
            $.base64.utf8encode = true;
            if($('#old_password').val() == ''){
                showtab1('2',"旧密码不能为空！");
                return false;
            }
            if($('#new_password').val() == ''){
                showtab1('2',"新密码不能为空！");
                return false;
            }

            if($('#confirm_password').val() == ''){
                showtab1('2',"重复密码不能为空！");
                return false;
            }

            $('#old_password').val($.md5($('#old_password').val()));
            $('#new_password').val($.md5($('#new_password').val()));
            $('#confirm_password').val($.md5($('#confirm_password').val()));

            $.ajax({
                url : "<?php echo U('Userm/editPassword');?>",
                type: "POST",
                data:$("#form3").serialize(),
                async: true,
                success: function(data) {
                    if(data.status == 1){
                        showtab1('1',"修改成功，即将刷新页面!");
                        //window.location.reload();
                        window.location.href="<?php echo U('user/index');?>";
                    }else{
                        showtab1('2',"修改失败"+data.info);
                        $('#old_password').val('');
                        $('#new_password').val('');
                        $('#confirm_password').val('');
                        return false;
                    }
                }
            });
        });

    </script>
</div>
</body>
</html>