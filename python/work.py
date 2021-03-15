#!/usr/bin/env python3
# -*- coding: utf8 -*-

import requests
import sys
import os
from bs4 import BeautifulSoup
from pathlib import Path
import random
import re

def stop(text):
    print(text)
    exit(0)

def run(file):

    print(file)

    soup = BeautifulSoup(open(file), 'html.parser')

    url = soup.select_one('#container > div > div > div.pen-l > p.time > script')['src']
    aid = str(re.match(r'.*&aid=(\d+)', url).group(1))
    mid = str(re.match(r'.*&mid=(\d+)', url).group(1))
    
    description = soup.select_one('head meta[name=description]')['content']
    keywords = soup.select_one('head meta[name=keywords]')['content']
  
    title = soup.select_one('#container > div > div > div.pen-l h1').string

    datetime = soup.select_one('#container > div > div > div.pen-l > p.time > span').string

    click = str(random.randint(1, 100))

    img = soup.select_one('#container > div > div > div.pen-l img')
    if img:
        img = img['src']
    else:
        img = ''

    content = soup.select_one('#container > div > div > div.pen-l')

    content.select_one("div.case_share").decompose();
    content.find("h1").decompose();
    content.select_one("p.time").decompose();

    article = "".join([str(x) for x in content.contents])
    article = article.replace("'", "\\'").replace('"', '\\"');

    typeid = "8"
    sql = "INSERT INTO `aa_archives` (`id`, `typeid`, `typeid2`, `sortrank`, `flag`, `ismake`, `channel`, `arcrank`, `click`, `money`, `title`, `shorttitle`, `color`, `writer`, `source`, `litpic`, `pubdate`, `senddate`, `mid`, `keywords`, `lastpost`, `scores`, `goodpost`, `badpost`, `voteid`, `notpost`, `description`, `filename`, `dutyadmin`, `tackid`, `mtype`, `weight`) VALUES ("+aid+", "+typeid+", '0', "+datetime+", 'p', 1, 1, 0, "+click+", 0, '"+title+"', '', '', 'admin', '未知', '"+img+"', "+datetime+", "+datetime+", "+mid+", '"+keywords+"', 0, 0, 0, 0, 0, 1, '"+description+"', '', 1, 0, 0, 0);"
    
    sql2 = "INSERT INTO `aa_addonarticle` ( `aid`, `typeid`, `body`, `redirecturl`, `templet`, `userip` ) VALUES( "+aid+", "+typeid+", '"+article+"', '', '', '127.0.0.1' );"

    #print(sql)
    #stop(sql2)

    with open("aaa.sql", "a") as file:
        file.write(sql +"\n" + sql2 + "\n")


# 遍历文件夹
def list_files(file, look=False):

    for root, dirs, files in os.walk(file):

        for f in files:
            yield os.path.join(root, f)


dir  = "/c/Users/admin/Documents/263EM/zhengbaoshan@hotniao.com/receive_file/news/pnews"
for file in list_files(dir):
    run(file)

