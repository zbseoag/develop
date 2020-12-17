
var log = console.log;

var ajax = {

	init: function(data){

	    this.code = data.code;
        this.msg = data.msg;
        this.url = data.url;
	    return this;
	},
    
    message: function(time, refresh){
	    
	    var it = this;
	    
	    if(time && typeof time != 'number'){
            refresh = time; time = undefined;
        }
        
        if(time == undefined) time = 2000;
        
        var options = {message:this.msg, timeOut: time};
        options.type = (this.code == 1)? 'success' : 'error';
        
        $.toastr(options);
        
        if(typeof refresh == 'function'){
            setTimeout(function(){ refresh(it); }, time);
            
        }else if(refresh){
            
            if(refresh == 'reload'){
                setTimeout(function(){ window.location.reload(); }, time);
            }else{
                setTimeout(function(){ location.href = this.url; }, time);
            }
        }
        
    }
    
    

};


/**
 * cookie 对象
 */
var cookie = {

	set: function(name, value, day){
    	 var d = new Date();
    	 d.setTime(d.getTime() + (day*24*60*60*1000));
    	 var expires = "expires=" + d.toGMTString();
    	 document.cookie = name + "=" + value + "; " + expires;
	},

    get: function(b){
    	var a = document.cookie.match(new RegExp("(^| )" + b + "=([^;]*)(;|$)"));
        return ! a ? "": decodeURIComponent(a[2]);
    },
    
    del: function(a, b, c){
    	document.cookie = a + "=; expires=Mon, 26 Jul 1997 05:00:00 GMT; path=" + (c ? c: "/") + "; " + (b ? ("domain=" + b + ";") : "");
    },

};




/**
 * system 对象
 */
var system = {


    safe: function(){
    	if(window.top !== window.self){ window.top.location = window.location;}
    },
    
    onError: function(){
      
        window.onerror = function(message, url, line, colum, object){
          alert("消息：" + message +"\n\n位置："+ url + "\n\n行号：" + line);
        }
        return this;
    },
    
    open: function(url, name){
    	
    	if(typeof parent.tabWindow == 'undefined'){
    		window.open(url, name);
    	}else{
    		parent.tabWindow.open(url, name);
    	}
    	return this;
    },
    
    
    close: function(element){

    	if(typeof parent.tabWindow == 'undefined'){
    		window.close();
    	}else{
    		parent.tabWindow.close(element);
    	}
    	return this;
    },
    
    reload: function(url){
    	
    	if(typeof parent.tabWindow == 'undefined'){
    		window.reload();
    	}else{
    		parent.tabWindow.reload(url);
    	}

    	return this;
    },

    alert: function(message, offset){
        layer.alert(message, {icon: 5, title:'系统消息', offset: offset});
	},

    confirm: function(message, offset, callback){
        
        if(typeof offset == 'function'){
            callback = offset;
            offset = '140px';
        }
        
        layer.confirm(message, {icon: 3, title:'系统消息', offset: offset}, function(index){
            
            layer.close(index);
            callback(true);
        },function(index){
            
            layer.close(index);
            callback(false);
        });

	},
    
    query: function(search){
        
        if(!search) search = window.location.search.substr(1);
        search = search.split('&');
        var query = {};
        for(var i in search){
            var row = search[i].split('=');
            query[row[0]] = row[1];
        }
        return query;
    },
    
    startPropagation: function(event){
        
        if(self != top) {
            $(document).on(event, function(){
                parent.$('body').trigger(event);
            });
        }
        
    },

    download:function (name, url){
        window.location.href = url;
    }
};



var common = {


    mousewheel: function(){

        $("body").mousewheel(function(event, delta) {
            this.scrollLeft -= (delta * 100);
            event.preventDefault();
        });
    },


    /**
     * 数据表字段排序
     * button 触发按钮
     * table 数据表
     * url 提交地址
     * identifier 标识符
     */
    sortTableColumn: function(button, table, url, identifier, func){

        var modal = $.modal({title:'调整表格列次序'});
        if(typeof func != 'function'){
            func = this.callback;
        }
        modal.body(function(){

            var html = ''
            $(table).find('thead th').each(function(){
                var text = $(this).text();
                if(text) html += '<li class="list-group-item"><input  name="data[]" type="hidden" value="'+$(this).attr('data-field')+'"/>'+ $(this).text() +'</li>';
            });
            html = '<ul id="editable" class="list-group">'+html+'</ul><input type="hidden" name="identifier" value="'+identifier+'">';

            return html;

        }, 0).form(url).ajaxForm(func);

        //提交时隐藏弹窗
        modal.button('[type="submit"]', function(){ modal.hidden(); });
        var list = document.getElementById("editable");
        Sortable.create(list);

        if(button == null) modal.show();
        else $(button).click(function(){ modal.show(); });

    },
    

};