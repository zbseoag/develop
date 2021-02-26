package com.example.annotation;

import java.lang.annotation.*;
import java.lang.reflect.Field;
import java.lang.reflect.Method;

public class Main{

    public static void main(String[] args){

        Filter f1 = new Filter();
        f1.setUserName("tom");
        System.out.println(query(f1));
    }


    public static String query(Filter f){

        StringBuffer sb = new StringBuffer();

        Class c = f.getClass();
        if(!c.isAnnotationPresent(Table.class)){
            return null;
        }

        //获取注解对象
        Table table = (Table) c.getAnnotation(Table.class);

        sb.append("select * from ").append(table.value()).append(" where 1=1");

        //获取定义过的字段
        Field[] fields = c.getDeclaredFields();

        for(Field field : fields){

            //首字母转大写
            char[] cs = field.getName().toCharArray();
            cs[0]-=32;
            String getMethod = String.valueOf(cs);

            Object value = null;
            try{
                //拼接成 Getter 方法
                Method getter = c.getMethod("get" + getMethod);
                //调用对象的 Getter 方法
                value = getter.invoke(f);

            }catch(Exception e){
                e.printStackTrace();
            }

            if(value == null) continue;

            //如果是字符串,则放在引号里
            if(value instanceof String) value = "'" + value + "'";

            if(!field.isAnnotationPresent(Column.class)) continue;
            Column column = field.getAnnotation(Column.class);

            sb.append(" and ").append(column.value()).append("=").append(value);
        }

        return sb.toString();
    }


}


/*使用注解*/
@Table("user")
class Filter {

    @Column("id")
    private int id;

    @Column("user_name")
    private String userName;



    public int getId(){
        return id;
    }

    public String getUserName(){
        return userName;
    }

    public void setId(int id){
        this.id = id;
    }

    public void setUserName(String userName){
        this.userName = userName;
    }


}



/*定义注解*/
@Target({ElementType.TYPE})
@Retention(RetentionPolicy.RUNTIME)
@interface Table {

    String value() default "test";
}


/*定义注解*/
@Target({ElementType.FIELD})
@Retention(RetentionPolicy.RUNTIME)
@interface Column {

    String value();
}


