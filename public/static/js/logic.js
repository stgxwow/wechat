var logic_urls = {
    adminLoading:'/home/index/adminLoading',
    saveUser:'/home/index/saveUser',
    adminUsers:'/home/index/adminUsers',
    delUsers:'/home/index/delUsers',
    adminRole:'/home/index/adminRole',
    delRole:'/home/index/delRole',
    saveRole:'/home/index/saveRole',
    adminProgram:'/home/index/adminProgram',
    saveProgram:'/home/index/saveProgram',
    adminClass:'/home/index/adminClass',
    saveClass:'/home/index/saveClass',
    delClass:'/home/index/delClass',
    adminCourse:'/home/index/adminCourse',
    saveCourse:'/home/index/saveCourse',
    delCourse:'/home/index/delCourse',
    adminAward:'/home/index/adminAward',
    saveAward:'/home/index/saveAward',
    delAward:'/home/index/delAward',
    adminMenu:'/home/index/adminMenu',
    saveMenu:'/home/index/saveMenu',
    delMenu:'/home/index/delMenu',
    login:'/home/login/signin',
    adminStudent:'/home/index/adminStudent',
    saveStudent:'/home/index/saveStudent',
    delStudent:'/home/index/delStudent',
    adminGroup:'/home/index/adminGroup',
    saveGroup:'/home/index/saveGroup',
    delGroup:'/home/index/delGroup',
    adminMarkInput:'/home/index/adminMarkInput',
    saveMark:'/home/index/saveMark',
    searchStudent:'/home/index/searchStudent',
    listStudent:'/home/index/listStudent',
    getMarkByStudentId:'/home/index/getMarkByStudentId',
    getMarkListByClass:'/home/index/getMarkListByClass',
    adminMark:'/home/index/adminMark',
    adminScoreRole:'/home/index/adminScoreRole',
    saveScoreRole:'/home/index/saveScoreRole',
    delScoreRole:'/home/index/delScoreRole',
    adminRoleInput:'/home/index/adminRoleInput',
    saveRoleInput:'/home/index/saveRoleInput',
    delRoleInput:'/home/index/delRoleInput',
    adminScore:'/home/index/adminScore',
    adminScoreInput:'/home/index/adminScoreInput',
    getScoreListByClass:'/home/index/getScoreListByClass',
    getScoreListBystudent:'/home/index/getScoreListBystudent',
    getRuleByName:'/home/index/getRuleByName',
    getScoreById:'/home/index/getScoreById',
    editPwd:'/home/index/editPwd',
    signOut:'/home/login/signOut',
    adminMain:'/home/index/adminMain',
    exportMarkByClass:'/home/index/exportMarkByClass',
    checkStudentNumber:'/home/index/checkStudentNumber',
    examinedScoreInput:'/home/index/examinedScoreInput',
    getEXScoreList:'/home/index/getEXScoreList',
    enterScoreEdit:'/home/index/enterScoreEdit',
    denyScoreEdit:'/home/index/denyScoreEdit',
    changeMsgInvi:'/home/index/changeMsgInvi',
    sendMsg:'/home/index/sendMsg',
    getMsgListByUser:'/home/index/getMsgListByUser',
    importStudent:'/home/index/importStudent',
    exportStudent:'/home/index/exportStudent',
    importMark:'/home/index/importMark',
    adminLogs:'/home/index/adminLogs',
    getMsgNum:'/home/index/getMsgNum',
    importScoreInput:'/home/index/importScoreInput',
    exportScoreInput:'/home/index/exportScoreInput',
    adminShowWorks:'/home/index/adminShowWorks',
    saveWorks:'/home/index/saveWorks',
    delWorks:'/home/index/delWorks',
    uploadMultipleFiles:'/home/index/uploadMultipleFiles',
    saveShowWorks:'/home/index/saveShowWorks',
    delShowWorks:'/home/index/delShowWorks',
    getTrueUrl:'/home/index/getTrueUrl',
    adminShowGroup:'/home/index/adminShowGroup',
    saveShowGroup:'/home/index/saveShowGroup',
    delShowGroup:'/home/index/delShowGroup',
    importScoreRule:'/home/index/importScoreRule',
    exportScoreRule:'/home/index/exportScoreRule',
};

function QueryString(key){
    var a = window.location.search.substr(1).split('&')
    if (a == "") return "";
    var b = {};
    for (var i = 0; i < a.length; ++i)
    {
        var p=a[i].split('=');
        if (p.length != 2) continue;
        b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
    }
    if(b[key])
        return b[key];
    else
        return "";
};

var logic_user = {
	cookie_name:"lgoic_user",
    userId:0,
	userName:'',
    nickName:'',
    proId:0,
    proName:"",
    classList:"",
    roleId:0,
    userRole:0,
    dataFlage:1,
    roleName:'',
    rolePower:'',
    powerArr:[],
    classArr:[],
	is_sign_in:false,
	read:function(){
        var cookie_data = $.cookie(this.cookie_name);
        if(cookie_data){
            cookie_data = JSON.parse(cookie_data);
            //if(cookie_data.mobile)
                for(var k in cookie_data)
                    this[k] = cookie_data[k];
        }
    },
    save:function(){
        var out = {};
        for(var k in this)
            if(!jQuery.isFunction(this[k]))
                out[k] = this[k];
        var save_string = JSON.stringify(out);

        $.cookie(this.cookie_name,save_string,{path:"/"});
        console.log($.cookie(this.cookie_name));
    },
    onSignin:function(dt){
        for(var k in dt){
            this[k] = dt[k];
        }
        this.save();
    },
	signout:function(signout_ok,signout_error){
		this["is_signin"] = false;
		this.save();
   },
}
Date.prototype.format = function(fmt) { 
     var o = { 
        "M+" : this.getMonth()+1,                 //月份 
        "d+" : this.getDate(),                    //日 
        "h+" : this.getHours(),                   //小时 
        "m+" : this.getMinutes(),                 //分 
        "s+" : this.getSeconds(),                 //秒 
        "q+" : Math.floor((this.getMonth()+3)/3), //季度 
        "S"  : this.getMilliseconds()             //毫秒 
    }; 
    if(/(y+)/.test(fmt)) {
            fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length)); 
    }
     for(var k in o) {
        if(new RegExp("("+ k +")").test(fmt)){
             fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
         }
     }
    return fmt; 
}
