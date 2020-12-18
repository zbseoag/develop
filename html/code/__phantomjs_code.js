
var system = require('system');

system.args;
system.args.forEach(function(value, index){});
system.env.hasOwnProperty('OS');

phantom.outputEncoding="utf8";

phantom.exit();
phantom.setProxy(host, port, 'manual', '', '');//设置代理



var page = require('webpage').create();
page.viewportSize = {width: 400, height: 400};//页面大小
page.zoomFactor = 0.25; //页面放大系数
page.plainText
page.content = '<html><body><canvas id="surface"></canvas></body></html>';
page.evaluate(function(){});

var settings = {
    operation: "POST",
    encoding: "utf8",
    headers: {
        "Content-Type": "application/json"
    },
    data: JSON.stringify({
        some: "data",
        another: ["custom", "data"]
    })
};
page.open(url, ['post', data] = settings, function(status) { if(status === 'success') 'ok'; });

//onConsoleMessage方法监听事件
page.onConsoleMessage = function(msg) {
    console.log('Page title is ' + msg);
};
page.open(url, function(status) {
    page.evaluate(function() {
        console.log(document.title);
    });
    phantom.exit();
});

//包含其他文件
page.includeJs("http://path/to/jquery.min.js", function() {
    page.evaluate(function() {
        $("button").click();
    });
    phantom.exit()
});


//当页面请求资源时触发事件
page.onResourceRequested = function(data, request){
    
    //如果是加载CSS文件，就中断连接
    if((/http:\/\/.+?\.css/gi).test(data['url']) || data.headers['Content-Type'] == 'text/css'){
        console.log(data['url']);
        request.abort();
    }
    console.log(JSON.stringify(data));
    console.log(JSON.stringify(request));
};




//当收到响应的资源时触发事件
page.onResourceReceived = function(response) {
    console.log(JSON.stringify(response));
};

page.render('google_home.jpg', {format: 'jpg', quality: '100'});

fs.write(filename, content, 'w');


//当window.callPhantom(msg) 调用时触发
page.onCallback = function(msg){
    return "消息: " + msg;
};


page.includeJs("http://jquery.js", function() {
    page.evaluate(function() {
        console.log($(".explanation").text());
    });
});



var webserver = require('webserver');
var server = webserver.create();
server.listen('127.0.0.1:8080', {'keepAlive': true}, function(request, response){
    response.statusCode = 200;
    response.write('<html><body>Hello!</body></html>');
    response.close();
});



