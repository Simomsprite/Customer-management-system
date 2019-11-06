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
    <header class="abe-header am-header-default"> <a href="javascript:goIndex();" class="return iconfont">&#xe612;</a>
        <h1 class="abe-header-title">联系人编辑</h1>
    </header>
    <form class="form-horizontal" action="<?php echo U('Contactsm/edit');?>" method="post" id="form" role="form">
        <input type="hidden" name="contacts_id" id="contacts_id" value="<?php echo ($contacts["contacts_id"]); ?>"/>
        <input type="hidden" name="refer_url" value="<?php echo ($refer_url); ?>"/>
        <ul class="cust-form">
            <?php if(is_array($field_list['main'])): $key = 0; $__LIST__ = $field_list['main'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key % 2 );++$key;?><!-- 主要字段 -->
                <li class="titbox">
                    <span class="tit"><?php echo ($vo["name"]); ?>：<?php if(($vo["is_null"] == '1' && $vo["is_validate"] == '1') || $vo["is_unique"] == '1'): ?><em class="abe-red">*</em><?php endif; ?></span>
                    <?php if($vo['form_type'] == 'address'): if($vo['tip_start'] == 1): ?><div class="col-md-7">
                                <?php echo ($vo["html"]); ?>
                            </div>
                            <div class="col-md-1"></div>
                            <?php else: ?>
                            <div class="col-md-8">
                                <?php echo ($vo["html"]); ?>
                            </div><?php endif; ?>
                        <?php else: ?>
                        <div class="col-md-6">
                            <?php echo ($vo["html"]); ?>
                        </div>
                        <?php if($vo['tip_start'] == 1): ?><div class="col-md-2"></div>
                            <?php else: ?>
                            <div class="col-md-2"></div><?php endif; endif; ?>
                </li><?php endforeach; endif; else: echo "" ;endif; ?>
            <?php if(is_array($field_list['data'])): $key = 0; $__LIST__ = $field_list['data'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key % 2 );++$key;?><!-- 附加字段 -->
                <?php if($vo['in_add']){ ?>
                <li class="titbox">
                    <span class="tit"><?php echo ($vo["name"]); ?>：<?php if(($vo["is_null"] == '1' && $vo["is_validate"] == '1') || $vo["is_unique"] == '1'): ?><em class="abe-red">*</em><?php endif; ?></span>
                    <?php if($vo['form_type'] == 'address'): if($vo['tip_start'] == 1): ?><div class="col-md-7">
                                <?php echo ($vo["html"]); ?>
                            </div>
                            <div class="col-md-1"><span style="color: red;line-height: 32px;margin-left: 10px;">*</span></div>
                            <?php else: ?>
                            <div class="col-md-8">
                                <?php echo ($vo["html"]); ?>
                            </div><?php endif; ?>
                        <?php else: ?>
                        <div class="col-md-6">
                            <?php echo ($vo["html"]); ?>
                        </div>
                        <?php if($vo['tip_start'] == 1): ?><div class="col-md-2"><span style="color: red;line-height: 32px;margin-left: 10px;">*</span></div>
                            <?php else: ?>
                            <div class="col-md-2"></div><?php endif; endif; ?>
                </li>
                <?php } endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <div class="sub-box">
            <input type="button" value="取消" onclick="goIndex()" name="button" class="pbtn pbtn-sm pbtnw30p mrg_15"/>
            <input type="submit" value="确定"  class="pbtn  pbtnw30p pbtn-sm" onclick="return myValidate()">
        </div>
    </form>
</div>
</div>
<script>
    function goIndex(){
        showtab(3,'是否退出编辑联系人')
    }

    function　closetab(){
        window.location.href="javascript:history.go(-1);";
    }

    function myValidate(){
        let mobile=$("#telephone").val();
        let phoneReg = /^1(3|4|5|6|7|8|9)\d{9}$/;
        let qq=$("#qq_no").val();
        let qqReg = /^[1-9]\d{4,9}$/;
        let email=$("#email").val();
        let emailReg=/^(\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*)?$/;
        if(mobile!="" && !phoneReg.test(mobile)) {
            showtab1(2,"手机格式不正确");
            return false;
        }else if (qq!="" && !qqReg.test(qq)) {
            showtab1(2,"qq格式不正确");
            return false;
        }else if (email!="" && !emailReg.test(email)){
            showtab1(2,"email格式不正确");
            return false;
        }
    }
</script>

</body>
</html>