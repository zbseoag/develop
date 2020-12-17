/*
 * @author zbseoag
 * QQ: 617937424
 */
(function(factory){
    (typeof define === "function" && define.amd)? define(["jquery"], factory ): factory(jQuery);
}(function($){

    
    $.fn.question = function(options){
    
        var settings = $.extend({
    
            question: '#question',
            container: '#questions',
            remove: '.fa-remove'
        
        },(typeof options == 'object')? options : {});
        
        var it = this;
        var $question = $(settings.question);
        var $container = $(settings.container);
        
        this.radio = function(data){
            
            var option = '\
                <li style="padding:4px 0 4px 22px; margin: 0; border-width:0 0 1px 0;" class="list-group-item radio" data-mark="option">\
                    <input name="default"  type="radio">\
                    <span data-mark="sort">A</span>. <input style="width:30%;  border-width:0; outline: 0;" name="option" placeholder="选项">\
                    <div style="float: right;">\
                        <a><i class="fa fa-arrow-down"></i></a>&nbsp;\
                        <a><i class="fa fa-arrow-up"></i></a>&nbsp;\
                        <a><i class="fa fa-remove"></i></a>\
                    </div>\
                </li>';
            
            
            this.create(option, 1, data);
            
            
        };
        
        this.checkbox = function(data){
    
            var option = '\
                <li style="padding:4px 0 4px 22px; margin: 0; border-width:0 0 1px 0;" class="list-group-item checkbox" data-mark="option">\
                    <input  type="checkbox">\
                    <span data-mark="sort">A</span>. <input style="width:30%; border-width:0; outline: 0;" name="option" placeholder="选项">\
                    <div style="float: right;">\
                        <a><i class="fa fa-arrow-down"></i></a>&nbsp;\
                        <a><i class="fa fa-arrow-up"></i></a>&nbsp;\
                        <a><i class="fa fa-remove"></i></a>\
                    </div>\
                </li>';
    
            this.create(option, 2, data);
            
            
        };
        
        
        //创建问题
        this.create = function(option, type, data){
            
            option += '\
            <li style="padding: 4px; border-width:0 0 1px 0;"class="list-group-item">\
                <i class="fa fa-angle-double-right"></i>\
                <input onkeydown="keyPressEnter(this)" style="width:20%; border-width:0; outline: 0;"  placeholder="选项">&nbsp;\
                <input onkeydown="keyPressEnter(this)" style="width:30%; border-width:0; outline: 0;"  placeholder="前缀">\
            </li>';
            
            $container.append($question.clone(true).find('[data-mark="options"]').append(option).end().find('input[name="type[]"]').val(type).end());
            
            this.sequence();
   
            if(data){
                data.option = eval ("(" + data.option + ")");
                var question = $container.find('[data-mark="question"]').last();
                question.find('input[name="question[]"]').val(data.question);
                question.find('input[name="type[]"]').val(data.type);
                for(var i in data.option){
                    this.command('option -w ' + data.option[i], question.find('[data-mark="options"]').children('li').last());
                }
            }
            
            
        };
        
        
        this.command = function(command, $item){
            
            var param = command.replace(/\-\w+/ig,"|#|").replace(/\s+(\|#\|)\s+/g, '$1').split("|#|");
            var operate = command.match(/\-\w+/ig);
            var command = param.shift();
            var params = [];
            for(var i in operate){
                params[operate[i]] = param[i];
            }
            
            var word = params['-w'];
            if(typeof params['-l'] != 'undefined'){
   
                var list =  params['-l'].replace(/(^\s*)|(\s*$)/g,"").replace(/\s+/g, ',').split(',');
    
                for(var i in list){
                    if(list[i] == '@') list[i] = '';
                    $item.before($item.prev().clone().find('input[name^="option"]').val(list[i] + word).end());
                }
            }else{
                $item.before($item.prev().clone().find('input[name^="option"]').val(word).end());
            }
    
            if(!$item.siblings().first().find('input[name^="option"]').val()) $item.siblings().first().remove();
    
            this.sequence();
        
        }
        
        
        //删除
        this.remove = function(button){
            
            var $item = $(button).closest('[data-mark="option"]');
            if($item.length == 0){
                $item = $(button).closest('[data-mark="question"]');
            }else{
                if($item.siblings().length < 2) return false;
            }
            
            $item.remove();
            this.sequence();
        };
    
        //初始化试题选项
        this.refresh = function(button){
            
            $item = $(button).closest('[data-mark="question"]');
            $item.find('[data-mark="option"]').not(':first').remove();
            $item.find('[data-mark="option"]').find(':text').val('');
            this.sequence();
        };
    
        
        //上移
        this.up = function(button){
            
            var $item = $(button).closest('[data-mark="option"]');
            if($item.length == 0){
                $item = $(button).closest('[data-mark="question"]');
                if($item.index() == 1) return false;
            }
            
            $item.prev().before($item);
            this.sequence();
        };
    
        //下移
        this.down = function(button){
    
            var $item = $(button).closest('[data-mark="option"]');
            if($item.length == 0){
                $item = $(button).closest('[data-mark="question"]');
            }else{
                if(!$item.next().attr('data-mark')) return false;
            }
            
            $item.next().after($item);
            this.sequence();
        };
        
        
        //题序
        this.sequence = function(){
            
            $container.find('[data-mark="question"]').each(function(key, item){
                var $item = $(item);
                $item.find('[data-mark="sequence"]').html(key + 1);
                $item.find('input[name^="option"]').attr('name', 'option[' + key + '][]');
                
                $item.find('input[name^="default"]').attr('data-name', 'default[' + key + ']');
                
                //选项
                $item.find('[data-mark="options"]').find('li').each(function(key, item){
                    var $item = $(item);
                    $item.find('[data-mark="sort"]').html(String.fromCharCode(65 + key))
                });
                
                //所有
                $item.find(':radio').each(function(k, item){
                    
                    var $item = $(item);
                    $item.val($item.closest('li').find('[data-mark="sort"]').text());
                    if($item.attr('data-name')) $item.attr('name', $item.attr('data-name'));
                });
          
            });
            return this;
        };
        
        //绑定添加问题
        this.click(function(){
            var func = $(this).data('mark');
            it[func]();
        });
        
        
        //绑定删除
        $container.on('click', settings.remove, function(){
            it.remove(this);
        });

        //绑定下移
        $container.on('click', settings.down, function(){
            it.down(this);
        });
        
        //绑定上移
        $container.on('click', settings.up, function(){
            it.up(this);
        });
    
        //绑定初始化选项
        $container.on('click', settings.refresh, function(){
            it.refresh(this);
        });
        
        return this;
    };
    
    
    
    


}));