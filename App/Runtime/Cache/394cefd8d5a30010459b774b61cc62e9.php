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
    <h1 class="abe-header-title">客户编辑</h1>
  </header>
    <form action="<?php echo U('customerm/edit');?>" method="post">
        <input type="hidden" name="p" value="<?php echo ($p); ?>">
        <input type="hidden" name="customer_id" id="customer_id" value="<?php echo ($customer['customer_id']); ?>">
      <ul class="cust-form">
    <?php if(is_array($field_list['main'])): $key = 0; $__LIST__ = $field_list['main'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key % 2 );++$key;?><li class="titbox">
        <span class="tit"><?php echo ($vo["name"]); ?>：<?php if(($vo["is_null"] == '1' && $vo["is_validate"] == '1') || $vo["is_unique"] == '1'): ?><em class="abe-red">*</em><?php endif; ?></span>
        <?php if($vo['field'] == 'customer_owner_id'): ?><div class="col-md-6">
            <input type="hidden" name="owner_role_id" id="owner_role_id" value="<?php echo ($_SESSION['role_id']); ?>"/>
            <input class="txt ptxt100p" aria-required="true" id="owner_name" name="owner_name" type="text" value="<?php echo (session('full_name')); ?>" readonly="true" style="cursor:pointer;" title="请点击选择" aria-invalid="false">
          </div>
            <div class="col-md-2"></div>
          <?php elseif($vo['form_type'] == 'textarea'): ?>
          <?php if($vo['tip_start'] == 1): ?><div class="col-md-6">
              <?php echo ($vo["html"]); ?>
            </div>
            <div class="col-md-2"></div>
            <?php else: ?>
            <div class="col-md-8">
              <?php echo ($vo["html"]); ?>
            </div><?php endif; ?>
          <?php elseif($vo['form_type'] == 'address'): ?>
          <?php if($vo['tip_start'] == 1): ?><div class="col-md-7">
              <?php echo ($vo["html"]); ?>
            </div>
            <div class="col-md-1"></div>
            <?php else: ?>
            <div class="col-md-8">
              <?php echo ($vo["html"]); ?>
            </div><?php endif; ?>
          <?php elseif($vo['form_type'] == 'box'): ?>
            <div class="col-md-6">
            <?php if($vo['field'] == 'grade'): ?><input type="hidden" id="star" name="grade" value="<?php echo ($customer['grade']); ?>">
              <div class="all-star" style="font-size: 18px;padding-top:3px;color:#D0D0D0;">
                <i class="fa fa-star">&nbsp;&nbsp;</i><i class="fa fa-star">&nbsp;&nbsp;</i><i class="fa fa-star">&nbsp;&nbsp;</i><i class="fa fa-star">&nbsp;&nbsp;</i><i class="fa fa-star">&nbsp;&nbsp;</i>
              </div>
              <?php else: ?>
              <?php echo ($vo["html"]); endif; ?>
          </div>
          <?php if($vo['tip_start'] == 1): ?><div class="col-md-2"></div>
            <?php else: ?>
            <div class="col-md-2"></div><?php endif; ?>
          <?php else: ?>
          <div class="col-md-6">
            <?php echo ($vo["html"]); ?>
          </div>
          <?php if($vo['tip_start'] == 1): ?><div class="col-md-2"></div>
            <?php else: ?>
            <div class="col-md-2"></div><?php endif; endif; ?>
      </li><?php endforeach; endif; else: echo "" ;endif; ?>

   <!--<li class="titbox"><span class="tit">客户负责人：</span>
      <div class="txtbox">
        <input type="text" class="txt ptxt100p" placeholder="请输入客户负责人">
      </div>
    </li>
    <li class="titbox"><span class="tit">下次联系时间：</span>
      <div class="txtbox">
        <input type="text" class="txt ptxt100p" placeholder="请选择日期" name="USER_AGE" id="USER_AGE">
      </div>
    </li>
	   <li class="titbox"><span class="tit">客户电话：</span>
      
             <div class="txtbox">
        <input type="text" class="txt ptxt100p" placeholder="请输入公司名">
      </div>
    </li>

    <li class="titbox"><span class="tit">客户编号：</span>
      <div class="txtbox">
        <input type="text" class="txt ptxt100p" placeholder="请输入客户编号">
      </div>
    </li>
	 <li class="titbox"><span class="tit">客户状态：</span>
      <div class="txtbox">
          <select name="" id="" class="pbsele w100p">
          <option value="">意向客户</option>
        </select>
      </div>
    </li>  
    <li class="titbox"><span class="tit">客户行业：</span>
      <div class="txtbox">
          <select name="" id="" class="pbsele w100p">
          <option value="">请选择</option>
        </select>
      </div>
    </li>
      	   <li class="titbox"><span class="tit">客户地址：</span>
      
             <div class="txtbox">
        <select name="" id="" class="pbsele w50p abe-fl">
          <option value="">请选择</option>
        </select><select name="" id="" class="pbsele w50p abe-fr">
          <option value="">请选择</option>
        </select>
      </div>
    </li>
	     <li class="titbox">
    <div class="txtbox">
          <input type="text" class="txt ptxt100p" placeholder="请输入详细地址">
      </div>
    </li>
      <li class="titbox"><span class="tit">客户信息来源：</span>
      <div class="txtbox">
        <select name="" id="" class="pbsele w100p">
          <option value="">请选择</option>
        </select>
      </div>
    </li>
      <li class="titbox"><span class="tit">客户等级：</span>
      <div class="txtbox">
        <select name="" id="" class="pbsele w100p">
          <option value="">等级1</option>
        </select>
      </div>
    </li>-->
  </ul>
  <div class="sub-box">
    <input type="button" value="取消" onclick="goIndex()" name="button" class="pbtn pbtn-sm pbtnw30p mrg_15"/>
      <input type="submit" value="确定"  class="pbtn  pbtnw30p pbtn-sm" onclick="return myValidate()">
  </div>


            <!--<li class="titbox"><span class="tit">联系人姓名：</span>
                <div class="txtbox">
                    <input type="text" class="txt ptxt100p" placeholder="请输入客户负责人">
                </div>
            </li>
            <li class="titbox"><span class="tit">角色：</span>
                <div class="txtbox">
                    <select name="" id="" class="pbsele w100p">
                        <option value="">请选择</option>
                    </select>
                </div>
            </li>

            <li class="titbox"><span class="tit">尊称：</span>
                <div class="txtbox">
                    <select name="" id="" class="pbsele w100p">
                        <option value="">请选择</option>
                    </select>
                </div>
            </li>

            <li class="titbox"><span class="tit">职位：</span>
                <div class="txtbox">
                    <input type="text" class="txt ptxt100p" placeholder="请输入职位">
                </div>
            </li>
            <li class="titbox"><span class="tit">手机：</span>
                <div class="txtbox">
                    <input type="text" class="txt ptxt100p" placeholder="请输入手机">
                </div>
            </li>
            <li class="titbox"><span class="tit">QQ：</span>
                <div class="txtbox">
                    <input type="text" class="txt ptxt100p" placeholder="请输入QQ">
                </div>
            </li>
            <li class="titbox"><span class="tit">邮箱：</span>
                <div class="txtbox">
                    <input type="text" class="txt ptxt100p" placeholder="请输入邮箱">
                </div>
            </li>-->



    </form>
</div>
<link type="text/css" rel="stylesheet" href="__PUBLIC__/waps/style/style.css">
    <script src="__PUBLIC__/waps/js/mobiscroll_date.js" charset="gb2312"></script>
<script src="__PUBLIC__/waps/js/mobiscroll.js"></script>
<script type="text/javascript">
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

	$("#USER_AGE").mobiscroll($.extend(opt['datetime'], opt['default']));

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

var star = $('#star').val();
if(star == '' || star == null){
    $('.fa-star').removeClass("star-orange");
}else{
    $('.fa-star').removeClass("star-orange");
    $('.fa-star:lt('+star+')').addClass("star-orange");
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

function goIndex(){
    showtab(3,'是否退出编辑客户')
}

function　closetab(){
    window.location.href="javascript:history.go(-1);";
}

function myValidate(){
    var name=$("#name").val();
    if (name==""){
        showtab1(2,"客户名称不能为空");
        return false;
    }
    var nameReg=/^[\u4e00-\u9fa5]{1,7}$|^[\dA-Za-z_]{1,14}$/;
    if (!nameReg.test(name)){
        showtab1(2,"客户名称格式不正确");
        return false;
    }

    var customer_code=$("#customer_code").val();
    if (customer_code==""){
        showtab1(2,"客户编号不能为空");
        return false;
    }
    var codeReg=/^[0-9a-zA-Z_]{1,14}$/;
    if (!codeReg.test(customer_code)){
        showtab1(2,"客户编号格式不正确");
        return false;
    }

    var customerMobile=$("#crm_vwlnfx").val();
    let customerMobileReg = /^1(3|4|5|6|7|8|9)\d{9}$/;
    if (customerMobile==""){
        return true;
    }else if (!customerMobileReg.test(customerMobile)) {
        showtab1(2,"客户电话格式不正确");
        return false;
    }
}
</script>

</body>
</html>