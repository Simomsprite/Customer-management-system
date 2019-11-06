$(document).on('click','.call',function(){
    var width = $('#wrapper').width() * 0.9;
    var model_id = $(this).attr('rel');
    var model = $(this).attr('model');
    var title = '客户信息';
    var tel = $(this).attr('phone');
    // iframe_id++;
    // //话机的呼出
    if (model == 'leads') {
        title = '线索信息';
    }
    layer.open({
        type: 2,
        title: title,
        shadeClose: true,
        shade: false,
        maxmin: true, //开启最大化最小化按钮
        area: [width+'px', '600px'],
        content: './index.php?m=call&a=data&tel='+tel+'&model_id='+model_id+'&model='+model
    });
    layer_open_big = false;
    swal.close();
});