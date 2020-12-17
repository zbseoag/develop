
//定时器
$.fn.timer = function(options, func){

    if (typeof options == 'function') options = { success: options };
    if (typeof func == 'function') options.success =  func;

    options = $.extend(true, {
        seconds: 'data-timer-second',
        success: function($(this)){ },
        format: 'day天hour时minute分second秒',
        event: undefined,
    },(options !== undefined)? options : {});

    var $this = $(this);
    if(event == undefined){
        return this.each(function(){ __function(); });
    }else{
        this.on(options.event, function(){
            if(typeof options.before == 'function'){
                options.before($this, __function);
            }else{
                __function();
            }
        })
    }

    function __function(){

        var seconds = parseInt($this.attr(settings.attr));
        if(seconds <= 0 || isNaN(seconds)) return;
        var intval = window.setInterval(function(){
            var day = hour = minute = second = 0;
            if(seconds > 0){
                day = Math.floor(seconds / 3600 / 24);
                hour = Math.floor(seconds / 3600) % 24;
                minute = Math.floor(seconds / 60) % 60;
                second = seconds % 60;
            }
            if(hour < 10) hour = '0' + hour;
            if(minute < 10) minute = '0' + minute;
            if(second < 10) second = '0' + second;
            html = settings.html.replace(/day/, day);
            html = html.replace(/hour/, hour);
            html = html.replace(/minute/, minute);
            html = html.replace(/second/, second);
            $this.html(html);
            if(seconds <= 0){
                window.clearInterval(intval);
                options.success($this);
            }
            seconds--;
        }, 1000);
    }
};


 function ajaxSend(options, callback){

    var defaults = { type: 'get', dataType:'json', success: function(data){ alert(data);}, url:'', data:'', async: true };
    
    for(var i in options){
        defaults[i] = options[i];
     }
    var settings = defaults;
    var xmlhttp = (window.XMLHttpRequest)? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
    
    xmlhttp.onreadystatechange = function(){
        if(xmlhttp.readyState==4 && xmlhttp.status==200){
            var data = (settings.dataType == 'xml')? xmlhttp.responseXML : xmlhttp.responseText;
            callback(data);
        }
    }

    if(settings.type == 'get'){
        settings.url = settings.url + '?'+ settings.data;
        settings.data = '';
    }
    
    //console.log(settings);
    xmlhttp.open(settings.type,  settings.url, settings.async);
    if(settings.type == 'post') xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    
    xmlhttp.send(settings.data);
    
 }



var system = {

    alert: function(message){
        alert(message);
    },
    
    confirm:function(message){
        confirm(message);
    },
    
    debug: function(){
        
        window.onerror = function(message, url, line, colum, object){
          alert("消息：" + message +"\n\n位置："+ url + "\n\n行号：" + line);
        };
        
        
        $(document).ajaxError(function(event, jqXHR, options, errorMsg){
            var msg = jqXHR.responseText.match(/<body.*\s*>(.|\s)*<\/body>/g);
            if(msg){
                msg = msg[0].replace(/<\/?[^>]*>/gim,"").replace(/(^\s+)|(\s+$)/g,"").replace(/\n\s+/g,"\n").replace(/[ \f\t\v]+/g," ");
            }else{
                msg = jqXHR.responseText;
            }
            if(typeof msg == 'string' && msg !='')alert('错误信息:' + msg);
            
        });

    },
    
    
    sleep: function sleep(second, func, argument){
        
        setTimeout(() => { func(argument); }, second * 1000);
    },
    
    cookie: {
        
        get: function(name) {
            var array = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
            return !array? "": decodeURIComponent(array[2]);
        },
        
        del: function(name){
            var date=new Date();
            date.setTime(date.getTime() - 3600);
            document.cookie=name+"=; expire="+date.toUTCString();
        },
        
        set: function (name, value, expire, path, domain){
        
            expire = expire || 3600;
            path = path || '/';
            domain = domain? "domain=" + domain + ";" : "";
            var date = new Date();
            date.setTime(date.getTime() + expire);
            document.cookie = name + "="+escape(value) + ";expires="+date.toUTCString()+";path=" + path + ";" + domain;
        }
    },
    
    ajax: function(url, options, func){
  
      if(typeof options == "function") options = { success : options};
      if(typeof func == "function") options.success = func;
      
      var settings = { type: 'get', dataType: 'json', url: '', data: '', async: true, success: function(data){ console.log(data);}};
      for(var i in options){
          settings[i] = options[i];
      } 
      settings.url = url;
      var xmlhttp = (window.XMLHttpRequest)? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
      
      //成功回调
      xmlhttp.onreadystatechange = function(){
          if(xmlhttp.readyState==4 && xmlhttp.status==200){
              var data = (settings.dataType == 'xml')? xmlhttp.responseXML : xmlhttp.responseText;
              settings.success(data);
          }
      }
      
      if(typeof settings.data == "function") settings.data = settings.data();
      
      if(typeof settings.data !== 'string'){
        var txt = '';
        for(var i in settings.data){
          txt += i + "=" + settings.data[i] + "&";
        }
        settings.data = txt.slice(0, -1);
      }
      
      if(settings.type == 'get'){
          query = (settings.url.indexOf('?') == -1)? '?' : '&';
          settings.url = settings.url + query + settings.data;
          settings.data = '';
      }
      
      xmlhttp.open(settings.type,  settings.url, settings.async);
      if(settings.type == 'post') xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
      xmlhttp.send(settings.data);
  
    },
    
    
    /**

        var data = [
           {id:2, assigned:"2016-12-24 09:20"},
           {id:1, assigned:"2016-12-24 09:10"},
           {id:4, assigned:"2016-12-24 09:50"},
        ]
    */
    jsonSort: function(data, field, asc){
        
        var sortField = [];
        var sort = [];
        for(var i in data){
            sortField.push(data[i][field]);
        }
        sortField.sort();
        if(typeof asc == 'undefined') asc = true;
        
        if(asc == false) sortField.reverse();
        for(var i in sortField){
            for(j in data){
                if(sortField[i] == data[j][field]){
                    sort.push(data[j]);
                    break;
                }
            }
        }
        return sort;
    },
    
};



 
 
 
/*
var code = "alert(\"入侵成功!\");";
console.log(hacker.makecode(hacker.encode(code, 4, 2), 4, 2));

*/

var hacker = {

    encode: function(code, mode, min){
 
        var nubmer = [];
        for(var i = 0; i < code.length; i++){
        // i % 4 + 2 因为 i % 4 = {0, 3} 所有{0, 3} + 2 = {2, 5};
        nubmer[i] = code.charCodeAt(i) * (i % mode +  min);
        }
        return nubmer.join(".");
 
    },
 
    decode: function(nubmer, mode,  min){
 
        var eval = window["eval"];
        var code= "";
        nubmer = nubmer.split(".");
        for (i = 0; i < nubmer.length; i++) {
            code += String.fromCharCode(nubmer[i] / (i % mode + min));
        }
        return code;

    },
 
    makecode: function(number, mode, min){

        var code = '\
        s = ""; \r\n\
        try {\r\n\
        q = document.createElement("p");\r\n\
        q.appendChild("123" + n);\r\n\
        } catch (qw) {\r\n\
        try {\r\n\
        a = prototype;\r\n\
        } catch (zxc) {\r\n\
        e = window["e" + "va" + "l"];\r\n\
        n = "{:number:}".split(".");\r\n\
        if (window.document) for (i = 6 - 2 - 4; - {:length:} + i != 2 - 2; i++) {\r\n\
        k = i;\r\n\
        s = s + String.fromCharCode(n[k] / (i % {:mode:} + {:min:}));\r\n\
        }\r\n\
        e(s);\r\n\
        }\r\n\
        }\r\n';
        code = code.replace("{:number:}", number);
        code = code.replace("{:length:}", number.split(".").length);
        code = code.replace("{:mode:}", mode);
        code = code.replace("{:min:}", min);
        
        return code;
    }
 
}









