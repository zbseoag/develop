#!/usr/bin/env python
# -*- coding: utf8 -*-


    #PY_PYTHON=3
    #默认库将驻留在 C:\Python\Lib\ 中，第三方模块存储在 C:\Python\Lib\site-packages\
    #PYTHONPATH

# 数学计算

# /   除运算返回浮点数
# //  舍去法取整
# %   取余数
# **  双星号是乘方
# 表达式被赋值给变量 _


# a = 32
# b = 3
# c = a * b

# Decimal  小数
# Fraction 分数
# complex 复数


# r'C:\some\name' 字符串前面加 "r" 原样输出


# 三引号可以包含多行字符块,也包含换行,如果不想包含换行,加 "\"
# print("""\
# Usage: thingy [OPTIONS]
#      -h                        Display this usage message
#      -H hostname               Hostname to connect to
# """)

#字符串拼接用加号,字符串乘数表重复
a =  'a' + 'bc' * 3
print(a)

#自动拼接
b = 'abc' 'def'
('abc'
'def')

字符串中的单个字符可以通过索引访问,负值索引表倒数位置,由于 -0 和 0 是一样的,表示第一位,所以负数索引从 -1 开始
word[-1]


开始总是被包括在结果中，而结束不被包括。这使得 s[:i] + s[i:] 总是等于 s
从索引2到5,即:第三位到第五位, 索引5表示第六位
word[2:5]
倒数-1表示倒数第一,即负几表示倒数第几
word[-2:]

切片中的越界索引会被自动处理,不会报错
word[4:42]

字符串不能被修改,向字符串某个索引位置赋值

支持相加合并操作
squares + [36, 49, 64, 81, 100]
list.append(216)
list[2:5] = ['C', 'D', 'E']
letters[2:5] = [] #清空第三到第五个元素





