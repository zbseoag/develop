

(function(factory) {
    (typeof define === "function" && define.amd)? define( ["jquery"], factory ): factory(jQuery);
}(function($){
    
    
$.demo = function(options, func){

};

$selector.validate(this.forms[key]);

$.extend($.fn, {
    
validation: function(options, func){
    
    //初始化配置
    var settings = {
        errorElement: "i",
        errorClass:"glyphicon glyphicon-remove",
        focusInvalid:false,
        onkeyup:false,//不在输入时验证
        errorPlacement:function(label, element){
            validator.defaults.popover.content = label;
            element.popover(validator.defaults.popover).popover('show');
        },
        onfocusin: function(element){
            $(element).popover('destroy'); //destroy
        },
    
        success:function(error, element){
            $(element).popover('destroy');
            validator.defaults.popover.content = '<i class="glyphicon glyphicon-ok"></i>验证通过';
            $(element).popover(validator.defaults.popover).popover('show');
            //$(element).popover('destroy');
        },
        submitHandler:function(form){
            form.submit();
        }, 

        //弹出层样式
        placement:"right",
        html:true ,
        content:"呵呵", 
        trigger:"manual", 
        template:'<div class="popover" role="tooltip"><div class="arrow"></div><div class="popover-content"></div></div>'
    };
    
    var style = {
        test: {
            
            
        },
        test2: {
            
            
            
        }
    };
    
    if(typeof options == 'string'){
        options = style['style'];
    }else if(typeof options == 'object'){
        if(typeof options.style !== 'undefined'){
            options = $.extend(options.options, style[options.style]);
        }
    }else if(typeof options == 'function'){
        options = {submitHandler:  options};
    }else{
        options = {};
    }
    (typeof func == 'function')? options.submitHandler = func : null;
    $.extend(true, settings, options);
    $.validator.setDefaults(this.settings);
    
    var $element = (typeof selector == 'string')? $(selector) : selector;
    var validator  = $element.validate(rule);
    
    //添加正则验证方法
    function addReMethod(name, pattern, message){
        $.validator.addMethod(name, function(value, element, param) {
            if(typeof pattern == 'string') pattern = new Array(pattern);
            var result =  this.optional(element);
            for(var i in pattern){
                result = result || i.test(value); 
            }
            return result;
        }, message);
    }
    
},

demo: function(){
    return this.bind('click', function(event) { });
    return this.each(function(){ });
}

});

function builOptions(options, func){
    if(typeof options == 'function') options = {success: options};
    if(typeof options.success != 'function'){
        options.success = (typeof func == 'function') func : formSuccess;
    }
    return options;
}

}));



//远程验基准url
var url = {
    userinfo: "/account/userDataCheck",
}
//远程验证配置
var remote = {
    phone:url.userinfo+'/field/phone',
    email:url.userinfo+'/field/email',
};



var forms ={
        
    register:{
        rules:{
            tfield:{required:true, phone:true, remote:remote.phone },
            email:{required:true, email:true, remote:remote.email },
            smscode:{required: true},
            name:{required: true, rangelength:[4,10]},
            password:{required: true, rangelength:[8,30]},
            password_confirm:{required: true, equalTo:['ul','[name=reg_password]']},
            xieyi:{required: true,},
        },
        messages:{
            tfield:{phone:'请输入合法手机号', remote:'手机号已存在'},
            email:{remote:'邮箱已存在'},
            name:{rangelength:'用户名在{0}-{1}个字符'},
            password_confirm:{equalTo:'重复密码与设置密码不一致'},
            xieyi:{required:'请勾选同意协议'},
            
        }
    },
}
    

validator.addReMethod("phone", /^1[34578]\d{9}$/);
validator.addReMethod("TransAmt", /^\d{3,}/);
validator.addReMethod("drawcash_money", /^\d{3,}/);

$.validator.addMethod("realname", function(value, element) {
    return this.optional(element) || /^[\u4E00-\u9FA5\uf900-\ufa2d]{2,5}$/.test(value);
}, "请输入正确的姓名");

$.validator.addMethod('account', function(value, element, param) {   
    return this.optional( element ) || ( /^1[34578]\d{9}$/.test(value) 
    || /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test( value ));

});

