var validator = {
    checkPhone:function(dt){
        if(!dt || dt.length == 0){
            return "请输入电话号码。";
        }
        if(! /^\d{7,20}$/.test(dt)){
            return "电话号码格式错误,应该都是数字";
        }
        return "";
    },
    checkMobile:function(dt){
        if(!dt || dt.length == 0){
            return "请输入手机号码。";
        }
        if(! /^1\d{10,10}$/.test(dt)){
            return "手机号码格式错误。";
        }
        return "";
    },
    checkPassword:function(dt,text){
        if(!dt || dt.length == 0){
            return "请输入" + text;
        }
        /*
        if(! /^[a-zA-Z]\w{5,17}$/.test(dt)){
            return text + "格式错误";
        }*/
        return "";
    },
    checkValidcode : function(dt){
        if(!dt || dt.length == 0){
            return "请输入验证码。";
        }
        return "";
    },
    /*验证手机验证码*/
    checkMobileCode : function(dt){
        if(!dt || dt.length == 0){
            return "请输入手机验证码";
        }
        if(! /^\d{6}$/.test(dt)){
            return "手机验证码格式不正确";
        }
        return "";
    },
    checkChinese:function(dt,min,max,text){
        if(!dt || dt.lenght == 0){
            return "请输入" + text;
        }
        if(! /^[\u4e00-\u9fa5]{1,}$/.test(dt)){
            return text + "格式错误,应该由汉字组成";
        }
        if(dt.length < min || dt.length > max){
            return text + "最少" + min + "个汉字,最多" + max +"个汉字。";
        }
        return "";
    },
    checkEmail:function(dt){
        if(!dt || dt.length == 0){
            return "请输入email";
        }
        if(! /^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/.test(dt)){
            return "email格式错误";
        }
        return "";
    },
    checkEmailWithPattern:function(dt){
        if(!dt || dt.length == 0){
            return "请输入email";
        }
        if(( /^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/.test(dt) ) || ( /^@(\w)+((\.\w+)+)$/.test(dt) ) ){
            return "";
        }
        return "email格式错误";
    },
    checkGender:function(dt){
        if(!dt || dt.length == 0){
            return "请选择性别";
        }
        if(dt != "male" && dt!= "female"){
            return "性别设置错误";
        }
        return "";
    },
    checkPostCode:function(dt){
        if(!dt || dt.length == 0){
            return "请输入邮政编码";
        }
        /*
        if(! /^[1-9]\d{5}$/.test(dt)){
            return "邮政编码格式错误";
        }*/
        return "";
    },
    checkText:function(dt,min,max,text){
        if(!dt || dt.length == 0){
            return "请输入" + text;
        }
        if(dt.length >= min && dt.length <= max){
            return "";
        }else{
            return text + "格式错误";
        }
    },
    checkNameEn:function(dt,text){
        if(!dt || dt.length == 0){
            return "请输入" + text;
        }
        if(! /^[A-Za-z]+$/.test(dt)){
            return text + "格式错误";
        }
        return "";
    },
    checkPassport:function(dt){
        if(!dt || dt.length == 0){
            return "请输入护照号码";
        }
        if(! /^(E|G|P)\d{8,8}$/.test(dt)){
            return "护照号码格式不正确";
        }
        return "";
    },
    checkDate:function(dt,text){
        if(!dt || dt.length == 0){
            return "请输入" + text;
        }
        if(! /^(\d{4})-(\d{2})-(\d{2})$/.test(dt)){
            return "请确认输入的" + text + "日期为yyyy-mm-dd格式";
        }else{
            var m = dt.substr(5,2);
            var d = dt.substr(9,2);
            if(parseFloat(m) > 12){
                return text + "中的月份不正确";
            }
            if(parseFloat(d) > 31){
                return text + "中的日期不正确";
            }
        }
        return "";
    },
    checkIdCard:function(dt){
        if(!dt || dt.length == 0){
            return "请输入身份证号码";
        }
        dt = dt.toUpperCase();
        if(! /(^\d{15}$)|(^\d{17}([0-9]|X)$)/.test(dt)){
            return "身份证格不正确";
        }
        return "";
    },
    checkCountry:function(dt){
        if(!dt || dt.length == 0){
            return "请输入去过的国家";
        }
        dt = dt.toUpperCase();
        if(! /^[\u4e00-\u9fa5]{2,8}(?:\s+[\u4e00-\u9fa5]{2,8})*$/.test(dt)){
            return "国家格不正确";
        }
        return "";
    },
    checkPostNumber:function(dt){
        if(!dt || dt.length == 0){
            return "请输入快递单号";
        }
        return "";
    },
    checkVisaNumber:function(dt){
        if(!dt || dt.length === 0){
            return "请输入签证号码"
        }
        return "";
    },
    checkNumber:function(dt,text){
        if( /^[0-9]$/.test(dt))
        {
            return "";
        }
        if(!dt){
            return "请输入" + text;
        }
        if(! /^[1-9]*[1-9][0-9]*$/.test(dt)){
            return text + "格式不正确";
        }
        return "";
    },
    checkUrl:function(dt,text){
        if(!dt || dt.length == 0){
            return "请输入" + text;
        }
        if(! /([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/.test(dt)){
            return text + "格式不正确";
        }
        return "";
    }
};