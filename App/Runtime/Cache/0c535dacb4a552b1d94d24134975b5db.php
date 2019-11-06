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
        <h1 class="abe-header-title">编辑日程</h1>
    </header>
    <form action="<?php echo U('eventm/edit');?>" method="post">
        <input type="hidden" name="event_id" value="<?php echo ($event_info["event_id"]); ?>">
        <ul class="cust-form">
            <li class="titbox">
                <span class="tit">日程内容：</span>
                    <div class="txtbox">
                        <input type="text" class="txt ptxt100p" name="subject"  value="<?php echo ($event_info["subject"]); ?>"/>
                    </div>
            </li>
            <li class="titbox">
                <span class="tit">开始时间：</span>
                <div class="txtbox">
                    <input type="text" class="txt ptxt100p" placeholder="请选择开始时间" name="start_date" id="USER_AGE" value="<?php echo (date('Y-m-d H:i',$event_info["start_date"])); ?>">
                </div>
            </li>
            <li class="titbox">
                <span class="tit">结束时间：</span>
                <div class="txtbox">
                    <input type="text" class="txt ptxt100p" placeholder="请选择结束时间" name="end_date" id="USER_AGE1" value="<?php echo (date('Y-m-d H:i',$event_info["end_date"])); ?>">
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
                    <textarea type="textarea" class="txt ptxt100p" rows="5" name="description"><?php echo ($event_info["description"]); ?></textarea>
                </div>
            </li>


            <div class="sub-box">
                <input type="button" name="button" value="取消" onclick="goIndex()" class="pbtn  pbtnw30p pbtn-sm mrg_15" >
                <input type="submit" style="cursor:pointer" value="提交日程" onclick="return checkdate()"  class="pbtn pbtn-sm pbtnw30p ">
            </div>
        </ul>
    </form>
</div>
</div>
<script src="__PUBLIC__/waps/js/mobiscroll_date.js" charset="gb2312"></script>
<script src="__PUBLIC__/waps/js/mobiscroll.js"></script>
<script type="text/javascript">
    function checkdate(){
        var start_date=$("#USER_AGE").val();
        var end_date=$("#USER_AGE1").val();
        var start=new Date(start_date.replace(/-/g, "/"));
        var end=new Date(end_date.replace(/-/g,"/"));
        if (start.getTime()>end.getTime()){
            showtab1(2,'开始时间不能大于结束时间');
            return false;
        }else{
            return true;
        }
    }

    $(function () {
        var currYear = (new Date()).getFullYear();
        var opt={};
        opt.date = {preset : 'date'};
        opt.datetime = {preset : 'datetime'};
        console.log( opt.datetime);
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

        $("#USER_AGE").mobiscroll($.extend(opt['datetime'], opt['default']));

    });

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

        $("#USER_AGE1").mobiscroll($.extend(opt['datetime'], opt['default']));

    });



    function checkinfo(param){
        var field_value = $('#'+param).val();
        if(field_value){
            $.post('<?php echo U("customer/checkinfo");?>',
                {
                    field_value:field_value,
                    field_name:param,
                },
                function(data){
                    if(data.status == 1){
                        $('#'+param+'-error').remove('');
                        $('#'+param).after('<label id="'+param+'-error" class="error" for="source"><i class="fa fa-times-circle"></i></label>');
                        $('#'+param+'-error').html('<i class="fa fa-times-circle"></i>'+data.data);
                    }else{
                        $('#'+param+'-error').remove('');
                    }
                },
                'json');
        }
    }

    function getnext(i){
        let gn=new Array("pageone","pagetwo");
        for (let j=0;j<gn.length;j++){
            if (i==gn[j]){
                document.getElementById(i).style.display="none";
            } else{
                document.getElementById(gn[j]).style.display="block";
            }
        }

    }

    /*星星特效*/
    $('.fa-star').mousemove(function(){
        $(this).addClass("star-orange");
        $(this).prevAll().addClass("star-orange");
        $(this).nextAll().removeClass("star-orange");
    });
    $('.fa-star').mouseout(function(){
        var star = $('#star').val();
        if(star == '' || star == null){
            $('.fa-star').removeClass("star-orange");
        }else{
            $('.fa-star').removeClass("star-orange");
            $('.fa-star:lt('+star+')').addClass("star-orange");
        }
    });
    $('.fa-star').click(function(){
        var num = $(this).index() + 1;
        $('#star').val(num);
    });
    /*地区联动*/
    //new PCAS("province","city","area")//三级联动，有默认值，有文字提示信息


    $('#name').blur(function(){
        name = $('#name').val();
        var is_check = $(this).attr('is_check');
        if(name != '' && is_check == 1){
            $.post('<?php echo U("customer/check");?>',
                {
                    name:name
                },
                function(data){
                    if(data.data != 0){
                        $result = '';
                        $.each(data.data, function(k, v){
                            $result += (k+1)+'、'+v+'</br>';
                        });
                        $('#dialog-validate').dialog('open');
                        $("#search_content").html($result);
                        // alert_crm($result,"","warning");
                    }
                },
                'json');
        }
    });

    /*$(":button").blur(function{
       this.css('background-image','blue');
    });*/

    $("#owner_name").click(function(){
        $('#dialog-role-list2').dialog('open');
        $('#dialog-role-list2').load('<?php echo U("user/listDialog");?>');
    });

    $("#dialog-role-info").dialog({
        autoOpen: false,
        modal: true,
        width: 800,
        maxHeight: 400,
        position: ["center",100]
    });

    $("#dialog-contacts-list").dialog({
        autoOpen: false,
        modal: false,
        width: 800,
        height: 400,
        close: function () {
            $(this).html("");
        },
        buttons: {
            "<?php echo L('OK');?>": function () {
                var item = $('input:radio[name="contacts"]:checked').val();
                var name = $('input:radio[name="contacts"]:checked').parent().next().html();
                $('#module_id').val(item);
                $('#module_name').val(name);
                $(this).dialog("close");
            },
            "<?php echo L('CANCEL');?>": function () {
                $(this).dialog("close");
            }
        },
        position:["center",100]
    });
    $("#dialog-leads").dialog({
        autoOpen: false,
        modal: false,
        width: 800,
        height: 400,
        close: function () {
            $(this).html("");
        },
        buttons: {
            "<?php echo L('OK');?>": function () {
                var item = $('input:radio[name="leads"]:checked').val();
                var name = $('input:radio[name="leads"]:checked').parent().next().html();
                var company = $('input:radio[name="leads"]:checked').parent().next().next().html();
                $('#module_id').val(item);
                $('#module_name').val(name+" "+company);
                $(this).dialog("close");
            },
            "<?php echo L('CANCEL');?>": function () {
                $(this).dialog("close");
            }
        },
        position:["center",100]
    });

    $("#dialog-customer-list").dialog({
        autoOpen: false,
        modal: false,
        width: 800,
        height: 400,
        close: function () {
            $(this).html("");
        },
        buttons: {
            "<?php echo L('OK');?>": function () {
                var item = $('input:radio[name="customer"]:checked').val();
                var name = $('input:radio[name="customer"]:checked').parent().next().html();
                $('#module_id').val(item);
                $('#module_name').val(name);
                $(this).dialog("close");
            },
            "<?php echo L('CANCEL');?>": function () {
                $(this).dialog("close");
            }
        },
        position:["center",100]
    });

    $("#dialog-business").dialog({
        autoOpen: false,
        modal: true,
        width: 800,
        height: 400,
        close: function () {
            $(this).html("");
        },
        buttons: {
            "<?php echo L('OK');?>": function () {
                var item = $('input:radio[name="business"]:checked').val();
                var name = $('input:radio[name="business"]:checked').parent().next().html();
                if(item){
                    $('#module_id').val(item);
                    $('#module_name').val(name);
                }
                $(this).dialog("close");
            },
            "<?php echo L('CANCEL');?>": function () {
                $(this).dialog("close");
            }
        },
        position:["center",100]
    });

    $("#dialog-product").dialog({
        autoOpen: false,
        modal: true,
        width: 800,
        height: 400,
        close: function () {
            $(this).html("");
        },
        buttons: {
            "<?php echo L('OK');?>": function () {
                var item = $('input:radio[name="product_id"]:checked').val();
                var name = $('input:radio[name="product_id"]:checked').parent().next().html();
                $('#module_id').val(item);
                $('#module_name').val(name);
                $(this).dialog("close");
            },
            "<?php echo L('CANCEL');?>": function () {
                $(this).dialog("close");
            }
        },
        position:["center",100]
    });

    function changeContent(){
        $('#module_id').val("");
        $('#module_name').val("");
    }


    $('#module_name').click(function(){
        a = $("#select1 option:selected").val();
        if (a == "contacts"){
            $('#dialog-contacts-list').dialog('open');
            $('#dialog-contacts-list').load('<?php echo U("contacts/radioListDialog");?>');
        }else if(a == "leads"){
            $('#dialog-leads').dialog('open');
            $('#dialog-leads').load('<?php echo U("leads/listDialog");?>');
        }else if(a == "business"){
            $('#dialog-business').dialog('open');
            $('#dialog-business').load('<?php echo U("business/listDialog");?>');
        }else if(a == "customer"){
            $('#dialog-customer-list').dialog('open');
            $('#dialog-customer-list').load('<?php echo U("customer/listDialog");?>');
        }else if(a == "product"){
            $('#dialog-product').dialog('open');
            $('#dialog-product').load('<?php echo U("product/allProductDialog");?>');
        }
    });

    function goIndex(){
        showtab(3,'是否退出编辑日程')
    }

    function　closetab(){
        window.location.href="javascript:history.go(-1);";
    }
</script>

</body>
</html>