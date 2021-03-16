import unittest

class MyTestCase(unittest.TestCase):

    def test_input(self):
        a = 12
        print("a")

    def test_input2(self):

        print('%2d %05d' % (5, 1)) # %05.2d 其中 0:带前导0, 5:占5位, .2:保留两位小数
        'Hello, {0}, 成绩提升了 {1:.1f}%'.format('小明', 17.125)

        name = "tom"
        age = 20
        print(f'Student {name} age is  {age:.2f}')
        print('中文'.encode('gb2312'))

        alist = ['Michael', 'Bob', 'Tracy']
        alist.append("a")
        print(alist)

        empty_tuple = ()
        atuple = ('Michael', 'Bob', 'Tracy')
        one_tuple = (1,)

        int("12")
        str(12)
        bool(3)
        float(3)
        sums = 0
        for x in [1,2,3]:
            sums += x
        list(range(5))



    def test_dict(self):

        adict = {'Michael': 95, 'Bob': 75, 'Tracy': 85}
        adict.get('bob', 0)

        #集合
        s1 = set([1, 2, 3])



    def test_22(self):

        x = 3
        if not isinstance(x, (int, float)):
            raise TypeError('bad operand type')
        return 10, 23, 12


    #参数定义顺序必须是：必选参数、默认参数、可变参数、命名关键字参数和关键字参数。
    #def person(name, args=2): #默认参数
    #def person(name, args*): #可变参数
    #def person(name, kw**): #关键字参数
    #def person(name, age, *, kw1, kw2): #命名关键字参数,限制关键字参数的名字

#计算阶乘
#递归函数的优点是定义简单，逻辑清晰
#理论上，所有的递归函数都可以写成循环的方式，但循环的逻辑不如递归清晰
def fact(n):
    if n==1:
        return 1
    return n * fact(n - 1)

#在计算机中，函数调用是通过栈（stack）这种数据结构实现的，每当进入一个函数调用，栈就会加一层栈帧，每当函数返回，栈就会减一层栈帧。
#由于栈的大小不是无限的，递归调用的次数过多，会导致栈溢出。
#解决递归调用栈溢出的方法是通过尾递归优化，事实上尾递归和循环的效果是一样的，所以，把循环看成是一种特殊的尾递归函数也是可以的。
#尾递归是指，在函数返回的时候，调用自身本身，并且 return 语句不能包含表达式。
#编译器或者解释器就可以把尾递归做优化，使递归本身无论调用多少次，都只占用一个栈帧，不会出现栈溢出的情况。

#尾递归版
#stage 保存上一次的结果,再传到下一次调用中
def fact2(n, stage=1):
    if n == 1:
        return stage
    return fact2(n - 1, n * stage)

fact2(5)

#import builtins; builtins.abs = 10


from functools import reduce

DIGITS = {'0': 0, '1': 1, '2': 2, '3': 3, '4': 4, '5': 5, '6': 6, '7': 7, '8': 8, '9': 9}

def char2num(s):
    return DIGITS[s]

def str2int(s):
    return reduce(lambda x, y: x * 10 + y, map(char2num, s))

def is_odd(n):
    return n % 2 == 1

list(filter(is_odd, [1, 2, 4, 5, 6, 9, 10, 15]))

sorted(['bob', 'about', 'Zoo', 'Credit'], key=str.lower, reverse=True)

def count():
    def f(j):
        def g():
            return j*j
        return g
    fs = []
    for i in range(1, 4):
        fs.append(f(i)) # f(i)立刻被执行，因此i的当前值被传入f()
    return fs

f1, f2, f3 = count()
f3()

def log(func):

    def wrapper(*args, **kw):
        print('call %s():' % func.__name__)
        return func(*args, **kw)
    return wrapper


@log
def now():
    print('2015-3-25')

now()


def log2(text):
    def decorator(func):
        def wrapper(*args, **kw):
            print('%s %s():' % (text, func.__name__))
            return func(*args, **kw)
        return wrapper
    return decorator

@log2('函数执行日志:')
def now2():
    print('2015-3-25')

now2()


import functools

def log3(text):
    def decorator(func):
        @functools.wraps(func) #把原始函数的__name__等属性复制到wrapper()函数中
        def wrapper(*args, **kw):
            print('%s %s():' % (text, func.__name__))
            return func(*args, **kw)
        return wrapper
    return decorator

#偏函数
import functools
int2 = functools.partial(int, base=2)

#sys.path
#sys.path.append('/Users/michael/my_py_scripts')

class Student(object):

    score = 0 #类属性,通过类名访问
     __slots__ = ('name', 'age') # 用tuple定义允许绑定的属性名称,若子类中也定义slots，则子类允许定义的属性就是自身的slots加上父类的slots,否则父类 slots无效

    def __init__(this, name, age):
        this.name = name
        this.age = age
        Student.score = 100

    def print(this):
        print("%s : %d" % (this.name, this.age))

    #可以通过设置或获取属性值,来访问相应的方法
    @property
    def score(self):
        return self._score

    @score.setter #不定义setter方法就是一个只读属性
    def score(self, value):
        if not isinstance(value, int):
            raise ValueError('score must be an integer!')
        if value < 0 or value > 100:
            raise ValueError('score must between 0 ~ 100!')
        self._score = value

    #toString 方法
    def __str__(self):
        return 'Student object (name: %s)' % self.name
    #返回报告
    def __repr__(self):
        return 'Student object (name: %s)' % self.name

    def __iter__(self):
        return self # 实例本身就是迭代对象，故返回自己

    def __next__(self):
        self.a, self.b = self.b, self.a + self.b # 计算下一个值
        if self.a > 100000: # 退出循环的条件
            raise StopIteration()
        return self.a # 返回下一个值

    #通过下标访问元素
    def __getitem__(self, n):
        if isinstance(n, int): # n是索引
            a, b = 1, 1
            for x in range(n):
                a, b = b, a + b
            return a
        if isinstance(n, slice): # n是切片
            start = n.start
            stop = n.stop
            if start is None:
                start = 0
            a, b = 1, 1
            L = []
            for x in range(stop):
                if x >= start:
                    L.append(a)
                a, b = b, a + b
            return L

    #访问不存在的属性
    def __getattr__(self, attr):
        if attr=='score':
            return 99
    #如果当函数调用时
    def __call__(self):
        print('My name is %s.' % self.name)

student = Student("tom", 44)
student.print()
print(student.score, Student.score)

print(type('abc') == str)
#callable(obj)
# import types
# type(fn) == types.FunctionType
# type(abs) == types.BuiltinFunctionType
# type(lambda x: x) == types.LambdaType
# type((x for x in range(10))) == types.GeneratorType

isinstance((1, 2, 3), (list, tuple))
dir('ABC') #返回一个包含字符串的list，包含对象的所有属性和方法
# getattr(obj, attr, default)
# setattr(obj, attr)
# hasattr(obj, attr)


from types import MethodType
def set_age(self, age):
    self.age = age

s.set_age = MethodType(set_age, s) # 给实例绑定一个方法
Student.setScore = set_score #给类绑定方法

class MyTCPServer(TCPServer, CoroutineMixIn):
    pass


from enum import Enum
Month = Enum('Month', ('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'))
for name, member in Month.__members__.items():
    print(name, '=>', member, ',', member.value) #Jan => Month.Jan, 1


from enum import Enum, unique
@unique
class Weekday(Enum):
    Sun = 0 # Sun的value被设定为0
    Mon = 1
    Tue = 2


from enum import Enum, unique
@unique
class Weekday(Enum):
    Sun = 0 # Sun的value被设定为0
    Mon = 1
    Tue = 2


def fn(self, name='world'): # 先定义...
    print('Hello, %s.' % name)

Hello = type('Hello', (object,), dict(hello=fn)) # 创建Hello class
h = Hello()

try:
    print('try...')
    r = 10 / int('2')
    print('result:', r)
except ValueError as e:
    print('ValueError:', e)
except ZeroDivisionError as e:
    print('ZeroDivisionError:', e)
else:
    print('no error!')
finally:
    print('finally...')


from io import StringIO
f = StringIO()
f.write('hello')
f.write(' ')
f.write('world!')
print(f.getvalue())

f = StringIO('Hello!\nHi!\nGoodbye!')
while True:
    s = f.readline()
    if s == '':
        break
    print(s.strip())

import os
os.uname()
os.environ.get('PATH')
os.path.abspath('.')
os.mkdir('/Users/michael/testdir')
os.rmdir('/Users/michael/testdir')
os.path.join('/Users/michael', 'testdir')
os.path.split('/Users/michael/testdir/file.txt')
os.path.splitext('/path/to/file.txt')
os.rename('test.txt', 'test.py')
os.remove('test.py')

[x for x in os.listdir('.') if os.path.isfile(x) and os.path.splitext(x)[1]=='.py']

#序列化
import pickle
d = dict(name='Bob', age=20, score=88)
pickle.dumps(d)
pickle.dump(d, open('dump.txt', 'wb'))
d2 = pickle.load(open('dump.txt', 'rb'))
print(d2)

import json
json.dumps(dict(name='Bob', age=20, score=88))

#json.dumps(obj, default=obj2dict)



# pip install mysql-connector
import mysql.connector

conn = mysql.connector.connect(host="127.0.0.1", user='root', password='123456', database='test', auth_plugin='mysql_native_password')
cursor = conn.cursor()

cursor.execute('insert into user(name) values (%s)', ['Michael888'])
conn.commit()

cursor.execute('select * from user where id = %s', (1,))
values = cursor.fetchall()
print(values)


cursor.close()
conn.close()