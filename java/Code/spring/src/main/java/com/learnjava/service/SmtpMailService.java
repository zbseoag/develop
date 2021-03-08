package com.learnjava.service;

import com.learnjava.condition.OnSmtpEnvCondition;
import org.springframework.context.annotation.Conditional;
import org.springframework.stereotype.Component;

@Component
@Conditional(OnSmtpEnvCondition.class) //如果满足OnSmtpEnvCondition的条件，才会创建 SmtpMailService 这个 Bean
public class SmtpMailService  {

    public void sendMail(String address, String subject, String body) {
        System.out.println("Send mail to " + address + " using SMTP:");
        System.out.println("Subject: " + subject);
        System.out.println("Body: " + body);

    }
}
