package com.chayu.cms.service;

import com.chayu.cms.entity.UserInfo;
import com.chayu.cms.mapper.UserInfoMapper;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.List;

@Service
public class UserService {

    @Autowired
    private UserInfoMapper userInfoMapper;

    public List<UserInfo> getAllUser(){
        return userInfoMapper.queryUser();
    }
}
