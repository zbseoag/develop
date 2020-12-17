#!/usr/bin/env python3.7
#-*- coding: utf-8 -*-

import redis
import threading

locks = threading.local()
locks.redis = {}


def key_for(user_id):
    return "account_{}".format(user_id)


def _lock(client, key):
    return bool(client.set(key, 'True', nx=True, ex=5))


def _unlock(client, key):
    client.delete(key)


def lock(client, user_id):
    key = key_for(user_id)

    # 如果 key 已经存在,则 key 加对应的值加 1
    if key in locks.redis:
        locks.redis[key] += 1
        return True
    ok = _lock(client, key)
    if not ok:
        return False
    locks.redis[key] = 1
    return True


def unlock(client, user_id):
    key = key_for(user_id)
    if key in locks.redis:
        locks.redis[key] -= 1
        if locks.redis[key] <= 0:
            del locks.redis[key]
        return True
    return False


client = redis.Redis('192.168.115.3', password='root', port=6379)

print("lock " + str(lock(client, "codehold")))
print("lock " + str(lock(client, "codehold")))
print("unlock " + str(unlock(client, "codehold")))
print("unlock " + str(unlock(client, "codehold")))


