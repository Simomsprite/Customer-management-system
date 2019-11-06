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
        <h1 class="abe-header-title">日程详情</h1>
    </header>
        <input type="hidden" name="event_id" id="event_id" value="<?php echo ($event_info["event_id"]); ?>">
        <ul class="cust-form">
            <li class="titbox">
                <span class="tit">日程内容：</span>
                <div class="txtbox">
                    <input type="text" class="txt ptxt100p" name="subject" readonly="readonly" value="<?php echo ($event_info["subject"]); ?>"/>
                </div>
            </li>
            <li class="titbox">
                <span class="tit">开始时间：</span>
                <div class="txtbox">
                    <input type="text" class="txt ptxt100p" placeholder="请选择开始时间" readonly="readonly" name="start_date" id="USER_AGE" value="<?php echo (date('Y-m-d H:i',$event_info["start_date"])); ?>">
                </div>
            </li>
            <li class="titbox">
                <span class="tit">结束时间：</span>
                <div class="txtbox">
                    <input type="text" class="txt ptxt100p" placeholder="请选择结束时间" readonly="readonly" name="end_date" id="USER_AGE1" value="<?php echo (date('Y-m-d H:i',$event_info["end_date"])); ?>">
                </div>
            </li>
            <li class="titbox">
                <div class="col-sm-1"></div>
                <span class="tit" for="people">相关：</span>
                <?php if ($module) { ?>
                <div class="col-sm-3">
                    <input type="hidden" name="module" value="<?php echo ($module['module_name']); ?>" />
                    <input type="text" class="txt ptxt100p " name="" readonly="true" value="<?php echo ($module['module']); ?>" />
                </div>
                <div class="col-sm-5" style="padding-left: 5px;">
                    <input type="hidden" id="module_id" name="module_id" value="<?php echo ($module['id']); ?>">
                    <input type="text" class="txt ptxt100p " name="module_name" readonly="true" value="<?php echo ($module['name']); ?>" style="cursor:pointer;" title="请点击选择" placeholder="请点击选择" />
                </div>
                <?php } else { ?>
                <div class="col-sm-3">
                    <select name="module" class="pbsele w100p " onchange="changeContent()" id="select1" style="padding-right: 0px;">
                        <option value="">--请选择--</option>
                        <option value="contacts" <?php if($event_info["module"] == contacts): ?>selected="selected"<?php endif; ?>>联系人</option>
                        <option value="leads" <?php if($event_info["module"] == leads): ?>selected="selected"<?php endif; ?>>线索</option>
                        <option value="customer" <?php if($event_info["module"] == customer): ?>selected="selected"<?php endif; ?>>客户</option>
                        <option value="business" <?php if($event_info["module"] == business): ?>selected="selected"<?php endif; ?>>商机</option>
                        <option value="product" <?php if($event_info["module"] == product): ?>selected="selected"<?php endif; ?>>产品</option>
                    </select>
                </div>
                <!--<div class="col-sm-5" style="padding-left: 5px;">
                    <input type="hidden" id="module_id" name="module_id">
                    <a href="<?php echo U('customer/listdialog');?>"><input type="text" class="form-control" name="module_name" id="module_name" readonly="true" style="cursor:pointer;" title="请点击选择" placeholder="请点击选择" /></a>
                </div>-->
                <?php } ?>
            </li>
            <li class="titbox">
                <span class="tit">描述：</span>
                <div class="txtbox">
                    <textarea type="text" class="txt ptxt100p" rows="5" readonly="readonly" name="description"><?php echo ($event_info["description"]); ?></textarea>
                </div>
            </li>


            <div class="sub-box">
               <!-- <input type="button" name="button" value="取消" onclick="goIndex()" class="pbtn  pbtnw30p pbtn-sm mrg_15" >
                <input type="submit" name="button" value="提交日程" class="pbtn pbtn-sm pbtnw30p ">-->
            </div>
        </ul>
</div>
<a class="faq-btn iconfont">&#xe60b;</a>
<div class="faq-btn-box ad-faq">
   <a href="<?php echo U('Eventm/edit',array('event_id'=>$event_info['event_id']));?>"><i class="iconfont icolor3">&#xe6f8;</i>编辑</a>
    <a href="javascript:showtab(3,'是否删除该条数据');"><i class="iconfont icolor5">&#xe603;</i>删除</a>
</div>
<div class="popup-bg" id="heimu" style="display: none;"></div>
</div>


<script src="__PUBLIC__/waps/js/mobiscroll_date.js" charset="gb2312"></script>
<script src="__PUBLIC__/waps/js/mobiscroll.js"></script>
<script type="text/javascript">
    $(function(){ $("select").attr("disabled", "disabled");
        $("select").prop("disabled", true);});

    $(function(){
        $("#heimu").css({top:"-100%"});
        $(".faq-btn").click(function(){
            $(".faq-btn-box").animate({bottom:"0"});
            $("#heimu").show();
            $("#heimu").animate({top:"0"},300);
        });
        $("#heimu").click(function(){
            $(this).animate({top:"-100%"},300);
            $(".faq-btn-box").animate({bottom:"-200px"});
        });
        $(".faq-btn-box a").click(function(){
            $(".faq-btn-box").animate({bottom:"-200px"});
            $("#heimu").animate({top:"-100%"},300);
        });
        $(".clue-tab").tabs();

    });


    function closetab(){
        var event_id=$("#event_id").val()
        $.ajax({
            url:"<?php echo U('Eventm/delete');?>",
            type:"POST",
            data:{'event_id':event_id},
            async: true,
            success:function(){
                var str="<?php echo U('Eventm/index');?>";
                window.location.href=str;
            }
        });
    }
</script>

</body>
</html>