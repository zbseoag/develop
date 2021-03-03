package com.annotation;

import org.aspectj.lang.ProceedingJoinPoint;
import org.aspectj.lang.annotation.Around;
import org.aspectj.lang.annotation.Aspect;
import org.springframework.stereotype.Component;

@Aspect
@Component
public class MetricAspect {

    @Around("@annotation(metricTime)")
    public Object metric(ProceedingJoinPoint joinPoint, MetricTime metricTime) throws Throwable {

        String name = metricTime.value();
        long start = System.currentTimeMillis();

        try {
            return joinPoint.proceed();

        } finally {

            long t = System.currentTimeMillis() - start;
            System.err.println("[AOP 注解方式] " + name + ": " + t + "ms");

        }
    }
}
