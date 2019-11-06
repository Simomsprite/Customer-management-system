function changeCondition(){
	var a = $("#field option:selected").attr('class');
	var b = $("#field option:selected").val();
	var c = $("#field option:selected").attr('rel');
	var search = $('#search').val();
	if(a == 'role'){
		role_id = $('#search').val();
	}
	if(a == 'number') {
		$("#conditionContent").html('<select class="form-control input-sm" id="condition" style="width:auto" name="condition" onchange="changeSearch()">'
							+'<option value="gt">  '+CrmLang.GT+'  </option>'
							+'<option value="lt">  '+CrmLang.LT+'  </option>'
							+'<option value="eq">  '+CrmLang.EQ+'  </option>'
							+'<option value="neq">  '+CrmLang.NEQ+'  </option>'
							+'</select>&nbsp;&nbsp; ');
		$("#searchContent").html('<input id="search" type="text" class="form-control input-sm search-query" name="search" value="'+search+'"/>&nbsp;&nbsp;');
	} else if ((a == 'word') || (a == 'text') || (a == 'textarea') || (a == 'editor') || (a == 'mobile') || (a == 'email')) {
		$("#conditionContent").html('<select class="form-control input-sm" id="condition" style="width:auto" name="condition" onchange="changeSearch()">'
							+'<option value="contains">'+CrmLang.CONTAINS+'</option>'
							+'<option value="not_contain">'+CrmLang.NOT_CONTAIN+'</option>'
							+'<option value="is">'+CrmLang.IS+'</option>'
							+'<option value="isnot">'+CrmLang.ISNOT+'</option>'							
							+'<option value="start_with">'+CrmLang.START_WITH+'</option>'
							+'<option value="end_with">'+CrmLang.END_WITH+'</option>'
							+'<option value="is_empty">'+CrmLang.IS_EMPTY+'</option>'
							+'<option value="is_not_empty">'+CrmLang.IS_NOT_EMPTY+'</option></select>&nbsp;&nbsp;');
		$("#searchContent").html('<input id="search" type="text" class="form-control input-sm search-query" name="search" value="'+search+'"/>&nbsp;&nbsp;');
	} else if (a == 'date' || a== 'datetime') {
		$("#conditionContent").html('<select class="form-control input-sm" id="condition" style="width:auto" name="condition" onchange="changeSearch()">'
							+'<option value="tgt">  '+CrmLang.BEHIND+'  </option>'
							+'<option value="lt">  '+CrmLang.BEFORE+'  </option>'
							+'<option value="between">  '+CrmLang.EXIST+'  </option>'
							+'<option value="nbetween">  '+CrmLang.ABSENT+'  </option>'
							+'</select>&nbsp;&nbsp;');
		$("#searchContent").html('<input id="search" type="text" class="form-control input-sm search-query" name="search" onclick="WdatePicker()" value="'+search+'"/>&nbsp;&nbsp;');
	} else if (a == 'bool') {
		$("#conditionContent").html('<select class="form-control input-sm" id="condition" style="width:auto" name="condition" onchange="changeSearch()">'
							+'<option value="1">'+CrmLang.IS+'</option>'
							+'<option value="0">'+CrmLang.ISNOT+'</option>'
							+'</select>&nbsp;&nbsp;');
		$("#searchContent").html('<input id="search" type="text" class="form-control input-sm search-query" name="search"/>&nbsp;&nbsp;');
	} else if (a == 'sex') {
		$("#searchContent").html('<select class="form-control input-sm" id="search" style="width:auto" name="search">'
							+'<option value="1">'+CrmLang.MAN+'</option>'
							+'<option value="0">'+CrmLang.WOMAN+'</option>'
							+'</select>&nbsp;&nbsp;');
		$("#conditionContent").html('');
	} else if (a == 'role') {
		var module = getUrlParam("m");
		var action = getUrlParam("a");
		$.ajax({
			type:'get',
			url:'index.php?m=user&a=getrolelist&module='+module+'&action='+action,
			async:false,
			success:function(data){
				options = '';
				$.each(data.data, function(k, v){
					var select ='';
					if(v.role_id == role_id){
						select = 'selected';
					}
					options += '<option value="'+v.role_id+'" '+select+'>'+v.user_name+' ['+v.department_name+'-'+v.role_name+'] </option>';
				});
				//$("#searchContent").html('<select id="search" class="form-control input-sm" style="width:auto;max-width: 200px;" name="search">' + options + '</select>&nbsp;&nbsp;');
				$("#searchContent").html('<select class="selectpicker show-tick form-control input-sm" data-live-search="true" id="search" name="search" style="width:auto">' + options + '</select>&nbsp;&nbsp;');
				$('#search').selectpicker('render');
                $('#search').selectpicker('refresh');
				
				$("#conditionContent").html('');
			},
			dataType:'json'
		});		
	} else if (a == 'business_status') {
		$.ajax({
			type:'get',
			url:'index.php?m=setting&a=getbusinessstatuslist',
			async:false,
			success:function(data){
				options = '';
				$.each(data.data, function(k, v){
					options += '<option value="'+v.status_id+'">'+v.name+'</option>';
				});

				$("#searchContent").html('<select class="form-control input-sm" id="search" style="width:auto" name="search">' + options + '</select>&nbsp;&nbsp;');
				$("#conditionContent").html('');
			},
			dataType:'json'
		});		
	}else if (a == 'customer') {
		$("#conditionContent").html('');
		$("#searchContent").html('<input id="search" type="text" class="form-control input-sm search-query" name="search"/>&nbsp;&nbsp;');
	}else if (a == 'stock') {
		$.ajax({
			type:'get',
			url:'index.php?m=stock&a=getwarehouselist',
			async:false,
			success:function(data){
				options = '';
				$.each(data.data, function(k, v){
					options += '<option value="'+v.warehouse_id+'">'+v.name+'</option>';
				});
				$("#searchContent").html('<select class="form-control input-sm" id="search" style="width:auto" name="search">' + options + '</select>&nbsp;&nbsp;');
				$("#conditionContent").html('');
			},
			dataType:'json'
		});		
	}else if (a == 'contract') {
		$.ajax({
			type:'get',
			url:'index.php?m=contract&a=getcontractlist',
			async:false,
			success:function(data){
				options = '';
				$.each(data.data, function(k, v){
					options += '<option value="'+v.contract_id+'">'+v.number+'--'+v.customer_name+'</option>';
				});
				$("#searchContent").html('<select class="selectpicker show-tick form-control input-sm" id="search" style="width:auto" data-live-search="true" name="search">' + options + '</select>&nbsp;&nbsp;');
				$('#search').selectpicker('render');
                $('#search').selectpicker('refresh');
				$("#conditionContent").html('');
			},
			dataType:'json'
		});		
	}else if (a == 'contract_check') {
		$("#searchContent").html('<select id="search" style="width:auto" name="search">'
							+'<option value="1">通过</option>'
							+'<option value="0">待审</option>'
							+'<option value="2">拒绝</option>'
							+'</select>&nbsp;&nbsp;');
		$("#conditionContent").html('');
	}else if (a == 'sales_status') {
		var options = '';
		if(c == 'index'){
			options += '<option value="97">未出库</option>';
			options += '<option value="98">已出库</option>';
		}else if(c == 'salesreturn'){
			options += '<option value="99">未入库</option>';
			options += '<option value="100">已入库</option>';
		}else{
			options += '<option value="97">未出库</option>';
			options += '<option value="98">已出库</option>';
			options += '<option value="99">未入库</option>';
			options += '<option value="100">已入库</option>';
		}
		$("#searchContent").html('<select class="form-control input-sm" id="search" style="width:auto" name="search">' + options + '</select>&nbsp;&nbsp;');
		$("#conditionContent").html('');
	} else if(a=='all') {
		$("#conditionContent").html('<select class="form-control input-sm" id="condition" style="width:auto" name="condition" onchange="changeSearch()">'
							+'<option value="contains">'+CrmLang.CONTAINS+'</option>'
							+'<option value="is">'+CrmLang.IS+'</option>'
							+'<option value="start_with">'+CrmLang.START_WITH+'</option>'
							+'<option value="end_with">'+CrmLang.END_WITH+'</option>'
							+'<option value="is_empty">'+CrmLang.IS_EMPTY+'</option>'
							+'</select>&nbsp;&nbsp;');
		$("#searchContent").html('<input id="search" type="text" class="form-control input-sm search-query" name="search"/>&nbsp;&nbsp;');
	} else if (a == 'task_status') {
		$("#conditionContent").html('<select class="form-control input-sm" id="search" style="width:auto" name="search">'
							+'<option value='+CrmLang.NOT_STARTED+'>'+CrmLang.NOT_STARTED+'</option>'
							+'<option value='+CrmLang.RETARDATION+'>'+CrmLang.RETARDATION+'</option>'
							+'<option value='+CrmLang.UNDERWAY+'>'+CrmLang.UNDERWAY+'</option>'
							+'<option value='+CrmLang.COMPLETED+'>'+CrmLang.COMPLETED+'</option>'
							+'</select>&nbsp;&nbsp;');
		$("#searchContent").html('');
	} else if (a == 'task_priority') {
		$("#conditionContent").html('<select class="form-control input-sm" id="search" style="width:auto" name="search">'
							+'<option value='+CrmLang.HIGH+'>'+CrmLang.HIGH+'</option>'
							+'<option value='+CrmLang.GENERAL+'>'+CrmLang.GENERAL+'</option>'
							+'<option value='+CrmLang.LOW+'>'+CrmLang.LOW+'</option>'
							+'</select>&nbsp;&nbsp;');
		$("#searchContent").html('');
	}else if (a == 'payables_status') {
		$("#conditionContent").html('<select class="form-control input-sm" id="search" style="width:auto" name="search">'
							+'<option value="0">'+CrmLang.NOT_PAYING+'</option>'
							+'<option value="1">'+CrmLang.PART_OF_THE_PREPAID+'</option>'
							+'<option value="2">'+CrmLang.ACCOUNT_PAID+'</option>'
							+'</select>&nbsp;&nbsp;');
		$("#searchContent").html('');
	}else if (a == 'order_status') {
		$("#conditionContent").html('<select class="form-control input-sm" id="search" style="width:auto" name="search">'
							+'<option value="0">'+CrmLang.NOT_CHECK+'</option>'
							+'<option value="1">'+CrmLang.HAS_THE_INVOICING+'</option>'
							+'</select>&nbsp;&nbsp;');
		$("#searchContent").html('');
	} else if (a == 'receivables_status') {
		$("#conditionContent").html('<select class="form-control input-sm" id="search" style="width:auto" name="search">'
							+'<option value="0">'+CrmLang.NOT_RECEIVE_PAYMENT+'</option>'
							+'<option value="1">'+CrmLang.PART_OF_THE_RECEIVED+'</option>'
							+'<option value="2">'+CrmLang.HAS_BEEN_RECEIVING+'</option>'
							+'</select>&nbsp;&nbsp;');
		$("#searchContent").html('');
	} else if (a == 'customer_ownership') {	
		$("#conditionContent").html('<select class="form-control input-sm" id="search" style="width:auto" name="search">'
							+'<option value='+CrmLang.HIGH+'>'+CrmLang.HIGH+'</option>'
							+'<option value='+CrmLang.NO+'>'+CrmLang.NO+'</option>'
							+'<option value='+CrmLang.STATE_OWNED_ENTERPRISES+'>'+CrmLang.STATE_OWNED_ENTERPRISES+'</option>'
							+'<option value='+CrmLang.FOREIGN_CAPITAL_ENTERPRISE+'>'+CrmLang.FOREIGN_CAPITAL_ENTERPRISE+'</option>'
							+'<option value='+CrmLang.PRIVATE_ENTERPRISE+'>'+CrmLang.PRIVATE_ENTERPRISE+'</option>'
							+'<option value='+CrmLang.COLLECTIVE_ENTERPRISE+'>'+CrmLang.COLLECTIVE_ENTERPRISE+'</option>'
							+'<option value='+CrmLang.JOINT_STOCK_COMPANY+'>'+CrmLang.JOINT_STOCK_COMPANY+'</option>'
							+'<option value='+CrmLang.JOINT_VENTURE+'>'+CrmLang.JOINT_VENTURE+'</option>'
							+'<option value='+CrmLang.SOLE_PROPRIETORSHIP_ENTERPRISE+'>'+CrmLang.SOLE_PROPRIETORSHIP_ENTERPRISE+'</option>'
							+'<option value='+CrmLang.OTHER+'>'+CrmLang.OTHER+'</option>'
							+'</select>&nbsp;&nbsp;');
		$("#searchContent").html('');
	} else if (a == 'customer_type') {	
		$("#conditionContent").html('<select class="form-control input-sm" id="search" style="width:auto" name="search">'
							+'<option value='+CrmLang.ANALYSTS+'>'+CrmLang.ANALYSTS+'</option>'
							+'<option value='+CrmLang.COMPETITOR+'>'+CrmLang.COMPETITOR+'</option>'
							+'<option value='+CrmLang.CUSTOMER+'>'+CrmLang.CUSTOMER+'</option>'
							+'<option value='+CrmLang.INTEGRATORS+'>'+CrmLang.INTEGRATORS+'</option>'
							+'<option value='+CrmLang.INVESTORS+'>'+CrmLang.INVESTORS+'</option>'
							+'<option value='+CrmLang.PARTNERS+'>'+CrmLang.PARTNERS+'</option>'
							+'<option value='+CrmLang.PUBLISHERS+'>'+CrmLang.PUBLISHERS+'</option>'
							+'<option value='+CrmLang.TARGET+'>'+CrmLang.TARGET+'</option>'
							+'<option value='+CrmLang.SUPPLIER+'>'+CrmLang.SUPPLIER+'</option>'
							+'<option value='+CrmLang.OTHER+'>'+CrmLang.OTHER+'</option>'
							+'</select>&nbsp;&nbsp;');
		$("#searchContent").html('');
	}else if (a == 'box') {
		$.ajax({
			type:'get',
			url:'index.php?m=setting&a=boxfield&model='+c+'&field='+b,
			async:false,
			success:function(data){
				options = '';
				$.each(data.data, function(k, v){
					options += '<option value="'+v+'">'+v+'</option>';
				});
				$("#searchContent").html('<select class="form-control input-sm" id="search" style="width:auto" name="search">' + options + '</select>&nbsp;&nbsp;');
                if(data.info == 'checkbox'){
                    $("#conditionContent").html('<input type="hidden" name="condition" value="contains">');
                }else{
                    $("#conditionContent").html('');
                }
			},
			dataType:'json'
		});		
	} else if (a == 'address') {
        $("#conditionContent").html('<select class="form-control input-sm" id="condition" style="width:auto" name="condition">'
							+'<option value="contains">'+CrmLang.EXIST+'</option>'
							+'<option value="not_contain">'+CrmLang.ABSENT+'</option></select>&nbsp;&nbsp;');
        $("#searchContent").html('<select class="form-control input-sm" name="state" id="state" style="width:80px;margin-bottom:10px;"></select>&nbsp;'
							+'<select class="form-control input-sm" name="city" id="city" style="width:80px;margin-bottom:10px;"></select>&nbsp;'
							+'<select class="form-control input-sm" name="area" id="area" style="width:80px;margin-bottom:10px;"></select>&nbsp;'
							+'<input type="text" id="search" class="form-control input-sm" style="width:100px;margin-bottom:10px;" name="search" placeholder='+CrmLang.STREET_INFORMATION+' class="input-large">&nbsp;&nbsp;');
        new PCAS("state","city","area","","","");
	} else if (a == 'is_examine') {
		var is_search = $('#is_search').val();
		var options = '<option value="">全部</option>';
		var a = new Array('待审', '通过', '拒绝');
		for(var i=0;i<3;i++){
			if(is_search == ''){
				options += '<option value="'+i+'">'+a[i]+'</option>';
			}else if(is_search == i){
				options += '<option value="'+i+'" selected >'+a[i]+'</option>';
			}else{
				options += '<option value="'+i+'">'+a[i]+'</option>';
			}
		}
        $("#searchContent").html('<select class="form-control input-sm" name="search" id="search" style="width:auto">'+options+'</select>&nbsp;&nbsp;');
        $("#conditionContent").html('');
	} else if (a == 'is_read') {
		var options = '';
		options += '<option value="2">未读</option>';
		options += '<option value="1">已读</option>';
		$("#searchContent").html('<select class="form-control input-sm" id="search" style="width:auto" name="search">' + options + '</select>&nbsp;&nbsp;');
		$("#conditionContent").html('');
	}
}
function checkSearchForm() {
    search = $("#searchForm #search").val();
    field = $("#searchForm #field").val();
    if($("#searchForm #state").length>0){
        if($("#searchForm #state").val() == '' && search == ''){
            alert(CrmLang.SELECT_REGION);return false;
        }else{
        	return true;
        }
    }else{
        if (search == "") {
			if(field == 'is_examine'){
				return true; 
			}else{
				return true;
			}
        }else if(field == ""){
			 alert(CrmLang.SELECT_FILTER_CONDITION);return false;
		}
    }
    return true;
}
$(function(){
	$('form').find('input[type="submit"]').removeAttr("disabled");
	$(document).on('click', 'input[type="submit"]', function(){
		if($(this).parent().find('.form_submit').length > 0){
			$(this).parent().find('.form_submit').val($(this).attr('value'));
		}else{
			$(this).after('<input class="form_submit" type="hidden" name="'+$(this).attr('name')+'" value="'+$(this).attr('value')+'">');
		}
		return true;
	});
	$(document).on('submit', 'form', function(){
		$(this).find('input[type="submit"]').attr("disabled",true);
		return true;
	});
});

function changeSearch() {
	a = $("#field option:selected").attr('class');
	b = $("#condition option:selected").val();
	if(b == 'is_empty' || b == 'is_not_empty') {
		$("#searchContent").html('');
	} else {
		if(a == "date") {
			$("#searchContent").html('<input id="search" type="text" class="form-control input-sm search-query" name="search" onclick="WdatePicker()"/>&nbsp;&nbsp;');	
		}  else if (a == "number" || a == "word" || a == "date") {
			$("#searchContent").html('<input id="search" type="text" class="form-control input-sm search-query" name="search"/>&nbsp;&nbsp;');
		}
	}
}
$(function(){
	if($('.table_thead_fixed thead').length>0){
		var b=30;
		var c=$(".table_thead_fixed").offset();
		var a=$(window).scrollTop();
		var default_w_width = $(window).width();
		var default_width = new Array();
		$.each($(".table_thead_fixed tbody tr:first td"),function(key,val){
			$('.table_thead_fixed thead tr:first th:eq('+key+')').width($(val).width());
			$(val).width($(val).width());
			default_width[key] = $(val).width();
		});
		if(a>c.top-b){
			$(".table_thead_fixed thead").addClass("fixed");
		}else{
			$(".table_thead_fixed thead").removeClass("fixed");
		};
		$(window).scroll(
			function(){
				var a=$(window).scrollTop();
				$.each($(".table_thead_fixed tbody tr:first td"),function(key,val){
					$('.table_thead_fixed thead tr:first th:eq('+key+')').width($(val).width());
					$(val).width($(val).width());
				});
				if(a>c.top-b){
					$(".table_thead_fixed thead").addClass("fixed");
				}else{
					$(".table_thead_fixed thead").removeClass("fixed");
				}
			}
		);
		$(window).resize(
			function(){
				$.each($(".table_thead_fixed tbody tr:first td"),function(key,val){
					if(default_w_width == $(window).width()){
						$(val).css({width:default_width[key]});
						$('.table_thead_fixed thead tr:first th:eq('+key+')').width(default_width[key]);
					}else{
						$(val).css({width:''});
						$('.table_thead_fixed thead tr:first th:eq('+key+')').width($(val).width());
					}
				});
			}
		)
	}
	
	/*删除提示*/
	$('.del_confirm').click(function(){
		if(confirm(CrmLang.CONFIRM_DELETE)){
			return true;
		}else{
			return false;
		}
	});
});

/*alert 显示优化*/
function alert_crm(msg){
	swal({
		title: "温馨提示",
		text: msg,
		type: "warning",
		// showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: "确认",
		// cancelButtonText: "取消",
		closeOnConfirm: true});
	return false;
}

function confirm_crm(url,msg){
	msg = msg || '确认删除?';
	swal({
		title: msg,
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: "确认",
		cancelButtonText: "取消",
		closeOnConfirm: false,
		closeOnCancel:  true
	}, function(isConfirm){
		if (isConfirm) {
			window.location.href = url;
		} else {
			return false;
		} 
	});
	return false;

}
//下载 方法
function filedown(obj){
	var file_path = $(obj).attr('file');
	var file_name = $(obj).attr('filename');
	if(file_path && file_name){
		var url = "index.php?m=file&a=filedownload"+"&file_path="+file_path+"&file_name="+file_name;
		window.location.href = url;
	}else{
		alert_crm('该文件不存在，请选择其他文件！');
	}
}

//获取URL参数
function getUrlParam(name){
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!=null)return  unescape(r[2]); return null;
}

//jquery控制input只能输入数字和两位小数
function num_input(obj){
	obj.value = obj.value.replace(/[^\d.]/g,""); //清除"数字"和"."以外的字符
	obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字
	obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个, 清除多余的
	obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
	obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); //只能输入两个小数
}

//默认两位小数
function bu(txtObj) {
	txtObj.value = Number(txtObj.value).toFixed(2);
}

//附件查看
$(document).on('click','.file_view',function(){
	var path = $(this).attr('rel');
	var title = $(this).attr('title');
	layer.open({
		type: 2,
		title: title,
		shadeClose: true,
		shade: false,
		maxmin: true, //开启最大化最小化按钮
		area: ['800px', '600px'],
		content: path
	});
});

//清除dialog缓存，关闭dialog执行“close”回调函数时调用
function dialog_destroy(obj){
	obj.html('<div class="spiner-example">\
        <div class="sk-spinner sk-spinner-three-bounce">\
            <div class="sk-bounce1"></div>\
            <div class="sk-bounce2"></div>\
            <div class="sk-bounce3"></div>\
        </div>\
    </div>');
}

//toastr 右上角弹框提示信息
function rtal(info, type = 'success')
{
	toastr.options = {
        closeButton: true,
        progressBar: true,
        showMethod: 'slideDown',
        timeOut: 2000,
        fadeIn: 7000
	};
	if (type == 'warning') {
		toastr.warning('软件', info);
	} else if(type == 'error'){
        toastr.error('软件',info);
    }else{
        toastr.success('软件',info);
    }
}

function ajax_loading(msg = '数据提交中')
{
	swal({
		title: msg,
		text: '......',
		type: 'info',
		showConfirmButton: false
	});
}

/**
 * 格式化时间戳
 * @param {*} date_str 		Y m d H i s
 * @param {*} timestamp 
 */
function y_date(date_str , timestamp = null)
{
	let time;
	if (timestamp === null) {
		time = new Date();
	} else {
		time = new Date(timestamp);
	}
	let Y = time.getFullYear();  // 获取完整的年份(4位,1970)
	let m = time.getMonth() + 1;  // 获取月份(0-11,0代表1月,用的时候记得加上1)
	if (m < 10) m = '0' + m;
	let d = time.getDate();  // 获取日(1-31)
	if (d < 10) d = '0' + d;
	let H = time.getHours();  // 获取小时数(0-23)
	if (H < 10)  H = '0' + H;
	let i = time.getMinutes();  // 获取分钟数(0-59)
	if (i < 10) i = '0' + i;
	let s = time.getSeconds();  // 获取秒数(0-59)
	if (s < 10) s = '0' + s;
	
	date_str = date_str.replace('Y', Y);
	date_str = date_str.replace('m', m);
	date_str = date_str.replace('d', d);
	date_str = date_str.replace('H', H);
	date_str = date_str.replace('i', i);
	date_str = date_str.replace('s', s);
	return date_str;
}


/**
 * 列拖动
 * @param {*} table_id 表格iD
 * 注意： 表头必须写 thead th
 */
function drag_col(table_id) {
	// 将表格th声明为可拖拽
	$(table_id + ' thead th,' + table_id + ' thead td').attr('draggable', true);
	// 被拖数据的数据类型和值
	$(table_id + ' thead th,' + table_id + ' thead td').on('dragstart', function (ev) {
		ev.originalEvent.dataTransfer.setData("TH_ID", ev.currentTarget.cellIndex);
	});
	// 阻止默认行为
	$(table_id + ' thead th,' + table_id + ' thead td').on('dragover', function (ev) {
		ev.preventDefault();
	});
	$(table_id + ' thead th,' + table_id + ' thead td').css('cursor', 'move');
	$(table_id + ' thead th,' + table_id + ' thead td').on('drop', function (ev) {
		// 阻止默认行为
		ev.preventDefault();
		// 获取被拖的数据的索引
		var s_id = ev.originalEvent.dataTransfer.getData("TH_ID");
		// 当前要放下的索引
		var e_id = this.cellIndex;
		if (s_id == e_id) return;
		// 遍历tbody的tr
		$(table_id + ' tr').each(function (i) {
			// 提供一个容器,分别用来存储原位置的一列和被拖拽的一列
			let box1, box2, temp, dh, td, th;
			td = $(this).children('td');
			th = $(this).children('th');
			dh = td.length > th.length ? td : th;
			// 遍历tr中的td
			dh.each(function () {
				// 存储原位置的一列
				if (this.cellIndex == e_id) {
					box1 = this;
				}
				// 存储被拖拽的一列
				if (this.cellIndex == s_id) {
					box2 = this;
				}
			});
			temp1 = $(box1).clone(1);
			temp2 = $(box2).clone(1);
			if (s_id > e_id) {
				$(box2).after(temp1);
				$(box1).after(temp2);
			} else {
				$(box2).before(temp1);
				$(box1).before(temp2);
			}
			$(box1).remove();
			$(box2).remove();
		});
	});
}


/**
 * 时间范围选择
 * 
 * @param {*} inp 	input框 $('#id')
 * @param {*} data 	后台数据
 */
function y_daterangepicker(inp, data = {}, format = 'YYYY-MM-DD') {
	let c = inp.clone();
	let _value = data.value;
	inp.addClass('hide');
	inp.after(c);
	c.removeAttr('id');
	c.attr('name', 'c_' + inp.attr('name'));
	c.daterangepicker({
		startDate: data.start_date,
		endDate: data.end_date,
		alwaysShowCalendars: false,	// 右侧时间框展示
		ranges: data.ranges,
		opens: 'right', //日期选择框的弹出位置  
		buttonClasses: ['btn btn-default'],
		applyClass: 'btn-small btn-primary blue',
		cancelClass: 'btn-small',
		separator: 'to',
		linkedCalendars: false,
		showDropdowns: true,
		locale: {
			applyLabel: '确定',
			cancelLabel: '取消',
			fromLabel: '起始时间',
			toLabel: '结束时间',
			customRangeLabel: '自定义',
			daysOfWeek: ['日', '一', '二', '三', '四', '五', '六'],
			monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
			firstDay: 1,
			format: format, //控件中from和to 显示的日期格式
		}
	}, function (start, end, label) {
		// 选择后回调
		inp.val(start.format(format) + ' - ' + end.format(format));
		if (label != '自定义') {
			setTimeout(() => {
				c.val(label);
				_value = label;
			}, 10);
		} else {
			_value = inp.val();
		}
	});
	c.val(_value);
	c.on('blur', () => {
		c.val(_value);
	})
	// setTimeout(() => {
	// $('.daterangepicker').find('.calendar.left').html('');
	// $('.daterangepicker').find('.calendar.right').html('');
	// let div = $('<div class="calendar"></div>');
	// div.html('\
	// 	<label>开始时间\
	// 	</label>\
	// 	<input class="date_range_start input-mini form-control active" type="text" name="daterangepicker_start" autocomplete="off" onclick="WdatePicker()">\
	// 	<label>结束时间：\
	// 	</label>\
	// 	<input class="date_range_end input-mini form-control active" type="text" name="daterangepicker_start" autocomplete="off" onclick="WdatePicker({onpicking: _validate()})">\
	// ');
	// $('.daterangepicker').find('.ranges').after(div);
	// }, 100);
}


/**
 * 统计模块跳转
 * @param {*} obj 
 */
function analytics_jump(obj = { url: undefined, param: undefined }) {
	if (obj.url == undefined) {
		console.log('url参数错误');
		return false;
	}
	let param;
	if (typeof (obj.param) == 'function') {
		param = obj.param();
	} else if (typeof (obj.param) == 'object') {
		param = obj.param;
	} else if (obj.param == undefined) {
		console.log('param参数错误');
		return false;
	}
	return url = obj.url + parseParam(param, '_analytics');
}


/**
 * 数组、对象转url字符串
 * @param {*} param 
 * @param {*} key 
 * @param {*} encode 
 */
function parseParam(param, key, encode) {
	if (param == null) return '';
	var paramStr = '';
	var t = typeof (param);
	if (t == 'string' || t == 'number' || t == 'boolean') {
		paramStr += '&' + key + '=' + ((encode == null || encode) ? encodeURIComponent(param) : param);
	} else {
		for (var i in param) {
			// var k = key == null ? i : key + (param instanceof Array ? '[' + i + ']' : '.' + i);
			var k = key == null ? i : key + '[' + i + ']';
			paramStr += parseParam(param[i], k, encode);
		}
	}
	return paramStr;
};
