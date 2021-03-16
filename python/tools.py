#!/usr/bin/env python3
# -*- coding: utf8 -*-
import requests
from bs4 import BeautifulSoup
import tempfile
import os
import time

def stop(*args):
    print(*args)
    exit(0)

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

    print('写入日志：'+ file +'\n')


def connectMongo(collection='test', document='test'):
    import pymongo
    client = pymongo.MongoClient('localhost', 27017)
    collection = client[collection]
    document = collection[document]
    return document

def get(url, selector=None, debug=False, params=None, **kwargs):

    kwargs.setdefault('allow_redirects', True)
    content = requests.request('get', url, params=params, **kwargs)
    content.encoding = 'utf8'

    if(debug): log(content.text)

    result = {}
    if isinstance(selector, str):

        result = BeautifulSoup(content.text, 'html.parser').select(selector)
    elif isinstance(selector, dict):

       for k, v in selector.items():
           result[k] = BeautifulSoup(content.text, 'html.parser').select(v)
    else:
        result = content.text

    return result


