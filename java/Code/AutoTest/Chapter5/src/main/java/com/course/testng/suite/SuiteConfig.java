package com.course.testng.suite;

import org.testng.annotations.*;

/**
 * 套件测试
 * 查看 suite.xml
 * 右键 suite.xml 运行
 */
public class SuiteConfig {

    @BeforeSuite
    public void beforeSuite(){
        System.out.println("before suite 运行啦");
    }

    @AfterSuite
    public  void aftersuite(){
        System.out.println("after suite 运行啦");
    }

    @BeforeTest
    public void beforeTest(){
        System.out.println("BeforeTest");
    }

    @AfterTest
    public void afterTest(){
        System.out.println("AfterTest");
    }
}
