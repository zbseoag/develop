#!/usr/bin/env python3.7
# -*- coding: utf-8 -*-
# time:2019/10/20

from httpserver.server.socket_server import TCPServer
from httpserver.handler.base_handler import StreamHandler
import threading
import socket
import time

class TestBaseRequestHandler(StreamHandler):

    def handle(self):
        msg = self.readline()
        print('Server recv msg: '+ msg)
        time.sleep(1)
        self.write(msg)
        self.send()

class SocketServerTest:

    def run_server(self):
        tcp_server = TCPServer(('127.0.0.1', 8888), TestBaseRequestHandler)
        tcp_server.open()


    #生成客户端
    def get_clients(self, num):
        clients = []
        for i in range(num):
            client_thread = threading.Thread(target=self.client_connect)
            clients.append(client_thread)

        return clients

    def client_connect(self):
        client = socket.socket()
        client.connect(('127.0.0.1', 8888))
        client.send(b'Hello Tcpserver\n')
        msg = client.recv(1024)
        print('client recv msg: ' + msg.decode())

    def run(self):
        server_thread = threading.Thread(target=self.run_server)
        server_thread.start()

        clients = self.get_clients(10)
        for client in clients:
            client.start()

        server_thread.join()
        for client in clients:
            client.join()

if __name__ == '__main__':
    SocketServerTest().run()