package com.chayu.cms.mapper;

import com.chayu.cms.entity.UserInfo;

import java.util.List;

/**
 * mapper注解可以在这里直接写SQL
 */
public interface UserInfoMapper {

    List<UserInfo> queryUser();

}
