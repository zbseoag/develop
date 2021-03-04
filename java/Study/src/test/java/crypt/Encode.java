package crypt;

import org.bouncycastle.jce.provider.BouncyCastleProvider;
import org.junit.jupiter.api.Test;

import javax.crypto.Cipher;
import javax.crypto.KeyGenerator;
import javax.crypto.Mac;
import javax.crypto.SecretKey;
import javax.crypto.spec.SecretKeySpec;
import java.io.UnsupportedEncodingException;
import java.math.BigInteger;
import java.net.URLDecoder;
import java.net.URLEncoder;
import java.nio.charset.StandardCharsets;
import java.security.*;
import java.util.Arrays;
import java.util.Base64;

import static org.junit.jupiter.api.Assertions.*;

public class Encode{

    /**
     * URL编码的目的是把任意文本数据编码为%前缀表示的文本，便于浏览器和服务器处理
     */
    @Test
    public void URLEncoder(){

        {
            String encoded = URLEncoder.encode("中文!", StandardCharsets.UTF_8);
            System.out.println(encoded);

            String decoded = URLDecoder.decode(encoded, StandardCharsets.UTF_8);
            System.out.println(decoded);

        }

    }

    /**
     * Base64编码的目的是把任意二进制数据编码为文本，但编码后数据量会增加1/3
     */
    @Test
    public void Base64(){

        {
            byte[] input = new byte[] { (byte) 0xe4, (byte) 0xb8, (byte) 0xad };
            String b64encoded = Base64.getEncoder().encodeToString(input);
            System.out.println(b64encoded);
        }

        {
            byte[] output = Base64.getDecoder().decode("5Lit");
            System.out.println(Arrays.toString(output)); // [-28, -72, -83]
        }

        {
            byte[] input = new byte[] { (byte) 0xe4, (byte) 0xb8, (byte) 0xad, 0x21 };
            String b64encoded = Base64.getEncoder().encodeToString(input);
            String b64encoded2 = Base64.getEncoder().withoutPadding().encodeToString(input);
            System.out.println(b64encoded);
            System.out.println(b64encoded2);
            byte[] output = Base64.getDecoder().decode(b64encoded2);
            System.out.println(Arrays.toString(output));
        }

        {//标准的Base64编码会出现+、/和=，所以不适合把Base64编码后的字符串放到URL中。一种针对URL的Base64编码可以在URL中使用的Base64编码，它仅仅是把+变成-，/变成_
            byte[] input = new byte[] { 0x01, 0x02, 0x7f, 0x00 };
            String b64encoded = Base64.getUrlEncoder().encodeToString(input);
            System.out.println(b64encoded);
            byte[] output = Base64.getUrlDecoder().decode(b64encoded);
            System.out.println(Arrays.toString(output));
        }

    }

    /**
     * 哈希算法（Hash）又称摘要算法（Digest），对任意一组输入数据进行计算，得到一个固定长度的输出摘要
     * 哈希碰撞:指两个不同的输入得到了相同的输出
     * 碰撞概率的高低关系到哈希算法的安全性，一个安全的哈希算法必须满足：1、碰撞概率低；2、不能猜测输出。
     * 不能猜测输出，是指输入的任意一个bit的变化会造成输出完全不同，这样就很难从输出反推输入，只能依靠暴力穷举。
     * MD5、SHA-1、SHA-256、SHA-512等
     *
     */
    @Test
    public void hash() throws UnsupportedEncodingException, NoSuchAlgorithmException{

        {
            "hello".hashCode();

            // 创建一个MessageDigest实例:
            MessageDigest md = MessageDigest.getInstance("MD5");

            // 反复调用update输入数据:
            md.update("Hello".getBytes("UTF-8"));
            md.update("World".getBytes("UTF-8"));
            byte[] result = md.digest(); // 16 bytes: 68e109f0f40ca72a15e05cc22786f8e6

            System.out.println(new BigInteger(1, result).toString(16));
        }

    }

    @Test
    public void BouncyCastle() throws NoSuchAlgorithmException, UnsupportedEncodingException{

        {
            // 注册BouncyCastle:
            Security.addProvider(new BouncyCastleProvider());
            // 按名称正常调用:
            MessageDigest md = MessageDigest.getInstance("RipeMD160");
            md.update("HelloWorld".getBytes("UTF-8"));
            byte[] result = md.digest();
            System.out.println(new BigInteger(1, result).toString(16));
        }

    }

    /**
     * Hmac本质上就是把key混入摘要的算法。验证此哈希时，除了原始的输入数据，还要提供key
     * 为了保证安全，我们不会自己指定key，而是通过Java标准库的KeyGenerator生成一个安全的随机的key
     */
    @Test
    public void Hmac() throws NoSuchAlgorithmException, InvalidKeyException, UnsupportedEncodingException{

        {
            KeyGenerator keyGen = KeyGenerator.getInstance("HmacMD5");
            SecretKey key = keyGen.generateKey();

            // 打印随机生成的key:
            byte[] skey = key.getEncoded();
            System.out.println(new BigInteger(1, skey).toString(16));

            Mac mac = Mac.getInstance("HmacMD5");
            mac.init(key);
            mac.update("HelloWorld".getBytes("UTF-8"));
            byte[] result = mac.doFinal();

            System.out.println(new BigInteger(1, result).toString(16));

        }

    }


    /**
     * 对称加密
     * 对称加密算法使用同一个密钥进行加密和解密，常用算法有DES、AES和IDEA等；
     * 密钥长度由算法设计决定，AES的密钥长度是128/192/256位；
     * 使用对称加密算法需要指定算法名称、工作模式和填充模式。
     * @throws Exception
     */
    @Test
    public void encode() throws Exception {
        // 原文:
        String message = "Hello, world!";
        System.out.println("Message: " + message);
        // 128位密钥 = 16 bytes Key:
        byte[] key = "1234567890abcdef".getBytes("UTF-8");
        // 加密:
        byte[] data = message.getBytes("UTF-8");
        byte[] encrypted = encrypt(key, data);
        System.out.println("Encrypted: " + Base64.getEncoder().encodeToString(encrypted));
        // 解密:
        byte[] decrypted = decrypt(key, encrypted);
        System.out.println("Decrypted: " + new String(decrypted, "UTF-8"));
    }

    // 加密:
    public static byte[] encrypt(byte[] key, byte[] input) throws GeneralSecurityException{
        Cipher cipher = Cipher.getInstance("AES/ECB/PKCS5Padding");
        SecretKey keySpec = new SecretKeySpec(key, "AES");
        cipher.init(Cipher.ENCRYPT_MODE, keySpec);
        return cipher.doFinal(input);
    }

    // 解密:
    public static byte[] decrypt(byte[] key, byte[] input) throws GeneralSecurityException {
        Cipher cipher = Cipher.getInstance("AES/ECB/PKCS5Padding");
        SecretKey keySpec = new SecretKeySpec(key, "AES");
        cipher.init(Cipher.DECRYPT_MODE, keySpec);
        return cipher.doFinal(input);
    }


}
