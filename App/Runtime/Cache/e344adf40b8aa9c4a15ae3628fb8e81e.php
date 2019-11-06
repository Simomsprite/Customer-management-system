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
        <h1 class="abe-header-title">添加沟通日志</h1>
    </header>
    <form id="add-form" action="<?php echo U('Logm/add');?>" method="post">
    <input type='hidden' name="r" value="rLeadsLog"/>
    <input type='hidden' name="module" value="leads"/>
    <input type='hidden' id="leads_id" name="id" value="<?php echo ($leads_id); ?>"/>
    <input type='hidden' name="role_id" value="<?php echo (session('role_id')); ?>"/>
    <ul class="cust-form">
        <li class="titbox"><span class="tit">跟进类型：</span>
            <div class="txtbox">
                <select name="status_id" id="status_id" class="pbsele w100p" onchange="selectStatus()">
                    <option value="">--请选择--</option>
                    <?php if(is_array($status_list)): $i = 0; $__LIST__ = $status_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
        </li>
        <li class="titbox"><span class="tit">下次联系时间：</span>
            <div class="txtbox">
                <input type="text" class="txt ptxt100p" placeholder="请选择下次联系时间" name="nextstep_time" id="nextstep_time" onClick="WdatePicker({dateFmt:'yyyy-MM-dd'})">
                <i class="iconfont arrow">&#xe607;</i>
            </div>
        </li>
        <li class="titbox"><span class="tit">日志内容：<em class="abe-red">*</em></span>
            <div class="txtbox">
                <input type="text" id="log_content" name="content" class="txt ptxt100p" placeholder="添加沟通日志内容">
            </div>
        </li>
    </ul>
    <div class="sub-box">
        <input type="button" id="add_log" value="确定" class="pbtn pbtn-sm pbtnw80p">
    </div>
    </form>
</div>
<script src="__PUBLIC__/waps/js/mobiscroll_date.js" charset="gb2312"></script>
<script src="__PUBLIC__/waps/js/mobiscroll.js"></script>
<script>
    function selectStatus() {
        var status_id = $("#status_id option:selected").val();
        var temp = '<option value="">--请选择--</option>';
        $.ajax({
            type: 'post',
            url: "<?php echo U('setting/getReplyByStatus');?>",
            data: { status_id: status_id },
            async: false,
            success: function (data) {
                if (data.data.length > 0) {
                    $.each(data.data, function (k, v) {
                        temp += '<option value="' + v.content + '">' + v.str_content + '</option>';
                    });
                }
            },
            dataType: 'json'
        });
        $('#replay_list').html(temp);
    }

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
            dateFormat: 'yyyy-mm-dd',
            dateOrder : 'yyyymmdd',
            lang: 'zh',
            showNow: true,
            nowText: "今天",
            startYear: currYear - 50, //开始年份
            endYear: currYear + 10 //结束年份
        };
        $("#nextstep_time").mobiscroll($.extend(opt['datetime'], opt['default']));

    });

    /*ajax 提交记录*/
    $('#add_log').click(function(){
        var content = $("#log_content").val();
        content = $.trim(content);
        if (content == '') {
            showtab('3','日志内容不能为空');
            return false;
        }
        var log_type = 'rLeadsLog';
        $(this).prop('disabled',true);
        $.post("<?php echo U('Log/add');?>", $("#add-form").serialize(), function(data){
            if(data.status == 1){
                window.location.href = "<?php echo U('leadsm/view',array('id'=>$leads_id,'by'=>'me'));?>";
            }else{
                showtab('2','添加失败, 请重试');
            }
        });
    });
</script>
</body>
</html>