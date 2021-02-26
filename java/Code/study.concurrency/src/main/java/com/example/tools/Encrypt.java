package tools;

import java.math.BigInteger;
import java.security.MessageDigest;

public class Encrypt {


    public static String md5(String str) throws Exception {

        return md5(str, false);
    }


    /**
     * md5加密
     * @param str 字符串
     * @param raw 是否返回原始数据
     * @return String 加密后的结果
     * @throws Exception
     */
    public static String md5(String str, Boolean raw) throws Exception {

        MessageDigest m = MessageDigest.getInstance("MD5");
        m.update(str.getBytes());

        return raw ? m.digest().toString() : new BigInteger(1, m.digest()).toString(16);
    }

}


