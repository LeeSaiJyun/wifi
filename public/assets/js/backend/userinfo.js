define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'userinfo/index',
                    add_url: 'userinfo/add',
                    edit_url: 'userinfo/edit',
                    del_url: 'userinfo/del',
                    multi_url: 'userinfo/multi',
                    table: 'userinfo',
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
                        {field: 'id', title: __('Id')},
                        {field: 'shop_id', title: __('Shop_id')},
                        {field: 'appid', title: __('Appid')},
                        {field: 'openid', title: __('Openid')},
                        {field: 'nickname', title: __('Nickname')},
                        {field: 'headimgurl', title: __('Headimgurl'), formatter: Table.api.formatter.url},
                        {field: 'tid', title: __('Tid')},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
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
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});