"""

"""
import tempfile
import os
import time

p = print

def stop(one, *args):
    if(args):
        print(one, args)
    else:
        print(one)
    exit()

def look(obj):
    stop(type(obj))

    
def log(*args):

    file = tempfile.gettempdir() + os.sep + "debug.log"
    date = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
    with open(file, 'a+', encoding='utf-8') as fp:
        count = len(args)
        for content in args:
            if not isinstance(content, str): content = str(content)
            fp.write(content + '\n-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------\n')

        fp.write('==============================================================================' + date + '==============================================================================\n')
        fp.write('-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------\n')

    p('写入日志：'+ file +'\n')


