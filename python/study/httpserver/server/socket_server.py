#!/usr/bin/env python3.7
# -*- coding: utf-8 -*-
# time:2019/10/20
import socket
import threading

class TCPServer:

    def __init__(self, address, hander):
        self.address = address
        self.Hander = hander
        self.socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        self.is_close = False

    #开启服务
    def open(self):
        self.socket.bind(self.address)
        self.socket.listen(10)

        while not self.is_close:
            request, client_address = self.receive()

            try:
                self.process_request_multithread(request, client_address)
            except Exception as e:
                print(e)


    #接收请求
    def receive(self):
        return self.socket.accept()

    #处理请求
    def handle(self, request, client_address):
        handler = self.Hander(self, request, client_address)
        handler.handle()
        # 释放连接
        self.release(request)


    #多线程处理请求
    def process_request_multithread(self, request, client_address):
        t = threading.Thread(target=self.handle, args=(request, client_address))
        t.start()


    #释放连接
    def release(self, request):
        request.shutdown(socket.SHUT_WR)
        request.close()

    #关闭服务
    def close(self):
        self.is_close = True


