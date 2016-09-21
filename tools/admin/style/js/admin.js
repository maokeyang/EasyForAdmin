/**
 * datepick相关功能
 */
function change_timezone(sel) {
    var val = sel.options[sel.selectedIndex].value;
    // 游戏语言版本时区
    var timezone = new Array();
    timezone["zh_CN"] = "Asia/Shanghai"; // 简体中文
    timezone["zh_TW"] = "Asia/Taipei"; // 繁体中文
    timezone["en_US"] = "America/New_York"; // 英语
    timezone["ko_KR"] = "Asia/Seoul"; // 韩语
    timezone["ja_JP"] = "Asia/Tokyo"; // 日语
    timezone["vi"] = "Asia/Ho_Chi_Minh"; // 越语
    timezone["th"] = "Asia/Bangkok"; // 泰语
    $('#timezone').val(timezone[val]);
}

// 游戏配置
function change_game_config(sel) {
    $('#slave_gateway_role_num').val(5000); // 从网关5000人
    switch($(sel).val()){
        case '1':
            // [中] 数据库100，日志20，网关3，
            $('#db_conn').val(100);
            $('#log_db_conn').val(20);
            $('#slave_gateway_num').val(3);
            $('#master_gateway_role_num').val(1);
            break;
        case '2':
            // [高] 数据库200，日志20，网关6，
            $('#db_conn').val(200);
            $('#log_db_conn').val(20);
            $('#slave_gateway_num').val(6);
            $('#master_gateway_role_num').val(1);
            break;
        default: // 默认为低配置
            // [低] 数据库连接50，日志连接10，网关0，主网关人数2000
            $('#db_conn').val(50);
            $('#log_db_conn').val(10);
            $('#slave_gateway_num').val(0);
            $('#slave_gateway_role_num').val(0);
            $('#master_gateway_role_num').val(2000);
            //$('#slave_gateway_num').val(1);
            //$('#master_gateway_role_num').val(1);
    }
}

var open = function(url){
    var retureValue, width = 900, height = 500;
    if (window.showModalDialog!=null) {//IE判断
        retureValue = window.showModalDialog(url, window, "dialogWidth=" + width + "px;dialogHeight=" + height + "px;resizable:yes;")
    } else {
        retureValue = window.open(url, window, "dialogWidth=" + width + "px;dialogHeight=" + height + "px;resizable:yes;")
    }
    if (typeof(result) == 'undefined') {
        retureValue = window.retureValue;
    }
    return retureValue;
}

function openServerDlg(in_vsn_id,in_opents_id,out_svr_id){
	if(!in_vsn_id){
		in_vsn_id='from_vsn';
	}
	if(!out_svr_id){
		out_svr_id='servers';
	}
    vsn = $('#'+in_vsn_id).val();
    if(!vsn){
        alert("请先输入当前版本");
        return;
    }
    url = "?mod=update&act=server_dlg&is_sign=1&version="+vsn;
	if(in_opents_id){
		url +='&opents='+$('#'+in_opents_id).val();	
	}
    var retureValue = open(url);
    $("#"+out_svr_id).val(retureValue);
    return retureValue;    
}

$(function(){
    //主菜单折叠
    $('#ul_menu').menu(true);
    /*
    //自动应用GridView插件
    $('.grid').each(function(index, el){
        GridView.init(el);
    });
    */

    //范围日期选择必须使用'kwDateBegin'和'kwDateEnd'这两个ID
    if($('#kwDateBegin').size() && $('#kwDateEnd').size()){
        $('#kwDateBegin, #kwDateEnd').datetimepicker({
            minView: "month", //选择日期后，不会再跳转去选择时分秒 
            format: "yyyy-mm-dd", //选择日期后，文本框显示的日期格式 
            language: 'zh-CN', //汉化 
            autoclose:true //选择日期后自动关闭 
        }); 
    }
});

/**
 * 生成折叠菜单
 * @param bool hl 是否高亮当前菜单
 */
$.fn.menu = function(hl){
    return this.each(function(){
        $(this).find('ul').each(function(){
//            $(this).hide();
            if(hl){ //高亮菜单项
                var expandParent = function(el){
                    $(el).show();
                    var p = $(el).parent().parent();
                    if( 'UL' == $(p).attr('tagName') && 'menu' != $(p).attr('class')){
                        expandParent(p);
                    }
                };
                var box = this;
                $(this).children('li').children('a').each(function(){
                    var link = $(this).attr('href');
                    if(-1 == link.indexOf('?')) return;
                    link = '\\' + link.substr(link.indexOf('?'));
                    var re = new RegExp(link, 'i');
                    if(document.location.search.match(re)){
                        $(this).css('font-weight', 'bold');
                        $(this).css('color', '#000000');
                        $(this).parent().parent().parent().addClass('active');
                        $(this).parent().parent().parent().siblings().removeClass('active');
                        expandParent(box); 
                    }else{
                        /*if(-1 == link.indexOf(',')){
                            var re = new RegExp(link.substr(0, link.indexOf('&')));
                        }else{
                            var re = new RegExp(link.substr(0, link.indexOf(',')));
                        }
                        if(document.location.search.match(re)){
                            expandParent(box); 
                        }*/
                    }
                });
            }
            /*$(this).parent().children('a').click(function(){
                $(this).parent().children('ul').DropToggleDown(300)
                $(this).parent().parent().find('ul').fadeIn("slow").hide();
                $(this).parent().children('ul').toggle();
            });*/
        });
    });
}

/**
 * 弹出居中窗口
 * @param string src 窗口内容地址
 * @param int w 宽度
 * @param int h 高度
 * @param bool resizable 是否允许改变大小和出现滚动条
 * @param string name 窗口名称
 * @return null
 */
var winOpen = function(src, w, h, name, r){
    var w = w? w : 600;
    var h = h? h : 480;
    var l = (screen.width - w) / 2;
    var t = (screen.height - h) / 2;
    var r = r ? 'resizable=yes,scrollbars=yes' : 'resizable=no,scrollbars=no';
    name = name ? name : '_blank';
    var win = window.open(src, name, 'width=' + w + ',height=' + h + ',top=' + t + ',left=' + l + ',' + r);
    win.focus()
    return win;
}

//数字<10加 "0" 函数(避免时间显示为8点1分3秒)
function fillZero(v){
    if(v<10){
        v='0'+v;
    }
    return v;
}

// showTime('localtime12',0,12);
// showTime('localtime24',0,0);
function showTime(ev,date,type){
    var d;
    var Y,M,D,W,H,I,S;
    var Week=['星期天','星期一','星期二','星期三','星期四','星期五','星期六'];
    if(date){
        d =new Date(date*1000);
    }else{
        d =new Date();
    }
    Y = d.getFullYear();;
    M = fillZero(d.getMonth()+1);
    D = fillZero(d.getDate());
    W = Week[d.getDay()];
    H = fillZero(d.getHours());
    I = fillZero(d.getMinutes());
    S = fillZero(d.getSeconds());
    var showData = '今天是'+Y+'年'+M+'月'+D+'日'+' '+H+'点'+I+'分'+S+'秒'+' '+W;
    $('#'+ev).html(showData);
    if(date){
        date++;
    }
    setTimeout(function(){showTime(ev,date,type)},1000);
}

function _online(num){
    _online.total = _online.total + Number(num)
    return (_online.total)
}

function startLoading(div_id){
	if(!div_id){
		div_id='div_loading';
	}
	$('#'+div_id).html('<img src="images/loading.gif" />');
	$('#'+div_id).show();
}
function endLoading(div_id){
	if(!div_id){
		div_id='div_loading';
	}
	$('#'+div_id).html('');
	$('#'+div_id).hide();
}

/**
 * 判断元素是否在数组中存在
 * @param obj 数组对象
 * @param val 给定元素
 */
function array_index_off(obj,val){
	for (var i = 0; i < obj.length; i++) {  
		if (obj[i] == val) {  
			return i;  
		}  
	}  
	return -1; 
}

/**
 * 全选按钮
 * @param obj 复选框对象 checkbox input name must be "boxIds"
 */
function check_all(obj){
	var checkboxs = document.getElementsByName('boxIds');
	for(var i = 0;i<checkboxs.length;i++){
		checkboxs[i].checked = obj.checked;
		//判断元素是否存在数组中
		var index = array_index_off(ids,checkboxs[i].value);
		if(checkboxs[i].checked){
			//添加数组元素
			if(index <= -1){
				ids.push(checkboxs[i].value);
			}
		}else{
			//删除数组元素
			if(index > -1){
				ids.splice(index,1);
			}
		}
		
	}
}

/**
 * 反选按钮
 * @param obj 复选框对象 checkbox input name must be "boxIds"
 */
function check_reverse(obj){
	var checkboxs = document.getElementsByName('boxIds');
	for(var i = 0;i<checkboxs.length;i++){
		checkboxs[i].checked = !checkboxs[i].checked;
		//判断元素是否存在数组中
		var index = array_index_off(ids,checkboxs[i].value);
		if(checkboxs[i].checked){
			//添加数组元素
			if(index <= -1){
				ids.push(checkboxs[i].value);
			}
		}else{
			//删除数组元素
			if(index > -1){
				ids.splice(index,1);
			}
		}
		
	}
}

/**
 * 点击复选框
 * @param obj 复选框对象
 * @param id 复选框值
 */
function check_select(obj,id){
	//判断元素是否存在数组中
	var index = array_index_off(ids,id);
	if(obj.checked){
		//添加数组元素
		if(index <= -1){
			ids.push(id);
		}
	}else{
		//删除数组元素
		if(index > -1){
			ids.splice(index,1);
		}
	}
}

function main_form_submit(){
    $('#mainform').submit();
}

function page(id, sort_id, sort_name){
    $('#'+id).DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "hover":true,
        "autoWidth": true,
        "lengthMenu": [sort_id,sort_name]
    });
}

// --- 联动函数 --
// 获取开发商
function getDeveloper(_callback){
    var _data = {};
    $(".form-control.pull-right").each(function(){
        _data[$(this).attr('name')] = $(this).val();
    });
    $.ajax({
        type: "post",
        url: '?act=common&mod=common&node=get_all_developer',
        dataType:'text',
        contentType: "application/x-www-form-urlencoded; charset=utf-8",   
        data:_data,
        error : function(XMLHttpRequest, textStatus, errorThrown){
            alert(errorThrown);
        },
        success: function(data, textStatus, XMLHttpRequest){
            $('#developer_code').html(data);
            if(typeof _callback == 'function'){
                _callback();
            }
        }
    });
}

//获取一级游戏
function getParentGame(_callback){
    var _data = {};
    $(".form-control.pull-right").each(function(){
        _data[$(this).attr('name')] = $(this).val();
    });
    $.ajax({
        type: "post",
        url: '?act=common&mod=common&node=get_parent_game',
        dataType:'text',
        contentType: "application/x-www-form-urlencoded; charset=utf-8",   
        data:_data,
        error : function(XMLHttpRequest, textStatus, errorThrown){
            alert(errorThrown);
        },
        success: function(data, textStatus, XMLHttpRequest){
            $('#parent_game_id').html(data);
            if(typeof _callback == 'function'){
                _callback();
            }
        }
    });
}

//获取二级游戏
function getGame(_callback){
    var _data = {};
    $(".form-control.pull-right").each(function(){
        _data[$(this).attr('name')] = $(this).val();
    });
    $.ajax({
        type: "post",
        url: '?act=common&mod=common&node=get_child_game',
        dataType:'text',
        contentType: "application/x-www-form-urlencoded; charset=utf-8",   
        data:_data,
        error : function(XMLHttpRequest, textStatus, errorThrown){
            alert(errorThrown);
        },
        success: function(data, textStatus, XMLHttpRequest){
            //alert($.toJSON(data));
            $('#game_code').html(data);
            if(typeof _callback == 'function'){
                _callback();
            }
        }
    });
}

//获取渠道
function getGameChannel(_callback){
    var _data = {};
    $(".form-control.pull-right").each(function(){
        _data[$(this).attr('name')] = $(this).val();
    });
    $.ajax({
        type: "post",
        url: '?act=common&mod=common&node=get_game_channel',
        dataType:'text',
        contentType: "application/x-www-form-urlencoded; charset=utf-8",   
        data:_data,
        error : function(XMLHttpRequest, textStatus, errorThrown){
            alert(errorThrown);
        },
        success: function(data, textStatus, XMLHttpRequest){
            $('#channel_code').html(data);
            if(typeof _callback == 'function'){
                _callback();
            }
        }
    });
}

function add_param(field){
    var _key = arguments[1] ? arguments[1] : '';
    var _value = arguments[2] ? arguments[2] : '';

    var _arr_html = '<div class="form-group"><div class="col-sm-5"><input type="text" placeholder="参数" value="'+_key+'" name="param_keys['+field+'][]" class="form-control" id="text_sdk_name"><span class="txt-impt"></span></div>';
    _arr_html += '<div style="float:left;padding: 6px 0px;">--</div>';
    _arr_html += '<div class="col-sm-6"><input type="text" placeholder="描述" value="'+_value+'" name="param_values['+field+'][]" class="form-control" id="text_sdk_name"><span class="txt-impt"></span></div>';
    _arr_html += '<div class="box-tools pull-right"> <button type="button" class="btn btn-box-tool" onclick="remove_param($(this));"><i class="fa fa-times"></i></button> </div></div></div>';
    $('#div_'+field).append(_arr_html);	
}

function remove_param(obj){
    obj.parent().parent().remove();
}

function trim(str) { 
    return str.replace(/(^\s*)|(\s*$)/g, ""); 
}

// 通用获取数据接口
function getTableData(_url, _page, _callback){
    var _limit= $('#sel_limit').val();

    var _order_by = '';
    var _sorting = '';
    if($('th.sorting_asc').attr('order_by')){
        _order_by = $('th.sorting_asc').attr('order_by');
        _sorting = 'ASC';
    }else if($('th.sorting_desc').attr('order_by')){
        _order_by = $('th.sorting_desc').attr('order_by');
        _sorting = 'DESC';
    }

    var _data = {
        page:_page,
        limit:_limit,
        order_by:_order_by,
        sorting:_sorting,
        is_ajax:1
    };
    $(".form-control.pull-right").each(function(){
        _data[$(this).attr('name')] = $(this).val();
    });
    $.ajax({
        type: "post",
        url: _url,
        dataType:'text',
        contentType: "application/x-www-form-urlencoded; charset=utf-8",   
        data:_data,
        async: false,
        beforeSend: function(XMLHttpRequest){
            $('#div_loading').show();
            $('#div_sub').hide();
        },
        complete: function(XMLHttpRequest, flagOK){
            $('#div_loading').hide();
            $('#div_sub').show();
        },
        error : function(XMLHttpRequest, textStatus, errorThrown){
            alert(errorThrown);
        },
        success: function(data, textStatus, XMLHttpRequest){
            //console.log(data);
            $('#div_sub').html(data);
            if(typeof _callback == 'function'){
                _callback();
            }
            search_table();
        }
    });
}

// 基于table的内容搜索
function search_table(){
    $('#ipt_search_table').keyup(function(){
        var _is_found = false;
        var _tr_no_found = '<tr id="tr_no_found" class="odd"><td valign="top" colspan="7" class="dataTables_empty">没有找到相关数据</td></tr>';
        var _text = trim($(this).val());
        if($('#tr_no_found').length > 0){
            $('#tr_no_found').remove();
        }
        $('#example1 > tbody > tr').each(function(){
            var _tr_text = $(this).text();
            if(_tr_text.toLowerCase().indexOf(_text.toLowerCase()) > -1){
                $(this).show();
                _is_found = true;
            }else{
                $(this).hide();
            }
        });
        if(!_is_found){
            $('#example1 > tbody').append(_tr_no_found);
        }
    });
}
