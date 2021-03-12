package com.course.testng;
import org.testng.annotations.*;

public class Basic {

    @BeforeSuite
    public void beforeSuite(){
        System.out.println("Before Suite 在测试之前");
    }

    @BeforeClass
    public void beforeClass(){
        System.out.println("Before Class 在类运行之前");
    }

    @BeforeMethod
    public void beforeMethod(){
        System.out.println("Before Method");
    }

    @Test
    public void testCase1(){
        System.out.println("Test 测试用例1");
    }

    @Test(enabled = false)
    public void testCase2(){
        System.out.println("Test 测试用例2");
    }


    @Test(expectedExceptions = RuntimeException.class, dependsOnMethods = {"testCase1"}, timeOut = 2000)
    public void testCase3(){
        System.out.println("testCase3");
        throw new RuntimeException();
    }


    /**
     * 通过 Paramter.xml 提供参数
     */
    @Test
    @Parameters({"name","age"})
    public void paramTest1(String name,int age){
        System.out.println("name = " + name + "; age = " + age);
    }


    @Test(invocationCount = 10, threadPoolSize = 3)
    public void testMultiThread(){

        System.out.printf("Thread Id : %s%n",Thread.currentThread().getId());

    }

}
