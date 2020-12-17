#!/usr/bin/env python3
# -*- coding: utf8 -*-
import requests
from bs4 import BeautifulSoup


def connect(collection='test', document='test'):
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

def search(content, selector):
    return BeautifulSoup(content.text, 'html.parser').select(selector)