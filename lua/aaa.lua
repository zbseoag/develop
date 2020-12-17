
    name = KEYS[1]
    argv = ARGV
    local type = redis.call("type", name)

    redis.debug(type)
    if type["ok"] ~= "list" then return name .. " 不是 list 类型" end

    local start = 1
    if tonumber(argv[1]) then start = tonumber(argv[1]) end

    local length = tonumber(redis.call("LLEN", name))
    local stop = length
    if tonumber(argv[2]) then stop = tonumber(argv[2]) end
    
    local step = 1
    if #argv == 3 and  tonumber(argv[3]) then step = tonumber(argv[3]) end

    if step < 0 then
        start = length
        stop = stop + 2
    else 
        start = start + 1
    end

    print("{start:" .. start .. ", stop:" .. stop .. ", step:" .. step .. "}")

    local index = 1
    local result = {}
    for i = start, stop, step do
        result[index] = redis.call("LINDEX", name, i - 1)
        index = index + 1
    end

    return result