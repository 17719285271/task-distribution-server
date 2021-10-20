<!DOCTYPE html>
<html class="x-admin-sm">

<head>
    <meta charset="UTF-8">
    <title>分配任务</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi"/>
    <link rel="stylesheet" href="./css/font.css">
    <link rel="stylesheet" href="./css/xadmin.css">
    <script type="text/javascript" src="./lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="./js/xadmin.js"></script>
    <!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="layui-fluid">
    <div class="layui-row">
        <form class="layui-form">
            <div class="layui-form-item">
                当前已选中 <span class="x-red"><?php echo count($taskId) ?></span> 条任务等待分配，请至少添加<span
                    class="x-red">{{$needHands}}</span>位刷手旺旺号
            </div>

            <div class="layui-form-item">
                <div class="layui-upload-drag " id="test10">
                    <i class="layui-icon"></i>
                    <p>点击上传，或将文件拖拽到此处</p>
                </div>
            </div>
            @foreach($taskId as $id)
            <input name="taskId[]" type="hidden" value="<?php echo $id ?>">
            @endforeach
            <div class="layui-form-item">
                <label for="L_repass" class="layui-form-label"></label>
                <button class="layui-btn" lay-filter="add" lay-submit="">增加</button>
            </div>
        </form>
    </div>
</div>
<script>layui.use(['form', 'layer', 'jquery'],
        function () {
            $ = layui.jquery;
            var form = layui.form,
                layer = layui.layer;
            //监听提交
            form.on('submit(add)',
                function (data) {
                    $.ajax({
                        url: 'taskGeneration',
                        method: 'post',
                        data: data.field,
                        dataType: 'JSON',
                        success: function (res) {
                            if (res.code == 0) {
                                layer.alert("增加成功", {
                                    icon: 6
                                }, function () {
                                    //关闭当前frame
                                    xadmin.close();
                                    // 可以对父窗口进行刷新
                                    xadmin.father_reload();
                                });
                            } else {
                                layer.alert(res.message,{icon:5})  ;
                            }
                        }
                    });
                    return false;
                });

        });
</script>

<script>
    layui.use('upload', function () {
        var $ = layui.jquery
            , upload = layui.upload;

        upload.render({ //允许上传的文件后缀
            elem: '#test4'
            , url: 'fileUpload'
            , accept: 'file' //普通文件
            , exts: 'zip|rar|7z' //只允许上传压缩文件
            , done: function (res) {
                console.log(res)
            }
        });

        //拖拽上传
        upload.render({
            elem: '#test10'
            , url: 'fileUpload?type=2'
            , accept: 'file'
            , done: function (res) {
                if (res.code == 0) {
                    uploadFileForm(res.data.fileName);
                    layer.alert("上传成功");
                } else {
                    layer.alert("上传失败");
                }
            }
        });

    });

    function uploadFileForm(fileName) {
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
