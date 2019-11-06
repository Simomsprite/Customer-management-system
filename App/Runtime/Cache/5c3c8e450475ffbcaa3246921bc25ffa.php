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




<script src="__PUBLIC__/waps/js/cropper/cropper.min.js"></script>
<script src="__PUBLIC__/waps/js/cropper/jcrop.js"></script>
<script src="__PUBLIC__/waps/js/cropper/exif.js"></script>
<link type="text/css" rel="stylesheet" href="__PUBLIC__/waps/style/cropper/cropper.min.css">
<body>
<div class="wrap-box">
    <header class="abe-header am-header-default"> <a href="javascript:history.go(-1)" class="return iconfont">&#xe612;</a>
        <h1 class="abe-header-title">修改头像</h1></header>
<div class="ibox-content" style="border: none;">
    <div class="nav pull-left" >
        <span style="font-weight:900;line-height:40px;">个人头像</span>
    </div>
</div>
    <br/>
<form id="form1" enctype="multipart/form-data" action="<?php echo U('Userm/userimg');?>" class="form-horizontal" method="post" style="margin-top:10px;">
    <input type="hidden" name="user_id" value="<?php echo ($_SESSION['user_id']); ?>" />
    <div class="ibox-content" style="border-color: #e7eaec;border-width: 1px 0;">
        <div class="col-xs-10 col-sm-10 col-lg-9">
            <p style="color: gray;">选择一张个人正面照片作为头像</p>
        </div>
        <div class="col-xs-10 col-sm-10 ">
            <div class="row">
                <div class="col-sm-6">
                    <div class="image-crop" style="height:50px;width:50px;">
                        <img id="img1"
                        <?php if($user_info['thumb_path'] != ''): ?>src="<?php echo ($user_info['img']); ?>"
                            <?php else: ?> src="__PUBLIC__/img/avatar_default.png"<?php endif; ?>
                        />
                    </div>
                    </div>
                </div>
                <div class="col-sm-6" style="margin-top:-35px;">
                    <h4 style="padding-bottom:10px;">图片预览：</h4>
                    <div class="img-preview img-preview-sm" id="canvas" style="height:200px;width:100px;"></div>
                    <p style="margin-top:10px;">
                      <br/>
                    </p>
                    <div class="btn-group">
                        <label title="选择图片" for="inputImage" class="btn btn-primary">
                            <input type="file" accept="image/x-png,image/gif,image/jpeg" multiple="multiple" name="img" id="inputImage"  class="hide">
                        </label>
                        <button title="上传头像" id="download" class="btn btn-primary">提交头像</button>
                    </div>
                     <h4>图片操作</h4>
                    <div class="btn-group">
                        <button class="btn btn-white" id="zoomIn" type="button">放大</button>
                        <button class="btn btn-white" id="zoomOut" type="button">缩小</button>
                        <button class="btn btn-white" id="rotateRight" type="button">右旋转</button>
                        <button class="btn btn-white" id="rotateLeft" type="button">左旋转</button>
                        <!--<button class="btn btn-warning" id="setDrag" type="button">裁剪</button>-->
                    </div>
                </div>
            </div>
        </div>
</form>
</div>
</body>
<script>

    /**
     * 将以base64的图片url数据转换为Blob
     * @param urlData
     * 用url方式表示的base64图片数据
     */
    function convertBase64UrlToBlob(urlData){
        var bytes=window.atob(urlData.split(',')[1]);        //去掉url的头，并转换为byte

        //处理异常,将ascii码小于0的转换为大于0
        var ab = new ArrayBuffer(bytes.length);
        var ia = new Uint8Array(ab);
        for (var i = 0; i < bytes.length; i++) {
            ia[i] = bytes.charCodeAt(i);
        }

        return new Blob( [ab] , {type : 'image/jpeg'});
    }

    /**
     * 显示图片
     */
    $(function(){
        var $image = $(".image-crop > img");

       // disable();
        $($image).cropper({
            aspectRatio: 1,
            preview: ".img-preview",
            //autoCrop:false,
            //  dragCrop:false,
            // center:true,
            done: function(data) {
                // Output the result data for cropping image.
                //clear();
            }
        });

        var $inputImage = $("#inputImage");
        var file=0;
        let Orientation = null;


            $inputImage.change(function () {
                files = this.files;
                file = files[0];
                let fileReader=new FileReader();
                // let size=file['size'];
                /*if (size>204800) {
                    showtab1(2,"图片不得大于200KB");
                    return false;
                }*/
                // console.log(size);

                if (file) {
                    console.log("爸爸告诉你这个文件存在了呢");
                    console.log(file);
                    EXIF.getData(file, function () {
                        //console.log(EXIF.getAllTags(this));
                        Orientation = EXIF.getTag(this, 'Orientation');
                        console.log(EXIF.getTag(this, 'Orientation'));
                    });

                        fileReader.onload = function (e) {
                            $inputImage.val("");
                            $image.cropper("reset", true).cropper("replace", this.result);
                            console.log(1111);
                            let image = new Image();
                           //image.src = fileReader.result;
                            image.src = e.target.result;
                            image.onload = function() {
                                var expectWidth = this.naturalWidth;
                                var expectHeight = this.naturalHeight;
                                console.log(expectWidth);
                                console.log(expectHeight);
                                if (this.naturalWidth > this.naturalHeight && this.naturalWidth > 800) {
                                    expectWidth = 800;
                                    expectHeight = expectWidth * this.naturalHeight / this.naturalWidth;
                                } else if (this.naturalHeight > this.naturalWidth && this.naturalHeight > 1200) {
                                    expectHeight = 1200;
                                    expectWidth = expectHeight * this.naturalWidth / this.naturalHeight;
                                }
                                var canvas = document.createElement("canvas");
                                var ctx = canvas.getContext("2d");
                                console.log(ctx);
                                canvas.width = expectWidth;
                                canvas.height = expectHeight;
                                ctx.drawImage(this, 0, 0, expectWidth, expectHeight);
                                var u = navigator.userAgent;
                                var base64=null;
                                console.log(u);
                                //修复ios
                                if (u.match(/iphone/i)) {
                                    console.log('iphone');
                                    //如果方向角不为1，都需要进行旋转 added by lzk
                                    if(Orientation != "" && Orientation != 1) {
                                        console.log('旋转处理');
                                        switch (Orientation) {
                                            case 6://需要顺时针（向左）90度旋转
                                                console.log('需要顺时针（向左）90度旋转');
                                                rotateImg(this, 'left', canvas);
                                                break;
                                            case 8://需要逆时针（向右）90度旋转
                                                console.log('需要逆时针（向右）90度旋转');
                                                rotateImg(this, 'right', canvas);
                                                break;
                                            case 3://需要180度旋转
                                                console.log('需要180度旋转');
                                                rotateImg(this, 'right', canvas);//转两次
                                                rotateImg(this, 'right', canvas);
                                                break;
                                        }
                                    }
                                    //base64 在外定义为全局变量
                                    //下面base64为得到旋转后的base64图片
                                    base64 = canvas.toDataURL("image/jpeg", 0.8);
                                    /*var type = 'jpeg';
                                    var fixtype = function (type) {
                                        type = type.toLocaleLowerCase().replace(/jpg/i, 'jpeg');
                                        var r = type.match(/png|jpeg|bmp|gif/)[0];
                                        return 'image/' + r;
                                    };
                                    base64 = base64.replace(fixtype(type), 'image/jpeg');
                                    // saveFile(base64, '111')  此处是如果想要保存当前图片到本地的话;
                                    //这里是把已经旋转过的图片路径赋值到img中
                                    console.log(base64);
                                    $("#img1").attr("src", base64);*/
                                }
                                /*else if (u.match(/Android/i)) */
                                else{
                                    console.log(Orientation);
                                    if(Orientation != "" && Orientation != 1){
                                        //alert('旋转处理');
                                        switch(Orientation){
                                            case 6://需要顺时针（向左）90度旋转
                                                console.log('需要顺时针（向左）90度旋转');
                                                rotateImg(this,'left',canvas);
                                                break;
                                            case 8://需要逆时针（向右）90度旋转
                                                console.log('需要顺时针（向右）90度旋转');
                                                rotateImg(this,'right',canvas);
                                                break;
                                            case 3://需要180度旋转
                                                console.log('需要180度旋转');
                                                rotateImg(this,'right',canvas);//转两次
                                                rotateImg(this,'right',canvas);
                                                break;
                                        }
                                    }
                                    base64 = canvas.toDataURL("image/jpeg", 0.8);
                                    /*var type = 'jpeg';
                                    var fixtype = function (type) {
                                        type = type.toLocaleLowerCase().replace(/jpg/i, 'jpeg');
                                        var r = type.match(/png|jpeg|bmp|gif/)[0];
                                        return 'image/' + r;
                                    };
                                    base64 = base64.replace(fixtype(type), 'image/jpeg');
                                    // saveFile(base64, '111')  此处是如果想要保存当前图片到本地的话;
                                    //这里是把已经旋转过的图片路径赋值到img中
                                    $("#img1").attr("src",base64);*/

                                }
                                $("#img1").attr("src", base64);
                            }
                        };
                        fileReader.readAsDataURL(file);
                }
            })

       /*if (window.FileReader) {
            $inputImage.change(function() {
                var fileReader = new FileReader(),
                    files = this.files,
                    file;

                if (!files.length) {
                    return;
                }

                 file = files[0];
                if (file['size']>204800) {
                    showtab1(2,"图片不得大于200KB");
                    return;
                }/!*else {
                    if (/^image\/\w+$/.test(file.type)) {
                        fileReader.readAsDataURL(file);
                        fileReader.onload = function () {
                            $inputImage.val("");
                            $image.cropper("reset", true).cropper("replace", this.result);
                        };

                    } else {
                        showMessage("请选择一张图片");
                    }
                }*!/

            });
        }
*/



        $("#download").click(function() {
            // window.open($image.cropper("getDataURL"));
            // alert($image.cropper("getDataURL"));
            // let img=$('#inputImage')[0].files;

            /*if ( img[0]['size']>204800){
                return;
            }*/

            var form=document.forms[0];
            var formData = new FormData(form);
            //这里连带form里的其他参数也一起提交了,如果不需要提交其他参数可以直接FormData无参数的构造函数
            //convertBase64UrlToBlob函数是将base64编码转换为Blob

            formData.append("blob",convertBase64UrlToBlob($image.cropper("getDataURL")), "image.jpeg");
            console.log(formData);




            //append函数的第一个参数是后台获取数据的参数名,和html标签的input的name属性功能相同
            //ajax 提交form
            $.ajax({
                url : form.action,
                type : "POST",
                data : formData,
                dataType: "json",
                processData : false,         // 告诉jQuery不要去处理发送的数据
                contentType : false,        // 告诉jQuery不要去设置Content-Type请求头
                async:true,

                success:function(data){
                    if(data== 1){
                        // showtab1(1,data.msg);
                    }else{
                        // showtab1(2,data.msg);
                    }
                }
            });
        });

        $("#zoomIn").click(function() {
            $image.cropper("zoom", 0.1);
        });

        $("#zoomOut").click(function() {
            $image.cropper("zoom", -0.1);
        });

        $("#rotateLeft").click(function() {
            $image.cropper("rotate", 45);
        });

        $("#rotateRight").click(function() {
            $image.cropper("rotate", -45);
        });

        $("#setDrag").click(function() {
            $image.cropper("setDragMode", "crop");
        });
    })

    function rotateImg(img, direction,canvas) {
        console.log(direction);
        console.log(canvas);
        //最小与最大旋转方向，图片旋转4次后回到原方向
        var min_step = 0;
        var max_step = 3;
        //var img = document.getElementById(pid);
        if (img == null)return;
        //img的高度和宽度不能在img元素隐藏后获取，否则会出错
        var height = img.height;
        var width = img.width;
        //var step = img.getAttribute('step');
        var step = 2;
        if (step == null) {
            step = min_step;
        }
        if (direction == 'right') {
            step++;
            //旋转到原位置，即超过最大值
            step > max_step && (step = min_step);
        } else {
            step--;
            step < min_step && (step = max_step);
        }
        //旋转角度以弧度值为参数
        var degree = step * 90 * Math.PI / 180;
        var ctx = canvas.getContext('2d');
        switch (step) {
            case 0:
                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0);
                break;
            case 1:
                canvas.width = height;
                canvas.height = width;
                ctx.rotate(degree);
                ctx.drawImage(img, 0, -height);
                break;
            case 2:
                canvas.width = width;
                canvas.height = height;
                ctx.rotate(degree);
                ctx.drawImage(img, -width, -height);
                break;
            case 3:
                canvas.width = height;
                canvas.height = width;
                ctx.rotate(degree);
                ctx.drawImage(img, -width, 0);
                break;
        }
    }



</script>
</html>