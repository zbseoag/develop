
system.onError();
//iframe 单击事件冒泡
system.startPropagation('click');





//格式化时间字符串
// console.log(new Date().toFormatString("Y年M月D日 H时I分S秒"));
Date.prototype.toFormatString = function(format){

    if(!format) format = "Y-M-D H:i:s";
    format = format.replace(/Y|y/, this.getFullYear());
    format = format.replace(/M|m/, this.getMonth() + 1);
    format = format.replace(/D|d/, this.getDate());
    format = format.replace(/H|h/, this.getHours());
    format = format.replace(/I|i/, this.getMinutes());
    format = format.replace(/S|s/, this.getSeconds());
    return format;
}


//request.init({"url":"abc.com"}).success(function(){ alert('success');}).send({aaa:"123456"});
const request = {

    init: function(options){
        this.options = options;
        return this;
    },
    url: function(value){
        this.options.success = value;
        return this;
    },
    success: function(value){
        this.options.success = value;
        return this;
    },

    fail: function (value) {
        this.options.fail = value;
        return this;
    },

    complete: function (value) {
        this.options.complete = value;
        return this;
    },
    method: function(value){
        this.options.method = value;
        return this;
    },
    post: function(value){
        this.method('POST').send();
    },
    get: function(value){
        this.method('GET').send();
    },

    send: function(options){
        if(options) this.options = Object.assign(this.options, options);
        if(!this.options.fail){
            this.options.fail = function(){console.log('error');};
        }
        console.log(this.options);
        this.options.success();
        this.options.fail();
    },

};
