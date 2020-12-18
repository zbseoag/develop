#!/usr/bin/env python3.7
# -*- coding: utf-8 -*-
# time:2019/10/20

from httpserver.handler.base_handler import  StreamHandler
import logging

logging.basicConfig(level=logging.DEBUG)

class BaseHTTPRequestHandler(StreamHandler):

    def __init__(self, server, request, client_address):
        self.method = None
        self.path = None
        self.version = None
        self.headers = None
        self.body = None
        StreamHandler.__init__(self, server, request, client_address)

    def handle(self):
        try:
            if not self.parse_request():
                return
            method_name = 'do_' + self.method

            if not hasattr(self, method_name):
                return
            method = getattr(self, method_name)
            method()
            self.send()
        except Exception as e:
            logging.exception(e)

    def parse_request(self):
        first_line = self.readline()
        words = first_line.split()

        self.method, self.path, self.version = words
        self.headers = self.parse_headers()

        #解析请求内容
        key = 'Content-Length'
        if key in self.headers.keys():
            body_length = int(self.headers[key])
            self.body = self.read(body_length)

        return True


    def parse_headers(self):
        headers = {}
        while True:
            line = self.readline()
            if line:
                key, value = line.split(":", 1)
                key = key.strip()
                value = value.strip()
                headers[key] = value
            else:
                break
        return headers

    def write_header(self, key, value):
        msg = '%s : %s' % (key, value)
        self.write(msg)




