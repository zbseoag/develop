#!/usr/bin/env python3
# -*- coding: utf8 -*-

import requests
import sys
import os
from bs4 import BeautifulSoup

url = sys.argv[1]
path = sys.argv[2]

if url.isspace():
    print('url 地址为空!')
    exit()

if not path.isspace():
    if not os.path.exists(path):
        print('新建目录:' + path)
        os.mkdir(path)
else:
    path = '.'
    
html = requests.request('get', url)
html.encoding = 'utf8'
html = html.text

soup = BeautifulSoup(html, 'html.parser')
files = soup.select('div[aria-labelledby="files"] div[role="rowheader"] a')
files.pop(0)

#生成 url 地址，类似于: https://raw.githubusercontent.com/docker-library/php/master/8.0/buster/fpm
url = url.replace("github.com", "raw.githubusercontent.com")
url = url.replace("/tree/", "/")

for name in files:
    name = name.string

    print("文件" + name + " :" + url + "/" + name)
    content = requests.request('get', url + "/" + name)
    content.encoding = 'utf8'
    content = content.text

    with open(path + '/' + name, 'w', encoding='utf-8') as fp:
        fp.write(content)

