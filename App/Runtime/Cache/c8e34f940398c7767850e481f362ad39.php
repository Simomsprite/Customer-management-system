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
		<h1 class="abe-header-title">个人中心</h1>
	</header>
	<div class="uc-top">
		<a href="<?php echo U('Userm/userimgs');?>" class="abe-block">
			<i href="" class="iconfont">&#xe607;</i>
			<span class="uhead">
				<?php if(is_array($user_list)): $i = 0; $__LIST__ = $user_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['thumb_path']): ?><img src="<?php echo ($vo["img"]); ?>" alt="">
						<?php else: ?><img class="img-circle" style="width:32px;height:32px;" src="__PUBLIC__/img/avatar_default.png"/><?php endif; endforeach; endif; else: echo "" ;endif; ?>
			</span>

			<h1><?php echo ($_SESSION['full_name']); ?></h1>
			<h2><?php echo ($_SESSION['role_name']); ?></h2></a>
	</div>
	<ul class="uc-nav">
		<li><a href="<?php echo U('Userm/edit');?>"><i class="iconfont mrg_5">&#xe660;</i>个人资料<i class="iconfont arrow-r">&#xe607;</i></a></li>
		<li><a href="<?php echo U('Userm/editpwd');?>"><i class="iconfont mrg_5">&#xe62f;</i>更改密码<i class="iconfont arrow-r">&#xe607;</i></a></li>
		<li><a href="<?php echo U('Eventm/index');?>"><i class="iconfont mrg_5">&#xe62f;</i>我的日程<i class="iconfont arrow-r">&#xe607;</i></a></li>
		<li><a href="<?php echo U('Leadsm/analytics');?>"><i class="iconfont mrg_5">&#xe62f;</i>我的报表<i class="iconfont arrow-r">&#xe607;</i></a></li>
		<li><a href="<?php echo U('Indexm/brielog');?>"><i class="iconfont mrg_5">&#xe62f;</i>简报<i class="iconfont arrow-r">&#xe607;</i></a></li>
		<li><a href="<?php echo U('Userm/logout');?>"><i class="iconfont mrg_5">&#xe62f;</i>退出登录<i class="iconfont arrow-r">&#xe607;</i></a></li>
	</ul>
<div class="footer-menu footer-new">
    <a href="<?php echo U('Indexm/index');?>" id="Indexm"><i class="iconfont">&#xe60a;</i>首页</a>
    <a href="<?php echo U('Leadsm/index');?>" id="Leadsm"><i class="iconfont" >&#xe601;</i>线索</a>
    <a href="<?php echo U('Customerm/index');?>" id="Customerm"><i class="iconfont" >&#xe6a7;</i>客户</a>
    <a href="#" id="chat" name="chat" id=""><i class="iconfont">&#xe633;</i>聊天</a>
    <a href="<?php echo U('Userm/index');?>" id="Userm"><i class="iconfont">&#xe63b;</i>我的</a>
</div>
<script>
$(function () {
    var i = "<?php echo (MODULE_NAME); ?>";
    console.log(i);
    if (i == 'Indexm'){
        $('#Indexm').addClass("active");
    }else if(i == 'Leadsm'){
        $('#Leadsm').addClass("active");
    }else if(i == 'Customerm'){
        $('#Customerm').addClass("active");
    }else if(i == 'Userm'){
        $('#Userm').addClass("active");
    }
})

$('#chat').click(function () {
    var user_id = $("#chat1").val();
    var name = $("#chat2").val();
    $.ajax({
        url : "<?php echo U('Indexm/chatReg');?>",
        type: "POST",
        data:{user_id,name},
        success: function(msg) {
            if (msg) {
                console.log("五哥最帅");
            } else {
                console.log("五个最胖");
            }
        }
    });
});

$('#addFriend').click(function(){
    var user_id = $("#chat1").val();
    $.ajax({
        url:"<?php echo U('Indexm/chatAddFriends');?>",
        type: "POST",
        data:{user_id},
        success:function (msg) {
            showtab(msg);
        }
    });
});

</script>
</div>
</body>
</html>
<script>
	function logout(){
		showtab(3,'是否退出登录');
	}
	function　closetab(){
		window.location.href="<?php echo U('userm/logout');?>";
	}
</script>