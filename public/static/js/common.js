var TX = {};
var h;
TX.open = function(options){
	var opts = {};
	opts = $.extend(opts, {offset:'100px'}, options);
	return layer.open(opts);
}
TX.msg = function(msg, options, func){
	var opts = {};
	if(options){
		if(options.icon==1){
			options.icon='wst1';
		}else if(options.icon==2 || options.icon==5){
			options.icon='wst2';
		}else if(options.icon==3){
			options.icon='wst3';
		}
	}
	//有抖動的效果,第二位是函數
	if(typeof(options)!='function'){
		opts = $.extend(opts,{time:1000,shade: [0.4, '#000000'],offset: '200px'},options);
		return layer.msg(msg, opts, func);
	}else{
		return layer.msg(msg, options);
	}
}
TX.toJson = function(str){
	var json = {};
	try{
		if(typeof(str )=="object"){
			json = str;
		}else{
			json = eval("("+str+")");
		}
		if(json.status && json.status=='-999'){
			TX.msg('对不起，您已经退出系统！请重新登录',{icon:5},function(){
				if(window.parent){
					window.parent.location.reload();
				}else{
					location.reload();
				}
			});
		}else if(json.status && json.status=='-998'){
			TX.msg('对不起，您没有操作权限，请与管理员联系');
			return;
		}
	}catch(e){
		TX.msg("系统发生错误:"+e.getMessage,{icon:5});
		json = {};
	}
	return json;
}
Date.prototype.Format = function (fmt) { //author: meizz 
    var o = {
        "M+": this.getMonth() + 1, //月份 
        "d+": this.getDate(), //日 
        "h+": this.getHours(), //小时 
        "m+": this.getMinutes(), //分 
        "s+": this.getSeconds(), //秒 
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度 
        "S": this.getMilliseconds() //毫秒 
    };
    if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
    if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}
TX.upload = function(opts){
	var _opts = {};
	_opts = $.extend(_opts,{auto: true,swf:'/public/js/webuploader/Uploader.swf',server:'home/index/uploadPic'},opts);
	var uploader = WebUploader.create(_opts);
	uploader.on('uploadSuccess', function( file,response ) {
	    var json = WST.toJson(response._raw);
	    if(_opts.callback)_opts.callback(json,file);
	});
	uploader.on('uploadError', function( file ) {
		if(_opts.uploadError)_opts.uploadError();
	});
	uploader.on( 'uploadProgress', function( file, percentage ) {
		percentage = percentage.toFixed(2)*100;
		if(_opts.progress)_opts.progress(percentage);
	});
    return uploader;
}
TX.resize = function(){
	$(window).resize(function() {
		$("#wrapper").css("height",$("body").height() - 50 + "px");
		$("#iframe-box").css("height",$("body").height() - 90 + "px");
	});
	$("#iframe-box").css("height",$("body").height() - 90 + "px");
}
String.prototype.inArray = function(arr){
	if(arr.length == 0){
		return false;
	}
	if(arr.length == 1 && arr[0] == ''){
		return false;
	}
	for(var k=0;k<arr.length;k++){
		if(this == arr[k]){
			return true;
			break;
		}
	}
	return false;
}
TX.setPageBox = function(){
	var sh = $(window).height();
	$("#pd").css("top",sh-35);
}
TX.checkPower = function(arr){
	if(undefined == arr || arr.length == 0){
		return "";
	}
	var st = [];
	for(var i=0;i<arr.length;i++){
		if(arr[i]['select']){
			st.push(arr[i]['rolePower']);
		}
	}
	return st.join(",");
}
TX.intToDate = function(time){
    var dt = new Date(time*1000 + 8*3600*1000);
    return dt.toJSON().substr(0, 10);
}
TX.inItem = function(str,keys,arr){
	if(!str || arr.length == 0){
		return false;
	}
	for(var i=0;i<arr.length;i++){
		console.log(str);
		console.log(arr);
		if(keys.length > 0){
			if(str == arr[i][keys]){
				return true;
				break;
			}
		}else{
			if(str == arr[i]){
				return true;
				break;
			}
		}
		
	}
	return false;
}