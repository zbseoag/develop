package com.learnjava;

import com.learnjava.service.User;
import com.learnjava.service.UserService;
import com.zaxxer.hikari.HikariConfig;
import com.zaxxer.hikari.HikariDataSource;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Qualifier;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.context.ApplicationContext;
import org.springframework.context.ConfigurableApplicationContext;
import org.springframework.context.annotation.*;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.datasource.DataSourceTransactionManager;
import org.springframework.transaction.PlatformTransactionManager;
import org.springframework.transaction.annotation.EnableTransactionManagement;

import javax.sql.DataSource;
import java.time.ZoneId;

@Configuration
@ComponentScan
@PropertySource("jdbc.properties") // 表示读取classpath的 jdbc.properties
@EnableAspectJAutoProxy
@EnableTransactionManagement
public class AppConfig {

    @Value("${app.zone:Z}") //表示读取key为app.zone的value，但如果key不存在，使用默认值 Z
    String zoneId;


    @Bean
    @Profile("!test") //当前的Profile设置非 test, JVM参数-Dspring.profiles.active=test,master就可以指定以多个环境启动
    ZoneId createZoneIdForTest() {
        return ZoneId.of("America/New_York");
    }

    @Bean
    @Profile({ "test", "master" }) // 同时满足test和master
    ZoneId createZoneId() {
        return ZoneId.of("America/New_York");
    }


    public static void main(String[] args) {

        ApplicationContext context = new AnnotationConfigApplicationContext(AppConfig.class);
        UserService userService = context.getBean(UserService.class);
        userService.register("bob@example.com", "password1", "Bob");
        userService.register("alice@example.com", "password2", "Alice");
        User bob = userService.getUserByName("Bob");
        System.out.println(bob);
        User tom = userService.register("tom@example.com", "password3", "Tom");
        System.out.println(tom);
        System.out.println("Total: " + userService.getUsers());
        for (User u : userService.getUsers(1)) {
            System.out.println(u);
        }

        ((ConfigurableApplicationContext) context).close();

    }

    @Bean
    @Primary
    @Qualifier("z")
    ZoneId createZoneOfZ() {
        return ZoneId.of("Z");
    }

    @Bean
    @Qualifier("utc8")
    ZoneId createZoneOfUTC8() {
        return ZoneId.of("UTC+08:00");
    }



    @Value("${jdbc.url}")
    String jdbcUrl;

    @Value("${jdbc.username}")
    String jdbcUsername;

    @Value("${jdbc.password}")
    String jdbcPassword;

    @Bean
    JdbcTemplate createJdbcTemplate(@Autowired DataSource dataSource) {
        return new JdbcTemplate(dataSource);
    }

    @Bean
    DataSource createDataSource() {

        HikariConfig config = new HikariConfig();
        config.setJdbcUrl(jdbcUrl);
        config.setUsername(jdbcUsername);
        config.setPassword(jdbcPassword);
        config.addDataSourceProperty("autoCommit", "true");
        config.addDataSourceProperty("connectionTimeout", "5");
        config.addDataSourceProperty("idleTimeout", "60");
        return new HikariDataSource(config);

    }

    @Bean
    PlatformTransactionManager createTxManager(@Autowired DataSource dataSource) {
        return new DataSourceTransactionManager(dataSource);
    }

}

