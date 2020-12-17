#!/usr/bin/env python3.7
# -*- coding: utf-8 -*-
# time:2019/10/20
import socket

def server():

    s = socket.socket()
    HOST = '127.0.0.1'
    PORT = 6666
    s.bind((HOST, PORT))
    s.listen(5)
    while True:
        c, addr = s.accept()
        print("connect client: ", addr)
        msg = c.recv(1024)
        print("from client: %s" % msg)

        c.send(msg)


if __name__ == '__main__':

    server()

