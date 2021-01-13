#!/usr/bin/env python
# -*- coding: UTF-8 -*-
import sys

def hello(a, b, c):
    print(a)


if __name__ == '__main__':
    eval(sys.argv[1])(*sys.argv[2:])

