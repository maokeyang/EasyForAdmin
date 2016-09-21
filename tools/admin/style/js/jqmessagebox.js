/*
formDate format
[
{'lable':'参数1','desc':'描述','type':'lable','data':''},
{'lable':'参数2','desc':'描述','type':'text','data':'',change:function(){},click:function(){}},
{'lable':'参数3','desc':'描述','type':'longtext','data':'',change:function(){},click:function(){}},
{'lable':'参数4','desc':'描述','type':'select','option':[[0,'第一个'],[1,'第二个']],'data':'',change:function(){},click:function(){}}
]

buttons format 
['text':'button1',click:function(){alert('');}]
*/
jQuery.extend({
	jQMessageBox:{
		type : null,
		x : null,
		y : null,
		width : null,
		height : null,
		title : null,
		content : null,
		formData : null,
		error:null,
		ok : null,
		cannel : null,
		buttons : null,
		onLoad : null,
		onOk : null,
		onCannel : null,
		build : function(op){
			this.type = op.type||2;
			this.x = op.x||"auto";
			this.y = op.y||"auto";
			this.width = op.width||"auto";
			this.height = op.height||"auto";
			this.title = op.title||'提示';
			this.content = op.content||'这是一个弹窗';
			this.formData = op.formData||null;
			this.error = op.error||'';
			this.ok = op.ok||'确定';
			this.cannel = op.cannel||'取消';
			this.buttons = op.buttons||[];
			this.onLoad = op.onLoad||function(){;};
			this.onOk = op.onOk||function(){return true;};
			this.onCannel = op.onCannel||function(){return true;};
			if(this.formData!=null){
				this.content = this.getForm(this.formData);
			}
			var self = this;
			var sw = $(document).width(); 
			var sh = $(document).height();
			this.width = this.width=="auto"?500+'px':this.width+'px';
			this.height = this.height=="auto"?"auto":this.height+'px';
			if(document.all){
				sw-=21;
				sh-=4;
			}
			$(document.body).append('<div id="jQMessageBoxBackGroup" style="background-color:#333333;position:absolute; left:'+0+'px; top:'+0+'px;width:'+sw+'px; height:'+sh+'px;z-index:3;display:none;"></div>');
			var box = '';
			box +='<div id="jQMessageBox" style="position:absolute; width:'+this.width+';height:'+this.height+'; background-color:#666666; border:1px solid #666666; z-index:4;display:none;">';
			box +='<div id="jQMessageBoxHead" style="background-color:#87CEEB;border-bottom:2px groove #4682B4; height:32px; position:relative;margin:5px 5px 0px 5px;">';
			box +='<div id="jQMessageBoxHeadTitle" style="padding-left:7px;padding-top:7px; font-size:14px;font-weight:bold;color:#FFFFFF;">'+this.title+'</div>';
			box +='<div id="jQMessageBoxHeadClose" style="background-color:#87CEEB; width:18px; font-size:1px; height:18px; position:absolute;right:3px;top:3px;">X</div></div>';
			box +='<div id="jQMessageBoxContent" style="margin:0px 5px 5px 5px;">';
			box +='<div id="jQMessageBoxContentText" style="background-color:#F5FFFA;padding:20px 10px;"></div>';
			box +='<div id="jQMessageBoxContentError" style="background-color:#FF3333;padding:5px 10px;display:none;"></div>';
			box +='<div id="jQMessageBoxContentButton" style="background-color:#EEEEEE;border-top:1px solid #CCCCCC;padding:5px;text-align:center;">';
			if(this.type==1){
				box +='<button id="jQMessageBoxContentButtonOk" style="width:70px; height:28px; margin:0px 10px;">'+this.ok+'</button>';
			}else if(this.type==2){
				box +='<button id="jQMessageBoxContentButtonOk" style="width:70px; height:28px; margin:0px 10px;">'+this.ok+'</button>';
				box +='<button id="jQMessageBoxContentButtonCannel" style="width:70px; height:28px; margin:0px 10px;">'+this.cannel+'</button>';
			}else if(this.type==3){
				for(var key=0;key<this.buttons.length;key++)
					box +='<button id="jQMessageBoxContentButton'+key+'" style="width:70px; height:28px; margin:0px 10px;">'+this.buttons[key].text+'</button>';
			}
			box +='</div></div></div>';
			$(document.body).append(box);
			if(this.formData!=null){
				this.setForm(this.formData);
			}else{
				this.setContent(this.content);
			}
			$("#jQMessageBoxHeadClose").click(function(){self.hidden();});
			$("#jQMessageBoxContentButtonCannel").click(function(){if(self.onCannel()===false)return;self.hidden();});
			$("#jQMessageBoxContentButtonOk").click(function(){
				var paramArr = self.getParamArr();
				if(self.onOk(paramArr)===false){self.showError();return;}self.hidden();
			});
			for(var key=0;key<this.buttons.length;key++)
				eval('$("#jQMessageBoxContentButton'+key+'").click(function(){var paramArr = self.getParamArr();if(self.buttons['+key+']["click"])if(self.buttons['+key+']["click"](paramArr)===false){self.showError();return;}self.hidden();});');
			this.onLoad();
			this.show();
		},
		show : function(){
			$("#jQMessageBoxBackGroup").fadeTo(0,0);
			$("#jQMessageBoxBackGroup").css('display','');
			$("#jQMessageBoxBackGroup").fadeTo('slow',0.30);
			this.x = this.x=="auto"?($(document).width()-$("#jQMessageBox").width())/2:this.x;
			this.y = this.y=="auto"?(window.screen.availHeight-$("#jQMessageBox").height())/2+parseInt($(document).scrollTop()-70):this.y;
			$("#jQMessageBox").css({left:this.x+"px",top:this.y+"px"});
			$("#jQMessageBox").fadeIn("fact");
		},
		hidden : function(){
			$("#jQMessageBoxBackGroup").fadeOut('fact',function(){$("#jQMessageBoxBackGroup").remove();});
			$("#jQMessageBox").fadeOut("fact",function(){$("#jQMessageBox").remove();});
		},
		showError:function(html){
			html = html||this.error;
			if(html==null||html==''){
				$("#jQMessageBoxContentError").css('display','none');
				return;
			}
			$("#jQMessageBoxContentError").html(html);
			$("#jQMessageBoxContentError").css('display','');
		},
		setContent:function(html){
			$("#jQMessageBoxContentText").html(html);
		},
		setForm:function(formData){
			$("#jQMessageBoxContentText").html(this.getForm(formData));
			if(this.formData!=null){		
				var jqobjArr = $("#jQMessageBoxContentText").find("input,select,textarea");
				for(var i=0;i<jqobjArr.length;i++){
					var j = jqobjArr.eq(i).attr('name');
					if(formData[j]['click'])
						jqobjArr.eq(i).click(formData[jqobjArr.eq(i).attr('name')]['click']);
					if(formData[j]['change'])
						jqobjArr.eq(i).change(formData[jqobjArr.eq(i).attr('name')]['change']);
				}
			}
		},
		getParamArr:function(){
			var paramArr = [];							 
			if(this.formData!=null){		
				var jqobjArr = $("#jQMessageBoxContentText").find("input,select,textarea");
				for(var i=0;i<jqobjArr.length;i++){
					paramArr.push(jqobjArr.eq(i).val());
				}
			}
			return paramArr;
		},
		getForm:function(formData){
			var from = '<table style=" border:2px #DDDDDD solid;border-spacing:1px;width:100%;background-color:#FFFFFF;font-size:12px;"><tbody>';
			for(var i=0;i<formData.length;i++){
				from +='<tr><td style="background-color:#EEEEEE;text-align:right; padding:3px 5px;">'+formData[i]['lable']+'</td><td style="padding:3px 5px;">';
				from +='<div style="color:#999999;display:block;margin:0 3px;">'+formData[i]['desc']+'</div>';
				if(formData[i]['type']=='label'){
					from +='<div style="margin:0 3px;">'+formData[i]['data']+'</div>';
				}else if(formData[i]['type']=='text'){
					from +='<div style="margin:0 3px;"><input id="jQMessageBoxParameter'+i+'" name="'+i+'" type="input" value="'+formData[i]['data']+'" style="width:200px;" /></div>';
				}else if(formData[i]['type']=='longtext'){
					from +='<div style="margin:0 3px;"><textarea id="jQMessageBoxParameter'+i+'" name="'+i+'" style=" width:200px;height:80px;font-size:12px;">'+formData[i]['data']+'</textarea></div>';
				}else if(formData[i]['type']=='button'){
					from +='<div style="margin:0 3px;"><input id="jQMessageBoxParameter'+i+'" name="'+i+'" type="button" value="'+formData[i]['data']+'" style="font-size:12px;"></div>';
				}else if(formData[i]['type']=='select'){
					from +='<div style="margin:0 3px;"><select id="jQMessageBoxParameter'+i+'" name="'+i+'" style="min-width:100px;">';
					for(var j=0;j<formData[i]['option'].length;j++){
						var selected = formData[i]['data']===formData[i]['option'][j][0]?'selected="selected"':'';
						from +='<option value="'+formData[i]['option'][j][0]+'" '+selected+'>'+formData[i]['option'][j][1]+'</option>';
					}
					from +='</select></div>';
				}
				from +='</td></tr>';
			}
			from += '</tbody></table>';
			return from;
		},
		getPart:function(part){
			switch (part){
				case "title":
				return $("#jQMessageBoxHeadTitle");
				case "content":
				return $("#jQMessageBoxContentText");
				case "button":
				return $("#jQMessageBoxContentButton");
			}
			return $("#jQMessageBox");
		},
		myAlert:function(message){
			this.build({type:1,width:400,title:'提示','content':message});
		},
		myConfirm:function(message,okFunc,cancelFunc){
			this.build({type:2,width:400,title:'提示','content':message,'onOk':okFunc||function(){},'onCannel':cancelFunc||function(){}});
		}
	}
});