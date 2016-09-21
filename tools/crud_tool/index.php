<?php 
define('SESSION_DISABLE',true);
require 'db.php';

$db_cnf = require 'config.php';

extract($_REQUEST);

$tittle = '增删改查模版生成工具';

$dataname = $db_cnf['h5']['dbname'];

$db = new Mysql_pdo($db_cnf['h5']);

$tables = $db->getAll("SHOW TABLES");

if(!isset($table)){
	$table = $tables[0]['Tables_in_'.$dataname];
}

//d($_REQUEST);
if(isset($table) && !empty($table) && isset($submit)){
	if(!$describe){
		//exit('请输入描述');
	}
	$file_name=$table;
	if(isset($is_short) && !empty($is_short)){
		$file_name=preg_replace('/[^_]+_/','',$table,1);
	}
	
	$mod=$mod?$mod:$table;
	
	$key_field_arr = $db->getAll("SHOW INDEX FROM $table WHERE Key_name = 'PRIMARY'");
	$key_field = $autoid_field = $key_field_arr[0]['Column_name'];
	
    if(isset($is_ajax)){
        ob_start();
        include "crud_tpl/ajax_list_tpl.tpl.htm";
        $contents= ob_get_contents();		
        ob_end_clean();	
        $contents=str_replace('%%?php','<?php',$contents);
        $contents=str_replace('?%%','?>',$contents);
        file_put_contents('crud_output/'.$file_name.".tpl.htm",$contents);

        ob_start();
        include "crud_tpl/ajax_sub_list_tpl.tpl.htm";
        $contents= ob_get_contents();		
        ob_end_clean();	
        $contents=str_replace('%%?php','<?php',$contents);
        $contents=str_replace('?%%','?>',$contents);
        file_put_contents('crud_output/sub_'.$file_name.".tpl.htm",$contents);

        ob_start();
        include "crud_tpl/ajax_list_tpl.act.php";
        $contents= ob_get_contents();		
        ob_end_clean();	
        $contents=str_replace('%%?php','<?php',$contents);
        $contents=str_replace('?%%','?>',$contents);
        file_put_contents('crud_output/'.$file_name.".act.php",$contents);
    }else{
        ob_start();
        include "crud_tpl/list_tpl.tpl.htm";
        $contents= ob_get_contents();		
        ob_end_clean();	
        $contents=str_replace('%%?php','<?php',$contents);
        $contents=str_replace('?%%','?>',$contents);
        file_put_contents('crud_output/'.$file_name.".tpl.htm",$contents);

        ob_start();
        include "crud_tpl/list_tpl.act.php";
        $contents= ob_get_contents();		
        ob_end_clean();	
        $contents=str_replace('%%?php','<?php',$contents);
        $contents=str_replace('?%%','?>',$contents);
        file_put_contents('crud_output/'.$file_name.".act.php",$contents);
    }
    
    if(empty($is_readonly)){
        ob_start();
	include "crud_tpl/edit_tpl.tpl.htm";
	$contents= ob_get_contents();		
	ob_end_clean();	
	$contents=str_replace('%%?php','<?php',$contents);
	$contents=str_replace('?%%','?>',$contents);
	file_put_contents('crud_output/'.$file_name."_edit.tpl.htm",$contents);
	
	ob_start();
	include "crud_tpl/edit_tpl.act.php";
	$contents= ob_get_contents();		
	ob_end_clean();	
	$contents=str_replace('%%?php','<?php',$contents);
	$contents=str_replace('?%%','?>',$contents);
	file_put_contents('crud_output/'.$file_name."_edit.act.php",$contents);
	
	ob_start();
	include "crud_tpl/delete_tpl.act.php";
	$contents= ob_get_contents();		
	ob_end_clean();	
	$contents=str_replace('%%?php','<?php',$contents);
	$contents=str_replace('?%%','?>',$contents);
	file_put_contents('crud_output/'.$file_name."_delete.act.php",$contents);
    }
	
	
	?>
	<script>alert('生成成功');</script>
<?php 
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $tittle;?></title>
<style type="text/css">
    h1{font:bold 16px/28px arial,'宋体'; color:#333; background:#f4f4f4; margin:0 0 20px 0; padding:0;}
    body{font:12px/20px arial,'宋体';text-align:center;}
	form{font:12px/20px arial,'宋体';text-align:left; width:100%;}
	#main{width:1000px;margin:20px auto auto auto;}
	.floatright{float:right;}
	ul{list-style:none;margin: 0px;padding: 30px; width:100%;}
	ul li{float:left;display:inline;}
</style>
<script src="js/jquery.js"></script>
</head>
<body>

<div id="main" class="wrapper">
	<h1><?php echo $tittle;?></h1>
	<h4><a href="crud_output" target="_blank">进入模版目录</a></h4>
	<form method="post">
		<input name="game" type="hidden" value="qsqy" />
		<ul>
			<li>表名</li>
			<li class="floatright">
				<select id="sel_db" name="database" onChange="jump_d(this);">
					<?php foreach($db_cnf as $k => $v){
					?>
						
					 <option value="<?php echo $k;?>" ><?php echo $v['dbname'];?></option>
					
					<?php }?>
				</select>
				<script>
					document.getElementById('sel_db').value='<?php echo isset($database)?$database:''?>';
				</script>
				<select id="sel_table" name="table" onChange="jump_t(this);">
					<?php foreach($tables as  $v){?>
						
					 <option value="<?php echo $v['Tables_in_'.$dataname];?>" ><?php echo $v['Tables_in_'.$dataname];?></option>
					
					<?php }?>
				</select>
				<script>
					document.getElementById('sel_table').value='<?php echo isset($table)?$table:''?>';
				</script>
			</li>
		</ul>
		<ul>
			<li> 作者</li>
			<li class="floatright">
				<input type="text" name="author" value="<?php echo isset($author)?$author:'';?>"/>
			</li>
		</ul>
		<ul>
			<li> 模块</li>
			<li class="floatright">
				<input type="text" name="mod" value="<?php echo isset($mod)?$mod:'';?>"/>
			</li>
		</ul>
		<ul>
			<li> 标题</li>
			<li class="floatright">
                <?php $table_status = $db->getOne("SHOW TABLE STATUS LIKE '{$table}'");?>
				<input type="text" name="describe" value="<?php echo isset($describe)?$describe:$table_status['Comment'];?>"/>
			</li>
		</ul>
        <ul>
			<li> 异步模式</li>
			<li class="floatright">
				<input type="checkbox" name="is_ajax" <?php echo isset($is_ajax)?'checked':'';?>/>
			</li>
		</ul>
        <ul>
			<li> 进入后立刻异步查询</li>
			<li class="floatright">
				<input type="checkbox" name="is_ajax_now" <?php echo isset($is_ajax_now)?'checked':'';?>/>
			</li>
		</ul>
                <ul>
			<li> 只读模式</li>
			<li class="floatright">
				<input type="checkbox" name="is_readonly" <?php echo isset($is_readonly)?'checked':'';?>/>
			</li>
		</ul>
		<ul>
			<li> 排序</li>
			<li class="floatright">
				ORDER BY <input type="text" name="order_by" value="<?php echo isset($order_by)?$order_by:'';?>"/>
                <select id="sel_order_type" name="order_type">
                    <option value="ASC">ASC</option>
                    <option value="DESC">DESC</option>
                </select>
                <script>
<?php
if(isset($order_type)){
?>
    $('#sel_order_type').val('<?php echo $order_type;?>');
<?php } ?>
                </script>
			</li>
        </ul>
        <ul>
            <hr/>
        </ul>
		
		<?php if(isset($table) && !empty($table)){
			$table_struct = $db->getAll("SHOW FULL FIELDS FROM $table");
			foreach($table_struct as $k=> $v){?>
			<ul>
				<li><input type="text" style="width:100px;" name="Comment[<?php echo $v['Field']?>]" value="<?php echo isset($Comment[$v['Field']])?$Comment[$v['Field']]:$v['Comment']?>"/>&nbsp;<?php echo $v['Field']?></li>
				<li class="floatright">
				
				<input id="nullable_<?php echo $k;?>" type="checkbox" <?php echo isset($nullable[$v['Field']])?'checked':''?> name="nullable[<?php echo $v['Field']?>]" value="<?php echo $v['Field']?>"/>
				<label for="nullable_<?php echo $k;?>">非空</label>			
				<select id="sel_input_<?php echo $v['Field']?>" name="input[<?php echo $v['Field']?>]">
					<option value="text">text</option>
					<option value="noinput">noinput</option>
					<option value="hidden">hidden</option>
					<option value="select">select</option>
					<option value="checkbox">checkbox</option>
					<option value="textarea">textarea</option>
				</select>
				<script>
					document.getElementById('sel_input_<?php echo $v['Field']?>').value='<?php echo isset($input[$v['Field']])?$input[$v['Field']]:'text'?>';
				</script>
				
				<input id="Field_<?php echo $k;?>" checkall="checkall" type="checkbox" <?php echo isset($Field[$v['Field']])?'checked':''?> name="Field[<?php echo $v['Field']?>]" search="equal" value="<?php echo $v['Field']?>"/>
				
				<label for="Field_<?php echo $k;?>">精确搜索</label>

                <input id="Like_Field_<?php echo $k;?>" checkall="checkall" type="checkbox" <?php echo isset($Like_Field[$v['Field']])?'checked':''?> name="Like_Field[<?php echo $v['Field']?>]" search="like" value="<?php echo $v['Field']?>"/>
				
				<label for="Like_Field_<?php echo $k;?>">模糊搜索</label>
				
				<input id="list_<?php echo $k;?>" checkall="checkall" type="checkbox" <?php echo isset($list[$v['Field']])?'checked':''?> name="list[<?php echo $v['Field']?>]" value="<?php echo $v['Field']?>"/>
				<label for="list_<?php echo $k;?>">列表</label>
				
				<input id="is_time_<?php echo $k;?>" type="checkbox" <?php echo isset($is_time[$v['Field']])?'checked':''?> name="is_time[<?php echo $v['Field']?>]" value="<?php echo $v['Field']?>"/>
				<label for="is_time_<?php echo $k;?>">时间区间</label>
				
				<input id="from_unix_time_<?php echo $k;?>" type="checkbox" <?php echo isset($from_unix_time[$v['Field']])?'checked':''?> name="from_unix_time[<?php echo $v['Field']?>]" value="<?php echo $v['Field']?>"/>
				<label for="from_unix_time_<?php echo $k;?>">unix时间</label>
				
				<label for="code2name_<?php echo $k;?>"> NameTable</label>
				<input style="width:70px;" name="name_table[<?php echo $v['Field']?>]" value="<?php echo isset($name_table[$v['Field']])?$name_table[$v['Field']]:'';?>"/> 

				<input type="button" value="数组" onClick="add_arr('<?php echo $v['Field']?>');"/>
				<br/>
                <div id="div_arr_<?php echo $v['Field']?>">
<?php 
                if(isset($type_arr_key[$v['Field']])){
                    foreach($type_arr_key[$v['Field']] as $tk => $tv){?>
                        <div flag="<?php echo $v['Field'];?>_<?php echo $tk;?>"><input type="text" value="<?php echo $tv;?>" name="type_arr_key[<?php echo $v['Field'];?>][<?php echo $tk;?>]"/>
                        &nbsp;&nbsp;&nbsp;=>&nbsp;&nbsp;&nbsp;<input type="text" value="<?php echo $type_arr_value[$v['Field']][$tk];?>" name="type_arr_value[<?php echo $v['Field'];?>][<?php echo $tk;?>]"/>
                        <input type="button" value="删除" onClick="delet_arr('<?php echo $v['Field'];?>','<?php echo $tk;?>');"/></div>
                <?php    } } ?>
                </div>

				<!-- <br/><label>类型数组</label><input style="width:440px;" name="type_arr[<?php echo $v['Field']?>]" /> -->
				
				</li>
			</ul>	
			<?php }
		}?>
		<ul>
			<li>
				<input type="button" onClick="checkall()" value="全选"/>
				<input type="button" onClick="uncheckall()" value="反选"/>
			</li>
			<li class="floatright"><input type="submit" name="submit" value="生成"/></li>
		</ul>
	</form>
</div>
<script>
	var fields_index={
<?php foreach($table_struct as $k=> $v){
    if(isset($type_arr_key[$v['Field']])){ ?>
        "<?php echo $v['Field']?>":<?php echo 1 + max(array_keys($type_arr_key[$v['Field']]));?>,
<?php }else{?>
        "<?php echo $v['Field']?>":0,
<?php }}?>	
	};

	function checkall(){
		$('input[checkall=checkall]').attr('checked',true);
	}
	function uncheckall(){
		$('input[checkall=checkall]').attr('checked',false);
	}
	function jump_d(obj){
		var url = window.location.href;
		var str = url.split('?');
		var jump_url = str[0]+'?database='+obj.value;
		window.location.href = jump_url;
	}
	function jump_t(obj){
		var url = window.location.href;
		var str = url.split('?');
		var db = document.getElementById('sel_db').value;
		var jump_url = str[0]+'?database='+db+'&table='+obj.value;
		window.location.href = jump_url;
	}
	function add_arr(field){
		var _index = fields_index[field];
		var _arr_html = '<div flag="'+field+'_'+_index+'">&nbsp;&nbsp;&nbsp;<input type="text" value="" name="type_arr_key['+field+']['+_index+']"/>';
		_arr_html += '&nbsp;&nbsp;&nbsp;=>&nbsp;&nbsp;&nbsp;<input type="text" value="" name="type_arr_value['+field+']['+_index+']"/>';
		_arr_html += '<input type="button" value="删除" onclick="delet_arr(\''+field+'\','+_index+');"/></div>';
		$('#div_arr_'+field).append(_arr_html);	
		fields_index[field]++;
	}
	function delet_arr(field, _index){
		$('div[flag='+field+'_'+_index+']').remove();
	}
</script>
</body>
</html>
