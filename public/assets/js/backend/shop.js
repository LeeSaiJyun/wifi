define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'shop/index',
                    add_url: 'shop/add',
                    edit_url: 'shop/edit',
                    del_url: 'shop/del',
                    table: 'shop',
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
                        {field: 'name', title: __('Name') , operate: 'LIKE'},
                        {field: 'appid', title: __('Appid')},
                        {field: 'secretkey', title: __('Secretkey')},
                        {field: 'appsecret', title: __('Appsecret')},
                        {field: 'status_text', title: __('Status'), align: 'status', formatter: Table.api.formatter.status, searchList: {'0': __('Status 0'), '1': __('Status 1'),'-1': __('Status -1')}, style: 'min-width:100px;'},
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