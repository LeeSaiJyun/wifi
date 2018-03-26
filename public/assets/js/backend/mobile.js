define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'mobile/index',
                    add_url: 'mobile/add',
                    edit_url: 'mobile/edit',
                    del_url: 'mobile/del',
                    table: 'mobile',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'),operate:false},
                        {field: 'devmac', title: __('Devmac'), operate: 'LIKE %...%'},
                        {field: 'mac', title: __('Mac'), operate: 'LIKE %...%'},
                        {field: 'ssid', title: __('Ssid'), operate: 'LIKE', events: Controller.api.events.ssid, formatter: Controller.api.formatter.ssid},
                        {field: 'time', title: __('Time'), operate:false},
                        {field: 'status', title: __('Status'),formatter: Table.api.formatter.status, searchList: {'0': __('Status 0'), '1': __('Status 1'),'-1': __('Status -1')}, style: 'min-width:100px;'},
                        // {field: 'push_num', title: __('Push_num')},
                        // {field: 'last_push_time', title: __('Last_push_time'), formatter: Table.api.formatter.datetime},
                        // {field: 'createtime', title: __('Createtime'), formatter: Table.api.formatter.datetime},
                        // {field: 'updatetime', title: __('Updatetime'), formatter: Table.api.formatter.datetime},
                        {field: 'operate',  title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons: [
                                {name: 'ajax', title: __('重新发送'), classname: 'btn btn-xs btn-success btn-magic btn-ajax', icon: 'fa fa-magic', url: 'mobile/change',
                                    success: function (data, ret) {
                                        Layer.alert(ret.msg + ",返回数据：" + JSON.stringify(data));
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    }, error: function (data, ret) {
                                        console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },
                            ],
                            formatter: Table.api.formatter.operate
                        },
                    ]
                ],
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        change: function () {
            $(document).on('click', '.btn-callback', function () {
                Fast.api.close($("input[name=callback]").val());
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {//渲染的方法
                url: function (value, row, index) {
                    return '<div class="input-group input-group-sm" style="width:250px;"><input type="text" class="form-control input-sm" value="' + value + '"><span class="input-group-btn input-group-sm"><a href="' + value + '" target="_blank" class="btn btn-default btn-sm"><i class="fa fa-link"></i></a></span></div>';
                },
                ssid: function (value, row, index) {
                    return '<a class="btn btn-xs btn-ssid bg-success"><i class="fa fa-wifi"></i> ' + value + '</a>';
                },


            },
            events: {//绑定事件的方法
                ssid: {
                    //格式为：方法名+空格+DOM元素
                    'click .btn-ssid': function (e, value, row, index) {
                        e.stopPropagation();
                        console.log();
                        var container = $("#table").data("bootstrap.table").$container;
                        var options = $("#table").bootstrapTable('getOptions');
                        //这里我们手动将数据填充到表单然后提交
                        $("form.form-commonsearch [name='ssid']", container).val(value);
                        $("form.form-commonsearch", container).trigger('submit');
                    }
                },
            }
        }
    };
    return Controller;
});


