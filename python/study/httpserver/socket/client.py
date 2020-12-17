#!/usr/bin/env python3.7
# -*- coding: utf-8 -*-
# time:2019/10/20

import socket

def client():
    s = socket.socket()

    HOST = '127.0.0.1'
    PORT = 6666

    s.connect((HOST, PORT))

    s.send(b'Hello World!')
    msg = s.recv(1024)
    print("from server: %s" % msg)

if __name__ == '__main__':

    client()