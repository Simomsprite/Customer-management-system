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
    <h1 class="abe-header-title">客户转化率</h1>
  </header>
<!--线索详情-->
    <div class="newsx pdt_10 pdl_15 pdr_15">
      <form id="" action="<?php echo U('Leadsm/analytics');?>" class="form-group" method="post" style="margin-bottom: 0px;">
        <input type="hidden" name="m" value="leads" />
        <input type="hidden" name="a" value="analytics" />
    <ul class="nclearfix">
      <li><span class="tit">部门</span>
        <select name="department" id="department" onchange="changeRole()" class="pbsele">
          <option class="all" value="all"><?php echo L('ALL');?></option>
          <?php if(is_array($departmentList)): $i = 0; $__LIST__ = $departmentList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["department_id"]); ?>" <?php if($department == $vo['department_id']): ?>selected<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
        </select>
      </li>
      <li><span class="tit">客户级别</span>
        <select name="role" id="role" class="pbsele">
          <option class="all" value="all"><?php echo L('ALL');?></option>
          <?php if(is_array($roleList)): $i = 0; $__LIST__ = $roleList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["role_id"]); ?>" <?php if($_POST['role'] == $vo['role_id']): ?>selected<?php endif; ?>><?php echo ($vo["role_name"]); ?>-<?php echo ($vo["user_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
        </select>
      </li>
      <li class="w100">
          <span class="tit abe-fl">开始时间</span>
        <label class="timetxt">
          <input type="text" class="txt ptxt100p" placeholder="" value="<?php echo ($begin_time); ?>" name="begin_time" id="begin_time" onClick="WdatePicker({dateFmt:'yyyy-MM-dd'})">
          <i class="iconfont">&#xe605;</i></label>
        <label class="pdl_10 pdr_10">结束时间</label>
        <label class="timetxt">
          <input type="text" class="txt ptxt100p" placeholder="" value="<?php echo ($next_time); ?>" name="next_time" id="next_time" onClick="WdatePicker({dateFmt:'yyyy-MM-dd'})">
          <i class="iconfont">&#xe605;</i></label>
      </li>
        <li class="w100">
         <input type="submit" class="pbtn pbtn-sm" value="提交">
      </li>
    </ul>
      </form>
  </div>
    <div id="echart" style="width:100%; height:60%; margin: 0 auto"></div>
  <script src="__PUBLIC__/waps/js/mobiscroll_date.js" charset="gb2312"></script>
  <script src="__PUBLIC__/waps/js/mobiscroll.js"></script>
  <script type="text/javascript">
		var myChart = echarts.init(document.getElementById("echart"));
        var arr = [<?php echo ($comma_separated); ?>];
        var ass = [<?php echo ($comma_separateds); ?>];
        console.log(arr);
		option = {
			color: ['#3398DB'],

          tooltip : {
				trigger: 'axis',
				axisPointer : {            // 坐标轴指示器，坐标轴触发有效
					type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
				}
			},
			grid: {
				left: '3%',
				right: '12%',
				bottom: '3%',
				containLabel: true
			},
			xAxis : [
				{
					axisLine:{
						symbol:['none', 'arrow'],
						lineStyle:{
							color:'#fff',
						}
					},
					axisTick:{
						length:1,
                        alignWithLabel: true,
						
					},
                    splitLine:{
						show:false
					},
					type:'value',
                    name:'转化率/%'
					
				}
			],
			yAxis : [
				{   type : 'category',
					axisLine:{
						lineStyle:{
							color:'#fff',
						}
					},
					splitLine:{
						show:false
					},
					name :"客户数",
					type : 'category',
                    data: arr
				}
			],
			series : [
				{
					name:'转化率',
					type:'bar',
					barWidth: '20px',
					barMinHeight:20,
					data:ass,
					label:{
						normal:{
							show:true,
							position:'top',
							color:'#fff'
						}
						
					},
					itemStyle:{ 
					   normal:{ barBorderRadius: [0,50,50,0], //（顺时针左上，右上，右下，左下）
					   color: {
								type: 'linear',
								x: 0,
								y: 0,
								x2: 0,
								y2: 1,
								colorStops: [{
									offset: 0, color: '#00ccff' // 0% 处的颜色
								}, {
									offset: 1, color: '#364359' // 100% 处的颜色
								}],
								globalCoord: false // 缺省为 false
							}
					   }
					},
				}
			]
		};
		myChart.setOption(option);


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

          $("#begin_time").mobiscroll($.extend(opt['date'], opt['default']));
          $("#next_time").mobiscroll($.extend(opt['date'], opt['default']));
        });

        function changeRole(){
          department_id = $("#department option:selected").val();
          $.ajax({
            type:'get',
            url:'index.php?m=user&a=getrolebydepartment&department_id='+department_id,
            async:true,
            success:function(data){
              options = '<option value="all"><?php echo L('All');?></option>';
              if(data.data != null){
                $.each(data.data, function(k, v){
                  options += '<option value="'+v.role_id+'">'+v.role_name+"-"+v.user_name+'</option>';
                });
              }
              $("#role").html(options);
            <?php if($_GET['role']): ?>$("#role option[value='<?php echo ($_GET['role']); ?>']").prop("selected", true);<?php endif; ?>
            },
            dataType:'json'});
        }
  </script>
</div>
</body>
</html>