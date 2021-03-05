package com.chayu.cms.controller;

import com.chayu.cms.entity.UserInfo;
import com.chayu.cms.service.UserService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.servlet.ModelAndView;

import java.util.List;

@RestController
@RequestMapping("/user")
public class LoginController {

    @Autowired
    private UserService userService;

    @RequestMapping("/login")
    public List<UserInfo> getUser(){
        return userService.getAllUser();
    }

    @RequestMapping("/toLogin")
    public ModelAndView toLogin(){
        ModelAndView view = new ModelAndView();
        view.setViewName("login");
        return view;
    }
}
