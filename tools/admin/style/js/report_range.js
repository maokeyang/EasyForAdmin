
    $(function() {

        var cb = function(start, end, label) {
            $('#reportrange span').html(start.format('YYYY/MM/DD') + ' - ' + end.format('YYYY/MM/DD'));
        }

        var optionSet2 = {
            maxDate : moment(), //最大时间
            startDate: moment().subtract('days', 31),
            endDate: moment(),
            opens: 'right',
            //ranges: {
            //    '今日': [moment().startOf('day'), moment()],
            //    '昨日': [moment().subtract('days', 1).startOf('day'), moment().subtract('days', 1).endOf('day')],
            //    '最近7日': [moment().subtract('days', 6), moment()],
            //    '最近30日': [moment().subtract('days', 29), moment()]
            //},
            showCustomRangeLabel: true,
            format : 'YYYY/MM/DD',
            autoApply: false,
            locale : {
                applyLabel : '确定',
                cancelLabel : '取消',
                fromLabel : '起始时间',
                toLabel : '结束时间',
                customRangeLabel : '自定义',
                daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
                monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月',
                    '七月', '八月', '九月', '十月', '十一月', '十二月' ],
                firstDay : 1
            }
        };

        $('#reportrange span').html(moment().format('YYYY/MM/DD') + ' - ' + moment().format('YYYY/MM/DD'));

        $('#reportrange').daterangepicker(optionSet2, cb);

        $('#reportrange').on('show.daterangepicker',
            function() {
            });
        $('#reportrange').on('hide.daterangepicker',
            function() {
            });
        $('#reportrange').on('apply.daterangepicker',
            function(ev, picker) {
                $('#reportrange span').html(picker.startDate.format('YYYY/MM/DD') + ' - ' +picker.endDate.format('YYYY/MM/DD'));

                var _data = {};
                $(".form-control.pull-right").each(function(){
                    _data[$(this).attr('name')] = $(this).val();
                });
                _data['from_date'] = picker.startDate.format('YYYY/MM/DD');
                _data['end_date'] = picker.endDate.format('YYYY/MM/DD');
                $.ajax({
                    type: "POST",
                    url:"?mod=report&act=card_ajax_data",
                    data: _data,
                    dataType: 'json',
                    success: function(data){
                        console.log(data);
                        var displayChart = echarts.init(document.getElementById('display_date_main'));

                        var selectOption = option;
                        selectOption.xAxis[0].data = data.x;
                        selectOption.series[0].data = data.y.cost_total;
                        selectOption.series[1].data = data.y.selling_total;

                        if(data.x.length >= 10) {
                            selectOption.series[0].barWidth = 0;
                            selectOption.series[1].barWidth = 0;
                        } else {
                            selectOption.series[0].barWidth = 30;
                            selectOption.series[1].barWidth = 30;
                        }


                        displayChart.setOption(selectOption);
                    }
                });
            });
        $('#reportrange').on('cancel.daterangepicker',
            function(ev, picker) {
            });



        $('#reportrange2').daterangepicker({
            maxDate : moment(), //最大时间
            opens: 'left',
            locale : {
                applyLabel : '确定',
                cancelLabel : '取消',
                fromLabel : '起始时间',
                toLabel : '结束时间',
                customRangeLabel : '自定义',
                daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
                monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月',
                    '七月', '八月', '九月', '十月', '十一月', '十二月' ],
                firstDay : 1
            }
        }, function(start, end, label) {
            $('#reportrange2 span').html(start.format('YYYY/MM/DD') + ' - ' +end.format('YYYY/MM/DD'));

            var _data = {};
            $(".form-control.pull-right").each(function(){
                _data[$(this).attr('name')] = $(this).val();
            });
            _data['from_date'] = start.format('YYYY/MM/DD');
            _data['end_date'] = end.format('YYYY/MM/DD');

            //console.log(_data);
            $.ajax({
                type: "POST",
                url:"?mod=report&act=card_ajax_data",
                data: _data,
                dataType: 'json',
                success: function(data){
                    var selectChart = echarts.init(document.getElementById('select_date_chart_main'));

                    var selectOption = option;
                    selectOption.xAxis[0].data = data.x;
                    selectOption.series[0].data = data.y.cost_total;
                    selectOption.series[1].data = data.y.selling_total;
                    if(data.x.length >= 10) {
                        selectOption.series[0].barWidth = 0;
                        selectOption.series[1].barWidth = 0;
                    } else {
                        selectOption.series[0].barWidth = 30;
                        selectOption.series[1].barWidth = 30;
                    }
                    selectChart.setOption(selectOption);
                }
            });
        });
    });
