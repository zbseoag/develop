<?php
//Swoole\Coroutine::create 等价于go函数
//Coroutine\Channel 可以简写为chan
//Swoole\Coroutine::defer可以直接用defer



function aaa($s){

    $len = strlen($s);
    if($len % 2 == 1) return false;

    $pairs = [
        ')' => '(',
        ']' => '[',
        '}' => '{',
        '>' => '<',
    ];
    $stack = new SplStack();

    for($i = 0; $i < $len; $i++){

        $char = $s[$i];
        if(empty($char)) continue;

        //如果遇到右括号
        if(key_exists($char, $pairs)){

            //如果当前队列是空的,以右括号开始不合法。如果栈顶的左括号与当前正规的右括号不匹配
            if($stack->isEmpty() || $stack->top() != $pairs[$char]) {
                return false;
            }
            //若能匹配，则弹出
            $stack->pop();
        }else{
            //括号入栈
            $stack->push($char);
        }
    }
    return $stack->isEmpty();

}

p(aaa('({[((({})))]})'));
p(aaa('({})'));
