package com.chayu.cms;

import org.mybatis.spring.annotation.MapperScan;
import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;

/**
 * 启动类
 * mapperScan自动扫描mapper
 */
@SpringBootApplication
@MapperScan("com.chayu.Cms.mapper")
public class CmsApplication {
	public static void main(String[] args) {
		SpringApplication.run(CmsApplication.class, args);
	}

}


