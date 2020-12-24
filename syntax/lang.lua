#!/usr/bin/env lua

--[[
 多行注释
 --]]

 --lua hello.lua
 --lua -i file.lua
 --dofile('lib1.lua')

 --[[
    打印输出函数,方便调试
 --]]
 p = function(...)

    local cache={}
    local function sub_print_r(t, indent)
        if (cache[tostring(t)]) then
            print(indent.. "*" ..tostring(t))
        else
            cache[tostring(t)]=true
            if (type(t)=="table") then
                for pos,val in pairs(t) do
                    if (type(val)=="table") then

                        print(indent.."["..pos.."] \t= "..tostring(t).." {")

                        sub_print_r(val,indent..string.rep(" ",string.len(pos)+8))

                        print(indent..string.rep(" ",string.len(pos)+6).."}")

                    elseif (type(val)=="string") then

                        print(indent.."[".. pos ..'] \t= "'.. val ..'"')

                    else
                        print(indent.."[".. pos .."] \t= "..tostring(val))
                    end
                end
            else
                print(indent .. tostring(t))
            end
        end
    end

    local output = ''
    for _, val in ipairs({...}) do

        if (type(val)=="table") then

            print(tostring(val) .. "{")
            sub_print_r(val, "  ")
            print("}")
            
        else
            output = output .. tostring(val) .. '  '
        end
    end

    if(output ~= '')then
        print(output)
    end

end




function chapter1()
    p('传递脚本的参数：', arg)

    type(nil)
    io.flush()
    os.exit()
end

function chapter2()

    p(math.type(2), math.type(2.0)) --具体的数字类型 integer float
    p(4 // 3)
    p(1 ~= 1.0)
    p(math.huge, math.mininteger, math.maxinteger)
    math.randomseed(os.time())
    p(math.random(6))
    p(math.floor(3.4), math.floor(-3.4))
    p(math.ceil(3.4), math.ceil(-3.4))
    p(math.modf(3.2), math.modf(-3.2))
    p(2^12 | 0)
    p(math.tointeger(2^10))

    local function toint(x)
        return math.tointeger(x) or x
    end

    for i = 1, 10 do
        p(i, i % 3)
    end

    p(string.gsub('old-string', 'old', 'new'))
    p(#'abc')

    local html = [===[
        <html>
        <head></head>
        </html>
    ]===]

    p(tonumber(' -5 '), tonumber('f', 16))
    p(string.rep('a', 3))
    p(string.reverse('abc'))
    p(string.upper('abc') > string.upper('def'))
    p(string.sub('123456789', 2, -2), string.sub('123456789', -1, -1))
    p(string.char(97))
    p(string.byte('abc'), string.byte('abc', -1), '|', string.byte('abc', 1, 2))
    p(string.format('{name: %s age: %d}', 'tom', 20))
    p(string.find('hello world', 'wor'))
    p(utf8.char(114))
    p(utf8.len('中文'))

    local a = { 'a', 'b', 'c'}
    for k, v in pairs(a) do
        p(k, v)
    end

    for k, v in ipairs(a) do
        p(k, v)
    end

    for i = 1, #a do
        p(a[i])
    end
    
    a[#a] = nil -- 移除最后一个元素
    a[#a + 1] = 'append'
 
    local a = { b = { c = { d = 'end' } }}
    p(a.b.c.d)
    local d = (((a or {}).b or {}).c or {}).d --安全访问操作符的变通方式

    local a = {1, 2, 3}
    table.insert(a, 4)
    table.insert(a, 1, 'A')
    p(a) 
    table.remove(a)
    table.remove(a, 1)
    p(a)

    start = 1; ends = #a; newpos = 2;
    table.move(a, start, ends, newpos)
    a[1] = 'first'

    p(os.date())

    local function make() return 'a', 'b', 'c'; end
    local a = { make() }
    p((make()))

    local function foo(...)
        local all = {...}
        local a, b, c = ...
        local args = table.pack(...)
        p(args.n)
        return ...
    end

    start = 3
    p(select(start, 1, 2, 3, 4))
    p(select('#', 1, 2, 3, 4)) --返回参数总数
    
    local function sum(...)
        local sum = 0
        for i = 1, select('#', ...) do
            sum = sum + select(i, ...)
        end
        return sum
    end

    p(sum(1, 2, 3, 4, 5))
    p(table.unpack(a))
    p(table.unpack(a, 2, 3))
    p(table.pack('a', 'b', 'c'))
    
end


--chapter1()
chapter2()


