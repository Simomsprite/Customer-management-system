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




<body>
<div class="wrap-box">
    <header class="abe-header am-header-default"> <a href="javascript:history.go(-1)" class="return iconfont">&#xe612;</a>
        <h1 class="abe-header-title">基本信息</h1>
    </header>
    <form class="form-inline" id="add-form" action="" method="post">
        <input type="hidden" name="user_id" value="<?php echo ($user["user_id"]); ?>"/>
        <input type="hidden" name="r_url" value="<?php echo ($r_url); ?>">
    <ul class="cust-form">
        <li class="titbox">
            <span class="tit">姓名：<em class="abe-red">*</em></span>
            <div class="txtbox">
                <input type="text" name="full_name" value="<?php echo ($user['full_name']); ?>" class="txt ptxt100p" placeholder="请输入姓名">
            </div>
        </li>

        <li class="titbox">
            <span class="tit">登录账号：<em class="abe-red">*</em></span>
            <div class="txtbox">
                <?php if($is_edit == 1): ?><input type="text" name="name" value="<?php echo ($user['login_name']); ?>" class="txt ptxt100p" placeholder="请输入登录账号">
                <?php else: ?>
                    <?php echo ($user["login_name"]); endif; ?>
            </div>
        </li>

        <li class="titbox">
            <span class="tit">员工编号：<em class="abe-red">*</em></span>
            <div class="txtbox">
                <?php if($is_edit == 1): ?><input type="text" id="number" name="number" value="<?php echo ($user['number']); ?>" placeholder="请输入员工编号" class="txt ptxt100p">
                    <input type="hidden" name="prefixion" id="prefixion" value="<?php echo ($user['prefixion']); ?>">
                <?php else: ?>
                    <input type="hidden" name="prefixion" value="<?php echo ($user['prefixion']); ?>"/>
                    <input type="hidden" name="number" value="<?php echo ($user['number']); ?>" />
                    <?php echo ($user['prefixion']); echo ($user['number']); endif; ?>
            </div>
        </li>

        <li class="titbox"><span class="tit">角色：</span>
            <div class="txtbox">
                <?php if($is_edit == 1): ?><select name="type" id="type" class="pbsele w100p">
                        <?php if(is_array($user_type_list)): $key = 0; $__LIST__ = $user_type_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key % 2 );++$key;?><option value="<?php echo ($key); ?>" <?php if($user['type'] == $key): ?>selected<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                <?php else: ?>
                    <?php echo ($user['type_name']); endif; ?>
                <i class="iconfont arrow">&#xe607;</i>
            </div>
        </li>

        <li class="titbox"><span class="tit"><?php echo L('SEX');?>：</span>
            <div class="txtbox">
                <select name="sex" id="sex" class="pbsele w100p">
                    <option value="1" <?php if($user['sex'] == 1): ?>selected<?php endif; ?>><?php echo L('MALE');?></option>
                    <option value="2" <?php if($user['sex'] == 2): ?>selected<?php endif; ?>><?php echo L('FEMALE');?></option>
                </select>
                <i class="iconfont arrow">&#xe607;</i>
            </div>
        </li>

        <li class="titbox"><span class="tit"><?php echo L('DEPARTMENT');?>：</span>
            <div class="txtbox">
                <select name="department_id" id="department" onchange="changeRoleContent()" class="pbsele w100p">
                    <option value="">--请选择--</option>
                    <?php if(is_array($department_list)): $i = 0; $__LIST__ = $department_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$temp): $mod = ($i % 2 );++$i;?><option <?php if($user['department_id'] == $temp['department_id']): ?>selected<?php endif; ?> value="<?php echo ($temp["department_id"]); ?>"><?php echo ($temp["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
                <i class="iconfont arrow">&#xe607;</i>
            </div>
        </li>

        <li class="titbox"><span class="tit"><?php echo L('POSITION');?>：</span>
            <div class="txtbox">
                <select name="position_id" id="role" class="pbsele w100p">
                    <option value="">--请选择--</option>
                    <?php if(is_array($position_list)): $i = 0; $__LIST__ = $position_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$temp): $mod = ($i % 2 );++$i;?><option <?php if($user['position_id'] == $temp['position_id']): ?>selected<?php endif; ?> value="<?php echo ($temp["position_id"]); ?>"><?php echo ($temp["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
                <i class="iconfont arrow">&#xe607;</i>
            </div>
        </li>

        <li class="titbox">
            <span class="tit">家乡：</span>
            <div class="txtbox">
                <input type="text" name="hometown" value="<?php echo ($user['hometown']); ?>" class="txt ptxt100p" placeholder="请输入家乡地址">
            </div>
        </li>

        <li class="titbox"><span class="tit">出生日期：</span>
            <div class="txtbox">
                <input type="text" class="txt ptxt100p" placeholder="请选择日期" name="birthday" id="birthday" value="<?php echo ($user['birthday']); ?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd'})">
                <i class="iconfont arrow">&#xe607;</i>
            </div>
        </li>

        <li class="titbox"><span class="tit">自我介绍：</span>
            <div class="txtbox">
                <input type="text" name="introduce" value="<?php echo ($user["introduce"]); ?>" class="txt ptxt100p" placeholder="清输入自我介绍">
            </div>
        </li>

        <li class="titbox"><span class="tit">是否参与考勤：</span>
            <div class="txtbox">
                <select name="kaoqin" class="pbsele w100p">
                    <option value="1" <?php if($user['kaoqin'] == 1): ?>selected<?php endif; ?>>是</option>
                    <option value="0" <?php if($user['kaoqin'] == 0): ?>selected<?php endif; ?>>否</option>
                </select>
                <i class="iconfont arrow">&#xe607;</i>
            </div>
        </li>
        <li class="titbox"><span class="tit">手机号码：</span>
            <div class="txtbox">
                <input type="text" name="telephone" id="telephone" value="<?php echo ($user["telephone"]); ?>" class="txt ptxt100p" placeholder="请输入手机号码">
            </div>
        </li>
        <li class="titbox"><span class="tit">办公电话：</span>
            <div class="txtbox">
                <input type="text" name="office_tel" id="office_tel" value="<?php echo ($user["office_tel"]); ?>" class="txt ptxt100p" placeholder="请输入办公电话">
            </div>
        </li>
        <li class="titbox"><span class="tit">QQ/MSN：</span>
            <div class="txtbox">
                <input type="text" name="qq" id="qq" value="<?php echo ($user["qq"]); ?>" class="txt ptxt100p" placeholder="请输入QQ/MSN">
            </div>
        </li>
        <li class="titbox"><span class="tit"><?php echo L('EMAIL');?>：</span>
            <div class="txtbox">
                <input name="email" type="text" value="<?php echo ($user["email"]); ?>" class="txt ptxt100p" placeholder="请输入邮箱">
            </div>
        </li>
        <li class="titbox"><span class="tit"><?php echo L('ADDRESS');?>：</span>
            <div class="txtbox">
                <input type="text" name="address" value="<?php echo ($user["address"]); ?>" class="txt ptxt100p" placeholder="请输入联系地址">
            </div>
        </li>
    </ul>
    <div class="sub-box">
        <input type="button" id="btn" class="pbtn pbtnw80p" value="<?php echo L('SAVE');?>"></input>
    </div>
    </form>
</div>
<script src="__PUBLIC__/waps/js/mobiscroll_date.js" charset="gb2312"></script>
<script src="__PUBLIC__/waps/js/mobiscroll.js"></script>
<script type="text/javascript">
    $("#btn").click(function(){
        $.ajax({
            type:'post',
            url: "<?php echo U('Userm/edit');?>",
            data: $("#add-form").serialize(),
            dataType: 'json',
            success: function (result) {
                if(result.status == '1'){
                    showtab1('3','操作成功');
                }else if(result.status == '2'){
                    showtab1('2',result.msg);
                }else{
                    alert(result);
                }
            }
        });
    });

    $('#number').blur(function(){
        var number = $(this).val();
        var prefixion = $('#prefixion').val();
        var user_id = "<?php echo ($user["user_id"]); ?>";
        $.ajax({
            type: "post",
            async:true,
            url: "<?php echo U('Userm/yanchong');?>",
            data: {number:number,user_id:user_id,prefixion:prefixion},
            dataType: "json",
            success : function(result){
                if(result.status != 1){
                    showtab1('3',result.info);
                    return false;
                }else{
                    return true;
                }
            }
        });
    });

    function changeRoleContent(){
        department_id = $('#department').val();
        if(department_id == ''){
            $("#role").html('');
        }else{
            $.ajax({
                type:'get',
                url:'index.php?m=Userm&a=getPositionlistByDepartment&id='+department_id,
                async:true,
                success:function(data){
                    options = '';
                    if(data.data){
                        console.log(data.data);
                        $.each(data.data, function(k, v){
                            options += '<option value="'+v.position_id+'">'+v.name+'</option>';
                        });
                    }
                    $("#role").html(options);
                },
                dataType:'json'
            });
        }
    }

    $('#role').click(
        function(){
            department_id = $('#department').val();
            if(department_id == ''){
                showtab1('1',"<?php echo L('SELECT_DEPARTMENT_FIRST');?>");
                return false;
            }
        }
    );

    $(function () {
        var currYear = (new Date()).getFullYear();
        var opt={};
        opt.date = {preset : 'date'};
        opt.datetime = {preset : 'datetime'};
        opt.time = {preset : 'time'};
        opt.default = {
            theme: 'android-ics light', //皮肤样式
            display: 'modal', //显示方式
            mode: 'scroller', //日期选择模式
            dateFormat: 'yy-mm-dd',
            dateOrder : 'yyyymmdd',
            lang: 'zh',
            showNow: true,
            nowText: "今天",
            startYear: currYear - 50, //开始年份
            endYear: currYear + 10 //结束年份
        };

        $("#birthday").mobiscroll($.extend(opt['date'], opt['default']));
        $("#entry").mobiscroll($.extend(opt['date'], opt['default']));

    });
</script>
</body>
</html>