package com.course.testng;

import org.testng.annotations.DataProvider;
import org.testng.annotations.Test;

import java.lang.reflect.Method;

public class DataProviderTest {

    @DataProvider(name="user_list")
    public Object[][] providerData(){
        return new Object[][]{{"zhangsan",10}, {"lisi",20}, {"wangwu",30}};
    }

    @Test(dataProvider = "user_list")
    public void testDataProvider(String name,int age){

        System.out.println("name: " + name +",\t\t age: " + age);
    }


    @DataProvider(name="methodData")
    public Object[][] methodDataTest(Method method){

        Object[][] data = null;

        if(method.getName().equals("test1")){

            data = new Object[][]{{"zhangsan",20}, {"lisi",25}};
        }else if(method.getName().equals("test2")){
            data = new Object[][]{{"wangwu",50}, {"zhaoliu",60}};
        }

        return data;

    }

    @Test(dataProvider = "methodData")
    public void test1(String name,int age){
        System.out.println("name="+name+";age="+age);
    }

    @Test(dataProvider = "methodData")
    public void test2(String name,int age){

        System.out.println("name:"+name+", age:"+age);
    }






}
