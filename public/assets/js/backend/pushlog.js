define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'pushlog/index',
                    add_url: 'pushlog/add',
                    edit_url: 'pushlog/edit',
                    del_url: 'pushlog/del',
                    multi_url: 'pushlog/multi',
                    table: 'push_log',
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
                        {field: 'push_num', title: __('Push_num')},
                        {field: 'success_num', title: __('Success_num')},
                        {field: 'push_date', title: __('Push_date')},
                        {field: 'error_time', title: __('Error_time')},
                        {field: 'createtime', title: __('Createtime'), formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), formatter: Table.api.formatter.datetime},
                        {field: 'shop.id', title: __('Shop.id')},
                        {field: 'shop.appid', title: __('Shop.appid')},
                        {field: 'shop.secretkey', title: __('Shop.secretkey')},
                        {field: 'shop.appsecret', title: __('Shop.appsecret')},
                        {field: 'shop.shopid', title: __('Shop.shopid')},
                        {field: 'shop.admin_id', title: __('Shop.admin_id')},
                        {field: 'shop.push_count', title: __('Shop.push_count')},
                        {field: 'shop.success_count', title: __('Shop.success_count')},
                        {field: 'shop.access_token', title: __('Shop.access_token')},
                        {field: 'shop.expires_time', title: __('Shop.expires_time'), formatter: Table.api.formatter.datetime},
                        {field: 'shop.remark', title: __('Shop.remark')},
                        {field: 'shop.status', title: __('Shop.status'), formatter: Table.api.formatter.status},
                        {field: 'shop.createtime', title: __('Shop.createtime'), formatter: Table.api.formatter.datetime},
                        {field: 'shop.updatetime', title: __('Shop.updatetime'), formatter: Table.api.formatter.datetime},
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