--nginx变量  

local var = ngx.var

ngx.say("请求方式 : ", ngx.req.get_method(),  " HTTP/", ngx.req.http_version(), "<hr/>")  

ngx.say("pathinfo-var-one : ", var.one, " | ", var[1], "<br/>")
ngx.say("pathinfo-var-two : ", var.two, " | ", var[2], "<br/>") 
ngx.say("ngx.var.host : ", var.host, " | ", var['host'], "<br/>")
ngx.say("<hr/>")  
  
--请求头  
local headers = ngx.req.get_headers() 
ngx.say("请求头 <br/>")  
ngx.say("Host : ", headers["Host"], "<br/>")  
ngx.say("user-agent : ", headers.user_agent, "<br/>")  

ngx.say("遍历 <br/>")  
for k,v in pairs(headers) do  
    if type(v) == "table" then  
        ngx.say(k, " : ", table.concat(v, ","), "<br/>")  
    else  
        ngx.say(k, " : ", v, "<br/>")  
    end  
end  

ngx.say("<hr/>")  


--get请求uri参数  
ngx.say("请求uri参数 : <br/>")  
local uri_args = ngx.req.get_uri_args()
 
for k, v in pairs(uri_args) do  
    if type(v) == "table" then  
        ngx.say(k, " : ", table.concat(v, ", "), "<br/>")  
    else  
        ngx.say(k, ": ", v, "<br/>")  
    end  
end  

ngx.say("<hr/>")  
  

--post请求参数  
ngx.req.read_body()  
ngx.say("post请求参数  : <br/>")  
local post_args = ngx.req.get_post_args()  
for k, v in pairs(post_args) do  
    if type(v) == "table" then  
        ngx.say(k, " : ", table.concat(v, ", "), "<br/>")  
    else  
        ngx.say(k, ": ", v, "<br/>")  
    end  
end  
ngx.say("<hr/>") 



ngx.say("原始的请求头内容 : <br/>",  ngx.req.raw_header(), "<hr/>")  
ngx.say("请求的body内容体 : <br/>", ngx.req.get_body_data(), "<hr/>")  



