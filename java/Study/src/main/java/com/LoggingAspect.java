package com;
import org.aspectj.lang.ProceedingJoinPoint;
import org.aspectj.lang.annotation.Around;
import org.aspectj.lang.annotation.Aspect;
import org.aspectj.lang.annotation.Before;
import org.springframework.stereotype.Component;

/**
 * 拦截器类型
 * @Before：这种拦截器先执行拦截代码，再执行目标代码。如果拦截器抛异常，那么目标代码就不执行了；
 * @After：这种拦截器先执行目标代码，再执行拦截器代码。无论目标代码是否抛异常，拦截器代码都会执行；
 * @AfterReturning：和@After不同的是，只有当目标代码正常返回时，才执行拦截器代码；
 * @AfterThrowing：和@After不同的是，只有当目标代码抛出了异常时，才执行拦截器代码；
 * @Around：能完全控制目标代码是否执行，并可以在执行前后、抛异常后执行任意拦截代码，可以说是包含了上面所有功能。
 */
@Aspect
@Component
public class LoggingAspect {
    @Before("execution(public * com.service.UserService.*(..))")
    public void doAccessCheck() {
        System.err.println("[AOP日志] do access check...");
    }

    @Around("execution(public * com.service.MailService.*(..))")
    public Object doLogging(ProceedingJoinPoint pjp) throws Throwable {
        System.err.println("[AOP日志 Around] start " + pjp.getSignature());
        Object retVal = pjp.proceed();
        System.err.println("[AOP日志 Around] done " + pjp.getSignature());
        return retVal;
    }
}