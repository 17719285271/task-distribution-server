<!DOCTYPE html>
<html class="x-admin-sm">
<head>
    <meta charset="UTF-8">
    <title>任务列表</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport"
          content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi"/>
    <link rel="stylesheet" href="./css/font.css">
    <link rel="stylesheet" href="./css/xadmin.css">
    <script src="./lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="./js/xadmin.js"></script>
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="x-nav">
    <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right"
       onclick="location.reload()" title="刷新">
        <i class="layui-icon layui-icon-refresh" style="line-height:30px"></i></a>
</div>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body ">
                    <form class="layui-form layui-col-space5">
                        <div class="layui-inline layui-show-xs-block">
                            <input class="layui-input" autocomplete="off" placeholder="开始日" name="startDate" id="start"
                                   value="@if(isset($params["startDate"])) {{$params["startDate"]}} @endif">
                        </div>
                        <div class="layui-inline layui-show-xs-block">
                            <input class="layui-input" autocomplete="off" placeholder="截止日" name="endDate" id="end"
                                   value="@if(isset($params["endDate"])) {{$params["endDate"]}} @endif">
                        </div>
                        <div class="layui-inline layui-show-xs-block">
                            <input type="text" name="businessName" id="businessName" placeholder="商家名称"
                                   autocomplete="off" class="layui-input"
                                   value="@if(isset($params["businessName"])) {{$params["businessName"]}} @endif">
                        </div>
                        <div class="layui-inline layui-show-xs-block">
                            <button class="layui-btn" lay-submit="" lay-filter="sreach"><i
                                    class="layui-icon">&#xe615;</i></button>
                        </div>
                    </form>
                </div>
                <div class="layui-card-header">
                    <button class="layui-btn layui-btn-danger" onclick="assignTask()"><i class="layui-icon"></i>分配任务
                    </button>
                </div>
                <div class="layui-card-body layui-table-body layui-table-main">
                    <table class="layui-table layui-form">
                        <thead>
                        <tr>
                            <th>
                                <input type="checkbox" lay-filter="checkall" name="" lay-skin="primary">
                            </th>
                            <th>任务编号</th>
                            <th>添加日期</th>
                            <th>商家名称</th>
                            <th>产品链接</th>
                            <th>手机价格</th>
                            <th>关键词</th>
                            <th>单笔佣金</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($pageData as $data)
                            <tr>
                                <td>
                                    <input type="checkbox" name="id" value="{{$data->task_id}}" lay-skin="primary">
                                </td>
                                <td>{{$data->task_id}}</td>
                                <td>{{$data->created_at}}</td>
                                <td>{{$data->business_name}}</td>
                                <td>{{$data->product_url}}</td>
                                <td>{{$data->phone_price}}</td>
                                <td>{{$data->key}}</td>
                                <td>{{$data->commission}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="layui-card-body ">
                    <div class="page">
                        <div>
                            {{$pageData->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    layui.use(['laydate', 'form'], function () {
        var laydate = layui.laydate;
        var form = layui.form;


        // 监听全选
        form.on('checkbox(checkall)', function (data) {

            if (data.elem.checked) {
                $('tbody input').prop('checked', true);
            } else {
                $('tbody input').prop('checked', false);
            }
            form.render('checkbox');
        });

        //执行一个laydate实例
        laydate.render({
            elem: '#start' //指定元素
        });

        //执行一个laydate实例
        laydate.render({
            elem: '#end' //指定元素
        });


    });

    /*用户-删除*/
    function member_del(obj, id) {
        layer.confirm('确认要删除吗？', function (index) {
            //发异步删除数据
            $(obj).parents("tr").remove();
            layer.msg('已删除!', {icon: 1, time: 1000});
        });
    }


    function assignTask(argument) {
        var ids = [];

        // 获取选中的id
        $('tbody input').each(function (index, el) {
            if ($(this).prop('checked')) {
                ids.push($(this).val())
            }
        });

        if (ids.length == 0) {
            layer.alert('请至少选择一个任务');
        } else {
            var url = "assignTask?taskId=" + ids;
            xadmin.open('分配任务', url, 600, 400)
        }
    }
</script>
</html>
