/*
 * @author zbseoag
 * QQ: 617937424
 */
(function(factory){
    (typeof define === "function" && define.amd)? define(["jquery"], factory ): factory(jQuery);
}(function($){

    
    /**
     * 表单插件
     * html结构:
        <form action="" method="get" autocomplete="off">
        <input type="text" name="username" />
        <button type="submit">提交</button>
        </form>
        $('form:eq(0)').form(function(data){ alert('success'); });
     */
    
    $.fn.form = function(options, func){
    
        if(typeof options == 'string') options = { url: options };
        if(typeof options == 'function') options = { success: options };
        if(typeof func == 'function') options.success = func;
    
        var it = this;
        this.settings = $.extend({url: '', success: null, type:'post', dataType:'json', onbefore:null }, (typeof options == 'object')? options : {});
        
        this.action = function(value){
            return value == undefined? this.attr('action') : this.attr('action', value);
        }
    
        this.method = function(value){
            return value == undefined? this.attr('method') : this.attr('method', value);
        }
    
        this.enctype = function(value){
            return value == undefined? this.attr('enctype') : this.attr('enctype', value);
        }
        
        //发送POST
        this.post = function(url, func){
            return this.submit('post', url, func);
        };
        
        //发送GET
        this.get = function(url, func){
            return this.submit('get', url, func);
        };
    
        //提交所有表单
        this.submit = function(type, url, func){
        
            if(type == undefined){
                $(this).submit();
            }else{
                this.each(function(){
                    it.send($(this), type, url, func);
                });
            }
            return this;
        };
        
        //发送异步请求
        this.send = function(form, type, url, func){
            
            //为了不破坏原参数
            var settings = $.extend({}, it.settings);//如果直接赋值，it.settings会按引用传值
            
            if(typeof type == 'function') settings.success = type; else if(type != undefined) settings.type = type;
            if(typeof url == 'function') settings.success = url; else if(url != undefined) settings.url = url;
            if(typeof func == 'function') settings.success = func;
            if(!settings.url) settings.url = form.attr('action');
            if(!settings.type) settings.type = form.attr('method');
    
            if(typeof it.settings.data == 'function'){
                settings.data = it.settings.data();
            }
      
            if(typeof settings.data == 'object'){
                settings.data = $.param(settings.data);
            }
            
            if(settings.data && typeof settings.data == 'string'){
                settings.data = settings.data + '&' + form.serialize();
            }else{
                
                if(form.attr('enctype') == 'multipart/form-data'){
                    settings.contentType = false;
                    settings.processData = false;
                    settings.data = new FormData(form.get(0));
                }else{
                    settings.data = form.serialize();
                }
            }
            settings.context = form;
            $.ajax(settings);
            return this;
        }
        
        
        /**
         * 把json数据填充到from表单中
         * html结构:
            <form action="" method="get" autocomplete="off">
    
                用户名:<input type="text" name="usrname"/><br/>
                性别:  <input type="radio" name="sex" value="0" />男
                      <input type="radio" name="sex" value="1" />女<br/>
                爱好:<input type="checkbox" name="hobby[]" value="sing"/>唱歌&nbsp;
                    <input type="checkbox" name="hobby[]" value="code"/>写代码&nbsp;
                    <input type="checkbox" name="hobby[]" value="trance"/>发呆<br/>
                    <button type="submit">提交</button>
            </form>
    
            $('form:eq(0)').form().insert({"usrname":"张三", "sex": "1", "hobby":["sing", "trance"]});
         */
         
        this.insert = function(data){
            
            if(!data) data = decodeURIComponent(location.search.slice(1).replace(/\+/g, '%20'));
            
            if(typeof data == 'string'){
                var search = data.split('&');
                var data = {};
                for(var i in search){
                    var row = search[i].split('='); data[row[0]] = row[1];
                }
            }
    
            this.each(function(key, item){
                
                var input, name, value;
                
                for(var i = 0; i < this.length; i++){
   
                    input = this.elements[i];
                    //checkbox的name可能是name[]数组形式
                    name = input.name.replace(/(.+)\[\]$/, "$1");
                    
                    var value  = data[name];
                    if(value instanceof Array){

                        var index = it.find('[name="'+input.name+'"]').index($(input));
                        value = data[name][index];
           
                        
                    }else if(typeof value == 'undefined'){
                        
                        //处理"datetime":{"name":"bought","start":"2015","end":"2017"} 这种数据
                        name = input.name.replace(/(.+)\[.+\]$/, "$1");
                        if(typeof data[name] == 'undefined') continue;
                        
                        var subname = input.name.replace(/.+\[(.+)\]$/, "$1");
                        if(data[name][subname] == undefined) continue;
                        
                        value = (typeof subname == 'string')? data[name][subname] : data[name];
                      
                    }
                    
                    switch(input.type){
                        case "checkbox":
                            if(value === ""){
                                input.checked = false;
                            }else{
                                input.checked = (value.indexOf(input.value) > -1) ? true : false;
                            }
                            break;
                        case "radio":
                            
                            if(value === ""){
                                input.checked = false;
                            }else if(input.value == value){
                                input.checked = true;
                            }
                            break;
                        case "file" :
                            $('img[src=' + name + ']').attr('src', data[name]);
                            break;
                        default:
                            
                            //如果select选项不存在时,"field":{"text":"文本", "value":"123"}数据可自动生成
                            if(input.type == 'select-one' && typeof value == 'object'){
                                if($(input).find('option[value="'+value.value+'"]').length < 1){
                                    $(input).append('<option value="'+value.value+'">'+value.text+'</option>');
                                }
                                value = value.value;
                            }
                            
                            input.value = value;
                    }
                    $(input).trigger('change');
                }
            });
            
            return this;
        };
    
        this.reset = function(){
            
            this.each(function(key, item){
                
                $(item).find("input[type='hidden']").val('');
                item.reset();
            });
            return this;
        };
        

        //绑定事件
        if(typeof this.settings.success == 'function'){
            this.bind('submit', function(){

                if(typeof it.settings.onbefore == 'function' && it.settings.onbefore(it) === false) return false;
          
                it.send($(this));
                return false;
            });
        }
        
        return this;
     
    };
    
    
    
    $.modal = function(options, func){
    
        if(typeof options == 'function') options = { bind: options };
        if(typeof func == 'function') options.show.bs.modal = func;
        
        var settings = $.extend({
            modal: 'modal',
            width: null,
            title: '标题',
            body: '',
            padding: '',
            url: '',
            footer: undefined,
            show: false
            
        }, (typeof options == 'object')? options : {});
    
            //var modal = 'modal-' + new Date().getMilliseconds() + Math.floor(Math.random() * 1000);
        var $modal = $('#'+ settings.modal);
        
        if($modal.length < 1){
            var html = '<div class="modal fade" id="'+settings.modal+'" tabindex="-1" role="dialog" aria-labelledby="">\
              <div class="modal-dialog" role="document">\
                <div class="modal-content">\
                  <div class="modal-header">\
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\
                    <h4 class="modal-title">title</h4>\
                  </div>\
                  <form class="form-horizontal" action="'+settings.url+'" method="post">\
                  <div class="modal-body">body</div>\
                  <div class="modal-footer">\
                    <button type="button" class="btn btn-default" data-dismiss="modal">取 消</button>\
                    <button type="submit" class="btn btn-primary">保 存</button>\
                  </div>\
                 </form>\
                </div>\
              </div>\
            </div>';
            $('body').append(html);
        }
    
        var $modal = $('#'+ settings.modal);
        
        this.width = function(width){
            if(width){
                $modal.find('.modal-dialog').css('width', settings.width);
            }
            return this;
        }
        
        this.title = function(title){
            
            if(arguments.length == 0) return $modal.find('.modal-title').text();
            $modal.find('.modal-title').html(title);
            return this;
        }
    
        this.body = function(body, padding){
         
            if(typeof body == 'function') body = body();
            $modal.find('.modal-body').html(body);
            if(padding != undefined) $modal.find('.modal-body').css('padding', padding);
            return this;
        }
        
        this.footer = function(footer){
         
            if(footer === null) footer = '';
            
            var $footer = $modal.find('.modal-footer');
            if(typeof footer == 'object'){
                $footer.find('button').eq(0).html(footer[0]);
                $footer.find('button').eq(1).html(footer[1]);
            }else{
                $footer.html(footer);
            }
            return this;
        }
        this.show = function(){
            $modal.modal('show');
            return this;
        }
        
        this.hide = function(){
            $modal.modal('hide');
            return this;
        }
        this.form = function(action, method, enctype){
            var $form = $modal.find('form');
            if(action) $form.attr('action', action);
            if(method) $form.attr('method', method);
            if(enctype) $form.attr('enctype', enctype);
            if(arguments.length != 0) return this;
            return $form;
        }
        
        
        this.button = function(button, func){
            
            var $button = $modal.find(button);
            if(typeof func == 'function'){
                $button.click(func);
            }
            if(arguments.length != 0) return this;
            return $button;
        }
        
        this.modal = function(){
            return $modal;
        }
        
        this.width(settings.width).title(settings.title).body(settings.body, settings.padding).footer(settings.footer);
        
        $modal.modal(settings);
        
        return this;
    
    };
    
    
    
    /**
     * 全选/取消
     * 示例：
        <div id="checkboxes">
             <input type="checkbox" />
             <input type="checkbox" />
        </div>
        <label><input type="checkbox" id="checked" />全选</label>
        $('#checked').check('#checkboxes');
     */
    $.fn.check = function(parent){
        
        var $this = $(this);
        if(typeof parent == 'undefined'){
          var $checkbox = $this.parents('form').find(":checkbox");
        }else{
          var $checkbox = $(parent).find(":checkbox");
        }
        
        $checkbox.click(function(){
            
            if($checkbox.find() == $checkbox.length){
                $this.prop("checked", true);
            }
        });
        
        this.click(function(){
            if(this.checked){
                $checkbox.prop("checked", true);
            }else{
                $checkbox.prop("checked", false);
            }
        });
        
        return this;
    };
    
    
    
    /**
     * 倒计时
    <ul>
    <li class="timer" data-second="10"></li>
    <li class="timer" data-second="20"></li>
    </ul>
    $('.timer').timer(function($obj){
        system.alert($obj.attr('data-second'));
    });
     */
    
    $.fn.timer = function(options, func){
        
        var settings = $.extend({attr:'data-second', func:null, html:'%d天%h时%m分%s秒', dataType:'array'}, (typeof options == 'object')? options : {});
        
        if(typeof settings.func == 'function')func = settings.func;
        
        this.each(function(){
            
            var $this = $(this);
            var seconds = $this.attr(settings.attr);
            
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
                //处理结果显示
                if(typeof settings.html == 'function'){
                    
                    if(day < 10) day = '0' + day;
                    
                    var data = {day: day.toString(), hour: hour.toString(),    minute: minute.toString(), second: second.toString()}
                    if(settings.dataType == 'array'){
                        for(var name in data){
                            data[name] = [data[name].substr(0, 1), data[name].substr(1, 1), -1];
                        }
                        var last = $this.data('timer-last-data');
                        if(typeof last == 'object'){
                            for(var name in data){
                                if(data[name][0] != last[name][0]) data[name][2] = 0;
                                if(data[name][1] != last[name][1]) data[name][2] = 1;
                                if(data[name][0] != last[name][0] && data[name][1] != last[name][1]) data[name][2] = 2;
                            }
                        }
                        $this.data('timer-last-data', data);
                    }
                    settings.html($this, data);
                }else{
                    var html = settings.html.replace(/%d/, day);
                    html = html.replace(/%h/, hour);
                    html = html.replace(/%m/, minute);
                    html = html.replace(/%s/, second);
                    $this.html(html);
                }
                
                if(seconds <= 0){
                    window.clearInterval(intval);
                    if(typeof func == 'function') func($this);
                }
                seconds--;
            }, 1000);
    
        });
        
        return this;
    };


    $.fn.icheck = function(options, func){
        
        if(typeof options == 'function') options = { click: options };
        if(typeof func == 'function') options.click = func;
      
        var settings = $.extend({
            parent: 'form',
            checkboxClass: 'icheckbox_minimal-green',
            radioClass: 'icheckbox_minimal-green',
            increaseArea: '5%',
            style: 'success'
        },(typeof options == 'object')? options : {});
      
      self = $(this);
      
      this.closest(settings.parent).find('input').iCheck(settings);
    
      //全选
      this.on('ifClicked', function(event){
    
        var checked = (event.currentTarget.checked == false)? 'check' : 'uncheck';
        self.closest(settings.parent).find(':checkbox').iCheck(checked);
    
      });
    
      //没有选中项时
      this.closest(settings.parent).find(':checkbox').on('ifUnchecked', function(event){
         self.iCheck('uncheck');
      });
      
      //选中时
      this.closest(settings.parent).find(':checkbox').not('[data-button]').on('ifChanged', function(event){
        
        var checkbox = event.target;
        if(event.currentTarget.checked){
            $(checkbox).closest('tr').addClass('success');
        }else{
            $(checkbox).closest('tr').removeClass('success');
        }
        if(typeof settings.click == 'function') settings.click(event);
        
      });
      
    
      return this;
      
    };
    
    
    
    $.fn.laydate = function(options){
      
      if(typeof options == 'string'){
          
          var config = {
              year: {format: 'yyyy'},
              month: {format: 'yyyy-MM'},
              date: {format: 'yyyy-MM-dd'},
              datetime: {format: 'yyyy-MM-dd HH:mm:ss'}
              
          };
          options = $.extend({type:options}, config[options]);
      }
      
      var settings = $.extend({
        type: 'datetime',
        theme: 'default', //default、molv:墨绿、#颜色值、grid:格子、xxx:表示class="laydate-theme-xxx"
        format: 'yyyy-MM-dd HH:mm:ss',
        range: false,//范围选择
        //min: '2017-1-1',//最小值
        //max: '2017-12-31',//最大值
        trigger: 'click', //采用click弹出
        position: 'absolute', //定位方式,
        zIndex: 66666666,
        calendar: false, //公历节日
        mark: {
            '0-12-15': '生日'
        },
        //ready: function(date){},
        //change: function(value, date, endDate){},
        //done: function(value, date, endDate){}
          
      }, (typeof options == 'object')? options : {});
      
      laydate.set(settings);
      
      this.run = function(){
    
        this.each(function(){
            this.id = 'laydate-'+ new Date().getMilliseconds() + Math.floor(Math.random() * 1000);
            settings.elem = '#' + this.id;
            laydate.render(settings);
        });
        return this;
        
      }
      
      return this.run();
    };
    
    
    
    $.toastr = function(options, type){
          
          if(typeof options == 'string') options = {message : options, type:"success" };
          if(type !== undefined) options.type = type;
          
          var settings = $.extend({
            title: "",
            message: "this is a test message!",
            closeButton: true,
            debug: false,
            newestOnTop: false,
            progressBar: true,
            positionClass: "toast-top-right",
            preventDuplicates: false,
            onclick: null,
            showDuration: "300",
            hideDuration: "1000",
            timeOut: "3000",
            extendedTimeOut: "1000",
            showEasing: "swing",
            hideEasing: "linear",
            showMethod: "fadeIn",
            hideMethod: "fadeOut"
          }, (typeof options == 'object')? options : {});
          
          toastr.options = settings;
          if(settings.type) toastr[settings.type](settings.message, settings.title);
          
          this.info = function(message, title){ toastr['info'](message, title);  };
          this.success = function(message, title, func){
              
              if(typeof title == 'function'){
                  func = title; title = '';
              }
              toastr['success'](message, title);
              if(typeof func == 'function') window.setTimeout(func, parseInt(settings.timeOut));
          };
          
          this.warning = function(message, title, func){
              
              toastr['warning'](message, title);
              if(typeof func == 'function') window.setTimeout(func, parseInt(settings.timeOut));
          };
          
          this.error = function(message, title, func){
              
              toastr.options.timeOut = "100000";
              toastr.options.positionClass = "toast-top-full-width";
              toastr['error'](message, title);
              if(typeof func == 'function') window.setTimeout(func, parseInt(settings.timeOut));
          };
          
          return this;
    };
    
    
    
    $.fn.tabWindow = function(options){
        
          var settings = $.extend({
              
              menu: '',
              links: '',
              content: ''
          }, (typeof options == 'object')? options : {});
    
          var $this = this;
          var $links = $(settings.links);
          var $content = $(settings.content);
         
          //打开
          this.open = function(url, title, opener){
            
              //打开者
              if(opener === true) opener = $links.find('.active').attr('data-href');
            
              $links.children().removeClass('active');
              $content.children().hide();
              
              if(typeof url == 'object'){
                  title = url.text();
                  url = url.attr('data-href');
              }
              
              if($this.find('a[data-href="'+ url +'"]').length < 1){
     
                  //选项卡
                  var $link = $links.children(':first-child').clone();
                  var html = $link.html($link.html().replace(/.*&nbsp;/, title + '&nbsp;'));
                  $link.addClass('active').attr('data-href', url).append(html);
                  if(opener) $link.attr('data-opener', opener);
                  
                  //追加到父级
                  $links.append($link);
    
                  // 添加选项卡对应的iframe
                  var iframe = $content.children(':first-child').clone().attr('src', url).show();
                  $content.append(iframe);
                  
              }else{
    
                  var $link = $links.children('a[data-href="'+ url +'"]').addClass('active');
                  var $iframe = $content.children('iframe[src="'+ url +'"]').show();
                  //修正chrome滚动条bug
                  if($iframe.length > 0){
                      iframe = $iframe[0];
                      iframe.style.height = '99%';
                      iframe.scrollWidth;
                      iframe.style.height = '100%';
                  }
                  
              }
              $this.location($link);
              return this;
              
          };
          
          
          //定位到
          this.location = function(element) {
            
              var marginLeft = element.prevAll().sum();
              var marginRight = element.nextAll().sum();
              
              //可视区域tab宽度
              var visibleWidth = $this.outerWidth(true) - $this.children().not("nav").sum();
              var scrollVal = 0;
    
              if($links.outerWidth() < visibleWidth) {
                  scrollVal = 0;
              } else if (marginRight <= (visibleWidth - element.outerWidth(true) - element.next().outerWidth(true))) {
    
                  if ((visibleWidth - element.next().outerWidth(true)) > marginRight) {
                      scrollVal = marginLeft;
                      var $tabElement = element;
                      while ((scrollVal - $tabElement.outerWidth()) > ($links.outerWidth() - visibleWidth)) {
                          scrollVal -= $tabElement.prev().outerWidth();
                          $tabElement = $tabElement.prev();
                      }
                  }
                  
              } else if (marginLeft > (visibleWidth - element.outerWidth(true) - element.prev().outerWidth(true))) {
                  scrollVal = marginLeft - element.prev().outerWidth(true);
              }
              
              $links.animate({marginLeft: 0 - scrollVal + 'px' }, "fast");
          
          };
          
          
          //关闭
          this.close = function(element){
    
              if(element == undefined) element = $links.find('.active');
              if(typeof element == 'string')  element = $links.find('[data-href="'+ element +'"]');
              
              var $next;
              //如果有打开者
              if(element.attr('data-opener')){
                  $next = $links.find('[data-href="'+ element.attr('data-opener') +'"]');
              }
        
              if(!$next || $next.length < 1) $next = element.next();
              if($next.length < 1) $next = element.prev();
              
              element.remove();
              $content.children('iframe[src="'+ element.attr('data-href') +'"]').remove();
              $this.open($next);
              return this;
          };
          
          
          //刷新
          this.reload = function(url){
              
              var selector = url? '[src="'+ url +'"]' : 'iframe:visible';
              var iframe = $content.children(selector);
              iframe.attr('src', iframe.attr('src'));
              return this;
          };
          
    
          //滚动(前/后)
          this.scroll = function(direction){
              
              if(direction == undefined) direction = 'forward';
    
              //Tab可视区宽度
              var width = $this.innerWidth() - $this.children().not('nav').sum();
              var marginLeft = Math.abs(parseInt($links.css('margin-left')));
              var $tabElement = $links.children(':first');
    
               if(direction == 'forward'){
                  //如果菜单没超出可视区
                  if($links.width() < width) return false;
               }else{
                 if(marginLeft == 0) return false;
               }
            
              var offset = 0;
              var scroll = 0;
              //找到离当前第一个可见的Tab元素
              while ((offset + $tabElement.outerWidth(true)) <= marginLeft){
                  offset += $tabElement.outerWidth(true);
                  $tabElement = $tabElement.next();
              }
              
              offset = 0;
              if(direction == 'forward'){
    
                  while((offset + $tabElement.outerWidth(true)) < (width) && $tabElement.length > 0){
                      offset += $tabElement.outerWidth(true);
                      $tabElement = $tabElement.next();
                  }
                  
                  var scroll  = $tabElement.prevAll().sum();
                  if(scroll > 0){
                      $links.animate({ marginLeft: 0 - scroll + 'px'}, "fast");
                  }
                  
              }else if(direction == 'backward'){
    
                  if($tabElement.prevAll().sum() > width) {
                      while ((offset + $tabElement.outerWidth(true)) < (width) && $tabElement.length > 0) {
                        offset += $tabElement.outerWidth(true);
                          $tabElement = $tabElement.prev();
                      }
                      var scroll  = $tabElement.prevAll().sum();
                  }
                  
                  $links.animate({ marginLeft: 0 - scroll + 'px'}, "fast");
                  
              }
    
          };
    
          
          //菜单单击
          $(settings.menu).find('a[href]').click(function(){
                if($(this).attr('target')) return true;
                $this.open($(this).attr('href'), $(this).text());
                return false;
          });
    
          //标题单击
          $links.on('click', 'a', function(){
              $this.open($(this));
          });
    
          //关闭
          $links.on('click', 'a>i', function(e){
             
              e.stopPropagation();
              var $link = $(this).closest('a');
              if($link.index() == 0) return false;
              
              $this.close($(this).closest('a'));
          });
          
          
          //操作按钮
          $this.children('button').click(function(){
              
              var type = $(this).attr('data-button');
              if(type == 'reload'){
                  $this.reload();
              }else if(type == 'forward' || type == 'backward'){
                  $this.scroll(type);
              }
              
          });
          
          
          //滚动到已激活的选项卡
          $this.find('[data-button="show-active"]').on('click', function(){
              $this.location($links.find('.active'));
          });
         
    
           //批量关闭选项卡
          $this.find('[data-button^="close"]').on('click', function(){
            
            if($(this).data('button') == 'close-other'){
                var $close = $links.children().not(':first').not('.active');
            }else{
                var $close = $links.children().not(':first');
            }
    
            $close.each(function (){ $this.close($(this)); });
            $links.css('margin-left', '0');
          });
          
          return this;
    
    };
    
    
    
    $.fn.sum = function(attr, options){
        
        if(attr == undefined) attr = 'width';
        if(options == undefined) options = true;
        
      var sum = 0;
      this.each(function(){
            if(typeof options == 'function'){
                sum += options(parseFloat($(this).attr(attr)), options);
            }else{
    
                switch(attr){
                    case 'width' : sum += $(this).outerWidth(options);
                        break;
                    case 'height' : sum += $(this).outerHeight(options);
                        break;
                    case 'value' : sum += parseFloat($(this).val());
    
                        break;
                    default:
           
                        sum += parseFloat($(this).attr(attr));
                }
    
            }
    
      });
      return sum;
      
    };


    
    /**
     * 折叠ibox
     * $('.collapse-link').collapse();
     */
    $.fn.collapse = function(){
        
          var settings = $.extend({
              ibox: 'div.ibox',//box模块
              iboxClass: 'border-bottom',//box切换样式
              icon: 'i',//图标元素
              iconClass: ['fa-chevron-up', 'fa-chevron-down'], //图标切换样式
              content: '.ibox-content',//内容
              event: 'click'//绑定事件
              
         },(typeof options == 'object')? options : {});
        
          var ibox = $(this).closest(settings.ibox);
          var button = $(this);
          var content = ibox.find(settings.content);
          var icon = button.find(settings.icon);
    
          button.on(settings.event, function(){
              
              content.slideToggle(200);
    
              icon.toggleClass(settings.iconClass[0]).toggleClass(settings.iconClass[1]);
              ibox.toggleClass('').toggleClass(settings.iboxClass);
              
              setTimeout(function () {
                  ibox.resize();
                  ibox.find('[id^=map-]').resize();
              }, 50);
          });
    
    };


    $.fn.handle = function(options, callback){
        
        if(typeof options == 'object' && arguments.length == 1) options = { callback: options };
        if(typeof options == 'string') options = { event: options };
        if(typeof callback == 'object') options.callback = callback;
        
        var settings = $.extend({data: {}, event: 'click', parent: 'tr', callback: {}},(typeof options == 'object')? options : {});
        
        var it = this;
        this.run = function($element){
          
            if($element.attr('disabled') == 'disabled' || $element.hasClass('disabled')) return false;
            
            var handle = $element.data('button');
            
            var data = $element.closest(settings.parent).data('row');
            if(typeof data == 'string') data = (!data)? {} : JSON.parse(data);
            data = $.extend(settings.data, data);
            
            if(typeof settings.callback[handle] == 'function') settings.callback[handle](data, $element);
        }
        
        if(settings.event){
             this.on(settings.event, function(){ it.run($(this)); });
        }else{
             it.run(it);
        }
      
    };
    
    /**
     * 表格字段排序
     * @param options
     * <input type="hidden" name="_sort" value="" />
     * <a data-order="amount" style="padding:0 0.5em;"><i class="fa fa-sort"></i></a>
     *
     * $('[data-order]').sort();
     */
    $.fn.sort = function(options){
    
        var settings = $.extend({
            
            attr: 'data-order',
            form: '#search',
            input: 'input[name="_sort"]'
            
        },(typeof options == 'object')? options : {});
        
        var $form = $(settings.form);
        var $input = $form.find(settings.input);
        var input = $input.val();
        
        if(input){
            input = input.split(':');
            $('['+settings.attr+'="'+input[0]+'"]').data('sort', input[1]).find('i').attr('class', 'fa fa-sort' + (input[1]? "-" + input[1] : ''));
            
        }
        
        this.click(function(){
            
            var sort = $(this).data('sort');
            if(!sort){
                sort = 'desc';
            }else if(sort == 'desc'){
                sort = 'asc';
            }else if(sort == 'asc'){
                sort = '';
            }
            
            $(this).data('sort', sort).find('i').attr('class', 'fa fa-sort' + (sort? "-" + sort : ''));
            
            if(sort) sort = $(this).data('order') + ":" + sort;
            $input.val(sort);
            
            $form.submit();
            
        });
    
    };

    $.plug = function(name){
        return $('[data-plugin="'+name+'"]');
    };
    
    
    $.fn.radio = function(checked, unchecked){
        
        this.click(function(){
        
            if(!$(this).data('checked')) $(this).data('checked', 'false');
            var check = $(this).data('checked');
            this.checked = check == 'true'? false : true;
            $(this).data('checked', check == 'true'? 'false' : 'true');
            
            if(check == true){
                if(typeof unchecked == 'function') unchecked(this);
            }else{
                if(typeof checked == 'function') checked(this);
            }
        
        });
        
        return this;
    };
    
    
    
    
    $.fn.json = function(options, func){
        
        if(typeof options == 'function') options = { func: options };
        if(typeof func == 'function') options.func = func;
        
        var settings = $.extend({ autoSelect:true, data:[], field:['value', 'name'], init:'', template:'<option value="{0}">{1}</option>', func: null}, (typeof options == 'object')? options : {});
        
        return this.each(function(){
            
            var options = (typeof settings.init == 'boolean' && settings.init == true)? $(this).html() : settings.init;
            var length = 0;
            if(settings.data){
                for(var row in settings.data){
                    length++;
                    if(typeof settings.data[row] != 'object'){
                        
                        var temp = {};
                        temp[settings.field[0]] = row;
                        temp[settings.field[1]] = settings.data[row];
                        settings.data[row] = temp;
                    }
                    
                    var fields = settings.template.match(/\{(\d+)\}/g);
                    var option = settings.template;
                    log(fields);
                    for(var i in fields){
                        option = option.replace(fields[i], settings.data[row][ settings.field[i] ]);
                    }
                    options += option;
                }
    

            }
            
            $(this).children().first().siblings().remove();
            $(this).append(options).trigger('reset');
            //如果只有一个选项则自动选中
            if(length == 1 && settings.autoSelect) $(this).children(':last').prop('selected', true);
            
            if(typeof settings.func == 'function') settings.func($(this));
            
        });
    };
    
    
    $.fn.list = function(options, func){
        
        if(typeof options == 'function') options = { func: options };
        if(typeof func == 'function') options.func = func;
        
        var settings = $.extend({ autoSelect:true, data:[], init:'', field:['value', 'name'], template:'<option value="{value}">{name}</option>', func: null}, (typeof options == 'object')? options : {});
        
        return this.each(function(){
            
            var options = (typeof settings.init == 'boolean' && settings.init == true)? $(this).html() : settings.init;
            var length = 0;
            if(settings.data){
                for(var row in settings.data){
                    length++;
                    if(typeof settings.data[row] != 'object'){
                        
                        var temp = {};
                        temp[settings.field[0]] = row;
                        temp[settings.field[1]] = settings.data[row];
                        settings.data[row] = temp;
                    }
                    
                    var fields = settings.template.match(/\{\w+\}/g);
                    var template = settings.template;
    
                    for(var i in fields){
                        template = template.replace(fields[i], settings.data[row][ fields[i].slice(1, -1) ]);
                    }
                    options += template;
                }
                
                
            }
            
            $(this).children().first().siblings().remove();
            $(this).append(options).trigger('reset');
            //如果只有一个选项则自动选中
            if(length == 1 && settings.autoSelect){
                $(this).children(':last').prop('selected', true);
                $(this).trigger('change');
            }
            
            if(typeof settings.func == 'function') settings.func($(this));
            
        });
    };
    
    
    $.fn.select = function(options){
    
        if(typeof options == 'string') options = { url: options };
        
        var it = this;
        this.settings = $.extend({url:'', event:'change', field:'id', value:'value', type:'get', data:{}, dataType:'json', async:true, onbefore:null}, (typeof options == 'object')? options : {});
        
        this.send = function(func){
    
            //为了不破坏原参数
            var settings = $.extend({}, it.settings);//如果直接赋值，it.settings会按引用传值

            this.on(it.settings.event, function(){
        
                var value = $(this).prop(it.settings.value);
                if(value === undefined ||  value === '') return func(value);
                
                settings.data[it.settings.field] = value;
                settings.success = func;
                
                $.ajax(settings);
            });
    
            return this;
        };
        
        this.value = function(){
            
            this.each(function(){
                $(this).val($(this).data('value'));
            });
            return this;
        };
        
        this.option = function(){
            return this.find('option[value='+ this.val() +']');
        };
        
        this.clear = function(){
            
            this.children().first().siblings().remove();
            return this;
        };
        
        return this;
        
    };
    
    
    
    /**
     * 异步上传文件
     $('button').ajaxFile({url: 'test.php', file: '#avatar'}, function(data){ alert(data); });
     */
    $.fn.ajaxFile = function(options, func){
        
        if(typeof func == 'function') options.success = func;
        
        var settings = $.extend({
            
            url: '',
            file: 'file',
            open: '',
            clear:'',
            name: '',
            cache: false,
            processData: false,
            contentType: false,
            type: 'POST',
            dataType:'json',
            success: function(data){console.log(data);}
            
        },(typeof options == 'object')? options : {});
        
        
        
        var self = this;
        var formData = new FormData();
        
        this.before('<input type="file" style="display:none;">');
        var $file = this.prev(':file');
        
        this.open = function(){
            $file.trigger('click');
            return this;
        }
        
        this.clear = function(){
            $file.val('');
            if(settings.name) $(settings.name).val('');
            return this;
        }
        
        this.upload = function(){
            
            formData.append(settings.file, $file.prop('files')[0]);
            settings.data = formData;
            $.ajax(settings);
            return this;
        }
        
        if(settings.clear) $(settings.clear).click(function(){ self.clear(); });
        if(settings.open){
            $(settings.open).click(function(){ self.open(); });
            this.click(function(){ self.upload(); });
            if(settings.name) $file.change(function(){ var name = $(this).val().replace(/\\/g, '/'); name = name.substr(name.lastIndexOf('/') + 1);   $(settings.name).val(name); });
        }else{
            this.click(function(){ self.open(); });
            $file.change(function(){self.upload(); });
        }
        
    };
    
    
    /**
     * 异步上传文件
     $('button').file({url: 'test.php', file:'avatar'}, function(data){ alert(data); });
     */
    $.fn.file = function(options, func){
        
        if(typeof func == 'function') options.success = func;
        
        var settings = $.extend({url:'', file:'file', confirm: true, event:'click', cache:false, processData:false, contentType:false, type:'POST', dataType:'json',},(typeof options == 'object')? options : {});
        
        var it = this;
        var form = new FormData();
        this.before('<input type="file" style="display:none;">');
        var $file = this.prev(':file');
        
        this.open = function(){
            $file.trigger('click');
            return this;
        };
        
        this.upload = function(){
    
            form.append(settings.file, $file.prop('files')[0]);
            settings.data = form;
            $.ajax(settings);
            return this;
        };
    
        this.clear = function(){
            $file.val('');
            return this;
        };
        
        if(settings.event){
            this.on(settings.event, function(){ it.open(); });
        }else{
            it.open();
        }
        $file.change(function(){
            
            if(!window.confirm('确定上传文件?')){
                it.clear(); return false;
            }
            it.upload(); it.clear();
        });
        
        return this;
    };
    
    
}));