<!DOCTYPE html>
<html class="x-admin-sm">

<head>
    <meta charset="UTF-8">
    <title>添加任务</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi"/>
    <link rel="stylesheet" href="./css/font.css">
    <link rel="stylesheet" href="./css/xadmin.css">
    <script type="text/javascript" src="./lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="./js/xadmin.js"></script>
    <script type="text/javascript" src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]--></head>

<body>
<div class="layui-fluid">
    <div class="layui-row">
        <form class="layui-form" method="post" action="addTask">

            <div class="layui-form-item">
                <label for="username" class="layui-form-label">
                    <span class="x-red">*</span>店铺名称</label>
                <div class="layui-input-inline">
                    <input type="text" id="shopName" name="shopName" required="必须输入店铺名称" lay-verify="required"
                           autocomplete="off" class="layui-input"></div>
            </div>


            <div class="layui-form-item">
                <label for="username" class="layui-form-label">
                    <span class="x-red">*</span>产品连接</label>
                <div class="layui-input-inline">
                    <input type="text" id="productUrl" name="productUrl" required="必须输入产品链接" lay-verify="required"
                           autocomplete="off" class="layui-input"></div>
            </div>

            <div class="layui-form-item">
                <label for="phone" class="layui-form-label">
                    <span class="x-red">*</span>产品类目</label>
                <div class="layui-input-inline">
                    <input type="text" id="productType" name="productType" required=""
                           autocomplete="off" class="layui-input"></div>
            </div>

            <div class="layui-form-item">
                <label for="username" class="layui-form-label">
                    <span class="x-red">*</span>产品图片</label>
                <div class="layui-input-inline uploadHeadImage">
                    <div class="layui-upload-drag" id="headImg">
                        <i class="layui-icon"></i>
                        <p>点击上传图片，或将图片拖拽到此处</p>
                    </div>
                </div>
                <div class="layui-input-inline">
                    <div class="layui-upload-list">
                        <img class="layui-upload-img headImage" id="demo1" style="max-height: 200px;max-width: 200px">
                        <p id="demoText"></p>
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <label for="phone" class="layui-form-label">
                    <span class="x-red">*</span>手机端价格</label>
                <div class="layui-input-inline">
                    <input type="text" id="phonePrice" name="phonePrice" required="" lay-verify="float"
                           autocomplete="off" class="layui-input"></div>
            </div>

            <div class="leimu" style="margin-left: 30px; margin-bottom: 30px">
                <span class="x-red">*</span>
                <span style="margin-right: 10px">关键词</span>
                <div class="layui-input-inline layui-show-xs-block">
                    <input type="text" name="key[]" autocomplete="off" lay-verify="required" class="layui-input">
                </div>

                <span class="x-red" style="margin-left: 20px;">*</span>
                <span style="margin-right: 10px">单价</span>
                <div class="layui-input-inline layui-show-xs-block">
                    <input type="text" name="amount[]" autocomplete="off" lay-verify="required" class="layui-input">
                </div>

                <span class="x-red" style="margin-left: 20px;">*</span>
                <span style="margin-right: 10px">数量</span>
                <div class="layui-input-inline layui-show-xs-block">
                    <input type="text" name="quantity[]" autocomplete="off" lay-verify="required" class="layui-input">
                </div>
            </div>

            <div class="layui-show-xs-block">
                <div class="layui-btn" style="margin-left: 25px;margin-bottom: 30px" onclick="addDomLeiMu()">
                    <i class="layui-icon">新增关键词</i></div>
            </div>

            <div class="layui-form-item">
                <label for="L_email" class="layui-form-label">
                    <span class="x-red">*</span>任务要求</label>
                <div class="layui-input-inline">
                    <input type="text" id="taskRequire" name="taskRequire" required=""
                           autocomplete="off" class="layui-input"></div>
            </div>

            <div class="layui-form-item">
                <label for="L_email" class="layui-form-label">
                    <span class="x-red">*</span>单笔佣金</label>
                <div class="layui-input-inline">
                    <input type="text" id="commission" name="commission"
                           autocomplete="off" class="layui-input"></div>
            </div>


            <div class="layui-form-item">
                <label for="L_repass" class="layui-form-label"></label>
                <button class="layui-btn" lay-filter="add" lay-submit="">增加</button>
            </div>
        </form>
    </div>
</div>
<script>

    layui.use(['form', 'layer'],
        function () {
            $ = layui.jquery;
            var form = layui.form,
                layer = layui.layer;

            //自定义验证规则
            // form.verify({
            //     nikename: function (value) {
            //         if (value.length < 5) {
            //             return '昵称至少得5个字符啊';
            //         }
            //     },
            //     pass: [/(.+){6,12}$/, '密码必须6到12位'],
            //     repass: function (value) {
            //         if ($('#L_pass').val() != $('#L_repass').val()) {
            //             return '两次密码不一致';
            //         }
            //     }
            // });

            //监听提交
            form.on('submit(add)',
                function (data) {
                    $.ajax({
                        url: 'addTask',
                        method: 'post',
                        data: data.field,
                        dataType: 'JSON',
                        success: function (res) {
                            if (res.code = 0) {
                                layer.alert("增加成功", {
                                        icon: 6
                                    }, function () {
                                        //关闭当前frame
                                        xadmin.close();
                                        // 可以对父窗口进行刷新
                                        xadmin.father_reload();
                                    });
                            } else {
                              layer.alert("添加失败请稍后再试",{icon:5})  ;
                            }
                        }
                    });
                    return false;
                });

        });

</script>
<script>
    function addDomLeiMu() {
        var text =
            '<div style="margin-top: 25px; margin-bottom: 20px; margin-left: 5px">' +
            '<span class="x-red">*</span>' +
            '<span style="margin-right: 10px">关键词</span>' +
            ' <div class="layui-input-inline layui-show-xs-block">' +
            '<input type="text" name="key[]" autocomplete="off" lay-verify="required" class="layui-input">' +
            '</div>' +

            '<span class="x-red" style="margin-left: 20px;">*</span>' +
            '<span style="margin-right: 10px">单价</span>' +
            '<div class="layui-input-inline layui-show-xs-block">' +
            '<input type="text" name="amount[]" autocomplete="off" lay-verify="required" class="layui-input">' +
            '</div>' +

            '<span class="x-red" style="margin-left: 20px;">*</span>' +
            '<span  style="margin-right: 10px">数量</span>' +
            '<div class="layui-input-inline layui-show-xs-block">' +
            '<input type="text" name="quantity[]" autocomplete="off" lay-verify="required" class="layui-input">' +
            '</div>' + '</div>'
        ;

        $(".leimu").append(text);
    }

    layui.use(["jquery", "upload", "form", "layer", "element"], function () {
        var $ = layui.$,
            element = layui.element,
            layer = layui.layer,
            upload = layui.upload,
            form = layui.form;
        //拖拽上传
        var uploadInst = upload.render({
            elem: '#headImg',
            url: 'fileUpload?type=1',
            size: 500,
            before: function (obj) {
                //预读本地文件示例，不支持ie8
                obj.preview(function (index, file, result) {
                    $('#demo1').attr('src', result); //图片链接（base64）
                });
            }
            , done: function (res) {
                //如果上传失败
                if (res.code != 0) {
                    return layer.msg('上传失败');
                }
                //上传成功
                //把地址放入一个隐藏的input中, 和表单一起提交到后台, 此处略..
                uploadHeadImage(res.data.fileName);
                var demoText = $('#demoText');
                demoText.html('<span style="color: #8f8f8f;">上传成功!!!</span>');
            }
            , error: function () {
                //演示失败状态，并实现重传
                var demoText = $('#demoText');
                demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-mini demo-reload">重试</a>');
                demoText.find('.demo-reload').on('click', function () {
                    uploadInst.upload();
                });
            }
        });
        element.init();
    });

    function uploadHeadImage(fileName) {
        $('<input>').attr({
            type: 'hidden',
            id: 'fileName',
            name: 'fileName',
            value: fileName
        }).appendTo('form');
    }
</script>
</body>

</html>
