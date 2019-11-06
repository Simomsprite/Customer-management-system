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
    <h1 class="abe-header-title">线索添加</h1>
  </header>
  <form  action="<?php echo U('leadsm/add');?>" method="post" id="signForm">
    <ul class="cust-form">
      <?php if(is_array($field_list['main'])): $key = 0; $__LIST__ = $field_list['main'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($key % 2 );++$key;?><li class="titbox">
          <span class="tit"><?php echo ($vo["name"]); ?>：<?php if($vo["is_null"] == '1' && $vo["is_validate"] == '1'): ?><em class="abe-red">*</em><?php endif; ?></span>
          <?php if($vo['form_type'] == 'textarea'): if($vo['tip_start'] == 1): ?><div class="col-md-6">
                <?php echo ($vo["html"]); ?>
              </div>
              <div class="col-md-2"><span style="color: red;line-height: 32px;margin-left: 10px;">*</span></div>
              <?php else: ?>
              <div class="col-md-8">
                <?php echo ($vo["html"]); ?>
              </div><?php endif; ?>
            <?php elseif($vo['form_type'] == 'address'): ?>
            <?php if($vo['tip_start'] == 1): ?><div class="col-md-7">
                <?php echo ($vo["html"]); ?>
              </div>
              <div class="col-md-1"><span style="color: red;line-height: 32px;margin-left: 10px;">*</span></div>
              <?php else: ?>
              <div class="col-md-8">
                <?php echo ($vo["html"]); ?>
              </div><?php endif; ?>
            <?php elseif($vo['form_type'] == 'box'): ?>
            <div class="col-md-6">
              <?php echo ($vo["html"]); ?>
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
      <!--<li class="titbox"><span class="tit">来源：</span>
          <div class="txtbox">
              <select  name="box"  class="pbsele w100p">
                  <option value="">订单</option>
              </select>
          </div>
      </li>
      <li class="titbox"><span class="tit">联系人：</span>
          <div class="txtbox">
              <input type="text" class="txt ptxt100p" placeholder="请输入联系人姓名">
          </div>
      </li>
      <li class="titbox"><span class="tit">公司名：</span>

          <div class="txtbox">
              <input type="text" class="txt ptxt100p" placeholder="请输入公司名">
          </div>
      </li>

      <li class="titbox"><span class="tit">尊称：</span>
          <div class="txtbox">
              <select name="saltname" id="" class="pbsele w100p">
                  <option value="">请选择</option>
              </select>
          </div>
      </li>
      <li class="titbox"><span class="tit">手机：</span>
          <div class="txtbox">
              <input type="text" class="txt ptxt100p" placeholder="请输入手机号码">
          </div>
      </li>
      <li class="titbox"><span class="tit">邮箱：</span>
          <div class="txtbox">
              <input type="text" class="txt ptxt100p" placeholder="请输入邮箱">
          </div>
      </li>
      <li class="titbox"><span class="tit">地址：</span>

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
      </li>-->
    </ul>
    <div class="sub-box">
      <input type="submit" value="确定" id="button"  class="pbtn pbtn-sm pbtnw80p" onclick="return myValidate()">
    </div>
  </form>
</div>
</body>
<script src="__PUBLIC__/waps/js/mobiscroll_date.js" charset="gb2312"></script>
<script src="__PUBLIC__/waps/js/mobiscroll.js"></script>
</html>
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


$("form").submit(function(){
    $(":submit",this).attr("disabled","disabled");
});


function goIndex(){
    showtab(3,'是否退出新建线索')
}

function　closetab(){
    window.location.href=document.referrer;
}

function myValidate(){
    var contacts_name=$("#contacts_name").val();
    if (contacts_name=="") {
        showtab1(2,"联系人姓名不能为空");
        return false;
    }

    var mobile=$("#mobile").val();
    if (mobile==""){
        showtab1(2,"手机内容不能为空");
        return false;
    }

    let phoneReg = /^1(3|4|5|6|7|8|9)\d{9}$/;
    if (!phoneReg.test(mobile)) {
        showtab1(2,"手机格式不正确");
        return false;
    }


    var email=$("#email").val();
    var emailReg=/|^(\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*)?$/;
    if (!emailReg.test(email)){
        showtab1(2,"email格式不正确");
        return false;
    }

}


</script>