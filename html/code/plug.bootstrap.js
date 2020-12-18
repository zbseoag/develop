/*
 * bootstrap Plugin v1.0
 * @author zbseoag
 */

(function(factory){
    (typeof define === "function" && define.amd)? define( ["jquery"], factory ): factory(jQuery);
}(function($){

    
$.extend($.fn, {


advModal: function(options, func){

    if(typeof options == 'function') options = { success:options };
    if(typeof func == 'function') options.success = func;
    
    var settings = $.extend({modal:'autoWindow', event:'click', width:'', form:'', title:'标题', content:'', footer:'', success:'', onload:'', hide:false}, (typeof options == 'object')? options : {});

    var $modal = $('#'+ settings.modal);
    if($modal.length < 1){
        var html = '<div class="modal fade" id="'+ settings.modal +'" role="dialog" aria-hidden="true">\
              <div class="modal-dialog">\
                <div class="modal-content pop">\
                  <div class="modal-header">\
                    <a class="close" data-dismiss="modal" aria-hidden="true">&times;</a>\
                    <h4 class="modal-title"></h4>\
                  </div>\
                  <div class="modal-body"></div>\
                  <div class="modal-footer" style="margin-top:0">\
                    <button type="button" data-button="0" class="btn btn-default" data-dismiss="modal">取&nbsp;消</button>\
                    <button type="button" data-button="1" class="btn btn-primary">确&nbsp;定</button>\
                  </div>\
                </div>\
              </div>\
            </div>';
        $('body').append(html);
        $modal = $('#'+ settings.modal);

    }

    $modal.find('.modal-title').html(settings.title);
    if(settings.width) $modal.find('.modal-dialog').css('width', settings.width);
    
    if(typeof settings.content == 'function'){
        settings.content($modal.find('.modal-body'));
    }else{
        if(typeof settings.content == 'object') settings.content = settings.content.html();
        $modal.find('.modal-body').html(settings.content);
    }
    

    if(settings.form){
        if(typeof settings.form == 'string')settings.form = { action:settings.form };
        settings.form = $.extend({method:"post", class:"form-horizontal" }, settings.form);
        $modal.find('.modal-content').wrap('<form class="'+settings.form.class+'" method="'+settings.form.method+'" action="'+settings.form.action+'" autocomplete="off"></form>');
    }
    
    if(settings.footer == null){
        $modal.find('.modal-footer').html('');
    }else if(settings.footer){
        $modal.find('.modal-footer').html(settings.footer);
    }
    
    if(typeof settings.success == 'function'){
        $modal.find('button').click(function(){
            
            $modal.button = $(this).attr('data-button');
            settings.success($modal);
            if(settings.hide) $modal.modal('hide');
        });
    }

    if(settings.event){
        return this.bind(settings.event, function(){ _function(); });
    }else{
        return this.each(function(){ _function(); });
    }

    function _function(){
        
        if(settings.onload){
            if(typeof settings.onload == 'object'){
                $modal.find('form').formEdit(settings.onload);
            }else{
                settings.onload($modal);
            }
        }
        $modal.modal(settings).modal('show'); 
    }

},
    
    
    
});


}));





var user = {
    
    //登录弹窗
    login: function(data, form){
        var options = {modal:'userLogin', form:form, title: '用户登录',event:'', onload:function(modal){ modal.find('form').formEdit(data);}};
        options.content = '<ul>\
                <li><span class="">用户名</span><span class=""><input  name="account" type="text" class="form-control" placeholder="邮箱或手机号"></span></li>\
                <li><span class="">输密码</span><span class=""><input name="password" type="password" class="form-control" placeholder="用户密码"></span></li>\
                <li class="">\
                    <label><input type="checkbox" name="" value="" >记住密码</label>\
                    <label class=""><a href="">忘记密码</a></label>\
                </li></ul>';
    
        $('body').advModal(options, function(modal){
                if(modal.button == 1){
                    modal.find('form').ajaxForm({event:''}, function(data){
                        modal.parents('form').get(0).reset();
                });
            }
        });
        
    }
    
};



