define(['jquery', 'bootstrap', 'backend', 'addtabs', 'table', 'echarts', 'echarts-theme', 'template'], function ($, undefined, Backend, Datatable, Table, Echarts, undefined, Template) {

    var Controller = {
        index: function () {
            // 基于准备好的dom，初始化echarts实例
            var myChart = Echarts.init(document.getElementById('echart'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                title: {
                    text: '',
                    subtext: ''
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: [__('Send count'), __('Send user')]
                },
                toolbox: {
                    show: false,
                    feature: {
                        magicType: {show: true, type: ['stack', 'tiled']},
                        saveAsImage: {show: true}
                    }
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: Orderdata.column
                },
                yAxis: {

                },
                grid: [{
                        left: 'left',
                        top: 'top',
                        right: '10',
                        bottom: 30
                    }],
                series: [{
                        name: __('Send count'),
                        type: 'line',
                        smooth: true,
                        areaStyle: {
                            normal: {
                            }
                        },
                        lineStyle: {
                            normal: {
                                width: 1.5
                            }
                        },
                        data: Orderdata.senddata
                    },
                    {
                        name: __('Send user'),
                        type: 'line',
                        smooth: true,
                        areaStyle: {
                            normal: {
                            }
                        },
                        lineStyle: {
                            normal: {
                                width: 1.5
                            }
                        },
                        data: Orderdata.mobiledata
                    }]
            };

            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option);

            //动态添加数据，可以通过Ajax获取数据然后填充
            /*
            setInterval(function () {
                Orderdata.column.push((new Date()).toLocaleTimeString().replace(/^\D*!/, ''));
                var amount = Math.floor(Math.random() * 200) + 20;
                Orderdata.mobiledata.push(amount);
                Orderdata.paydata.push(Math.floor(Math.random() * amount) + 1);

                //按自己需求可以取消这个限制
                if (Orderdata.column.length >= 20) {
                    //移除最开始的一条数据
                    Orderdata.column.shift();
                    Orderdata.paydata.shift();
                    Orderdata.mobiledata.shift();
                }
                myChart.setOption({
                    xAxis: {
                        data: Orderdata.column
                    },
                    series: [{
                            name: __('Send count'),
                            data: Orderdata.paydata
                        },
                        {
                            name: __('Send user'),
                            data: Orderdata.mobiledata
                        }]
                });
            }, 2000);
            */
            $(window).resize(function () {
                myChart.resize();
            });

            //读取FastAdmin的更新信息
            $.ajax({
                url: Config.fastadmin.api_url + '/news/index',
                type: 'post',
                dataType: 'jsonp',
                success: function (ret) {
                    $("#news-list").html(Template("newstpl", {news: ret.newslist}));
                }
            });
            $.ajax({
                url: Config.fastadmin.api_url + '/forum/discussion',
                type: 'post',
                dataType: 'jsonp',
                success: function (ret) {
                    $("#discussion-list").html(Template("discussiontpl", {news: ret.discussionlist.slice(0,6)}));
                }
            });
        }
    };

    return Controller;
});