define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'auth/admin/index',
                    add_url: 'auth/admin/add',
                    edit_url: 'auth/admin/edit',
                    del_url: 'auth/admin/del',
                    multi_url: 'auth/admin/multi',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'state', checkbox: true, },
                        {field: 'id', title: 'ID'},
                        {field: 'username', title: __('Username')},
                        {field: 'nickname', title: __('Nickname')},
                        {field: 'groups_text', title: __('Group'), operate:false, formatter: Table.api.formatter.label},
                        {field: 'phone', title: __('Phone')},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status, searchList: {'normal': __('Normal'), 'hidden': __('Hidden')}, style: 'min-width:100px;'},
                        {field: 'logintime', title: __('Login time'), formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: function (value, row, index) {
                                if(row.id == Config.admin.id){
                                    return '';
                                }
                                return Table.api.formatter.operate.call(this, value, row, index);
                            }}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Form.api.bindevent($("form[role=form]"));
        },
        edit: function () {
            Form.api.bindevent($("form[role=form]"));
        }
    };
    return Controller;
});

//加载卡包
$.post("{:url('czcard/index/cardpackage')}", {}, function (data) {
    console.log(data);
    if (data.code == 0) {
        if (data.data.count > 0) {
            console.log(data);
            var sb = new StringBuilder();
            $.each(data.data, function (index, item) {
                sb.append('<div class="weui-panel weui-panel_access" style="border-radius:5px;margin:15px 15px;top:15px;">');
                sb.append(' <label>');
                sb.append(' <div class="weui-panel__hd weui-btn_primary" style="color: #FFF;">¥');
                sb.append(' <span style="font-size: 20px;">'+item.leftcredit+'</span>');
                sb.append(' </div>');
                sb.append(' <div class="weui-media-box weui-media-box_text ">');
                sb.append(' <h4 class="weui-media-box__title">总额：'+item.credit+'</h4>');
                sb.append(' <input type="checkbox" class="weui-check" name="checkbox1" id="s11">');
                sb.append(' <i class="weui-icon-checked" style="float: right;"></i>');
                sb.append(' <p class="weui-media-box__desc">有限期至：'+item.enddate+'</p>');
                sb.append(' <p class="weui-media-box__desc">'+item.cardno+'</p>');
                sb.append(' </div>');
                sb.append(' </label>');
                sb.append('</div>');
            });
            $("#card").empty();
            $("#card").html(sb.toString());
        }
        else {
            $("#div_baodanlist").empty();
            $("#div_baodanlist").html('<div class="weui-cells__title" style="text-align: center">没有找到有效报价单信息</div>');
        }

    }
}, "json");
