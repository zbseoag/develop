package com.learnjava.service;

import com.learnjava.metrics.MetricTime;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Component;

import javax.annotation.PostConstruct;
import javax.annotation.PreDestroy;
import java.time.ZoneId;
import java.time.ZonedDateTime;
import java.time.format.DateTimeFormatter;

@Component
public class MailService {

	@Value("#{smtpConfig.host}") //从名称为smtpConfig的Bean读取 host属性，即调用getHost()方法。
	private String smtpHost;

	@Autowired(required = false)
	public ZoneId zoneId = ZoneId.systemDefault();

	public void setZoneId(ZoneId zoneId) {
		this.zoneId = zoneId;
	}

	@PostConstruct
	public void init() {
		System.out.println("Init mail service with zoneId = " + this.zoneId);
	}

	@PreDestroy
	public void shutdown() {
		System.out.println("Shutdown mail service");
	}

	public String getTime() {
		return ZonedDateTime.now(this.zoneId).format(DateTimeFormatter.ISO_ZONED_DATE_TIME);
	}

	@MetricTime("loginMail")
	public void sendLoginMail(User user) {
		System.err.println(String.format("Hi, %s! You are logged in at %s", user.getName(), getTime()));
	}

	public void sendRegistrationMail(User user) {
		System.err.println(String.format("Welcome, %s!", user.getName()));

	}

}
