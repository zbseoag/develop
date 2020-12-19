import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.lang.reflect.Array;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Calendar;
import java.util.Collections;
import java.util.Comparator;
import java.util.Date;
import java.util.GregorianCalendar;
import java.util.List;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import static java.lang.System.out;

/*
assert	断言条件是否满足
default
enum	枚举类型
implements	表示一个类实现了接口
instanceof	测试一个对象是否是某个类的实例
native	表示方法用非java代码实现
strictfp	浮点数比较使用严格的规则
synchronized	表示同一时间只能由一个线程访问的代码块
this	表示调用当前实例
throws	定义方法可能抛出的异常
transient	修饰不要序列化的字段
volatile	标记字段可能会被多个线程同时访问，而不做同步
List<? super Number> 类型通配符是 Nubmer 及其 父类
List<? extends Number> 类型通配符是 Nubmer 及其 子类

*/

public class Main extends Tools {
	
	public static byte 		BYTE;
	public static int 		INT;
	public static float 	FLOAT;
	public static char 		CHAR;
	public static String 	STRING;
	
	public static char[] 	CHARS 	= {'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'};
	public static byte[] 	BYTES 	= {65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80};
	public static String[] 	STRINTS = {"AAA", "BBB", "CCC", "DDD", "EEE", "FFF"};
	
	public static File 		FILE 			= new File("d:/data.txt");
	public static String 	INPUT_FILE 		= "d:/input.txt";
	public static String 	OUTPUT_FILE 	= "d:/output.txt";

	public static Date DATE = new Date();
	
	public static void main(String[] args) throws Exception {
		

		//arrayToCollection();
	    

	     String str = "runoob";
	        str.toUpperCase();
	}

	
	protected void finalize() throws Throwable {
		System.gc();
		print("程序结果");
		
	}
	
	public static void reset() {
	
		BYTE 	= 0;
		INT 	= 0;
		FLOAT	= 0.0f;
		CHAR 	= '\u0000';
		STRING 	= null;
	}
	
	

	  // 使用 java 7 排序
	   private void sortUsingJava7(List<String> names){   
	      Collections.sort(names, new Comparator<String>() {
	         @Override
	         public int compare(String s1, String s2) {
	            return s1.compareTo(s2);
	         }
	      });
	   }
	   
	   // 使用 java 8 排序
	   private void sortUsingJava8(List<String> names){
	      Collections.sort(names, (s1, s2) -> s1.compareTo(s2));
	   }
	   
	   
	
	
	public static void base() throws ParseException, IOException {
		
		char ch1 = '\u039a';
		char[] ch2 = {'a', 'b', 'c'};
		Character ch3 = 'A';
		

		print(Character.hashCode('a'));
		
		char[] carr = {'h', 'e' , 'l'};
		String str = new String(carr);
		str.concat("append");

		String fs = String.format("%f %d %s", 12.0, 18, "tom");
		
		String[] arr = new String[0];
		String[] arr2 = {};
		int[] iarr = new int[10];
		iarr[0] = 1;
		iarr[1] = 2;
		print(iarr);
		

		print((new SimpleDateFormat("E yyyy.MM.dd 'at' hh:mm:ss a zzz")).format(DATE));
	    print(String.format("%tc", DATE));
	    printf("%1$s %2$tB %2$td, %2$tY", "printf:", DATE);

	    
	    SimpleDateFormat ft = new SimpleDateFormat ("yyyy-MM-dd");
	    print(ft.parse("2020-07-06 12:02:14")); 
	    
	    long start = System.currentTimeMillis();

	    Calendar c1 = Calendar.getInstance();
	    
	    c1.set(2009, 6 - 1, 12);//把Calendar对象c1的年月日分别设这为：2009、6、12
	    c1.set(Calendar.YEAR,2008);
	    c1.add(Calendar.DATE, 10);
	    

		 int year = c1.get(Calendar.YEAR);
		 int month = c1.get(Calendar.MONTH) + 1;
		 int hour = c1.get(Calendar.HOUR_OF_DAY);
		 int minute = c1.get(Calendar.MINUTE);
		 int day = c1.get(Calendar.DAY_OF_WEEK);// 获得星期几（注意（这个与Date类是不同的）：1代表星期日、2代表星期1、3代表星期二，以此类推）
	  
		 
		 GregorianCalendar gcalendar = new GregorianCalendar();
		 print(year = gcalendar.get(Calendar.YEAR));
		 gcalendar.isLeapYear(year);//是否闰年
		 
		 
	      String string = "This order was placed for QT3000! OK?";
	      Pattern pattern = Pattern.compile("(.*)(\\d+)(.*)");

	      Matcher matcher = pattern.matcher(string);
	      
	      if (matcher.find()) {
	         print("Found: " + matcher.group(0) );
	         print("Found: " + matcher.group(1) );
	         print("Found: " + matcher.group(2) );
	      } else {
	         print("NO MATCH");
	      }
	      
	      //查找位置
	      Pattern pattern2 = Pattern.compile("\\bcat\\b");
	      Matcher matcher2 = pattern2.matcher("cat cat cat cattie cat");
	       
	      int count = 0;
	       while(matcher2.find()) {
	         count++;
	         printf("匹配第  %d 次  start:%d end:%d", count, matcher2.start(), matcher2.end());
	      }
	      
	       
	       Pattern pattern3 = Pattern.compile("foo");
	       Matcher matcher3 = pattern3.matcher("fooooo");
	       print("fooooo 中 查找 foo = lookingAt(): "+ matcher3.lookingAt());
	       print("fooooo 中 完全匹配 foo = matches(): " + matcher3.matches());
	       
	       
	       Pattern p = Pattern.compile("a*b");
	       // 获取 matcher 对象
	       Matcher m = p.matcher("aabfooaabfooabfoob");
	       StringBuffer sb = new StringBuffer();
	       while(m.find()){
	          m.appendReplacement(sb, "-");
	       }
	       //print(sb.toString());
	       //m.appendTail(sb);
	       print(sb.toString());


	       char[] achear = {'a', 'b'};
	       print(achear);
	       
	       
	       //从控制台不断读取字符直到用户
//	       char c;
//	       BufferedReader br = new  BufferedReader(new InputStreamReader(System.in));
//	       print("Enter characters, 'q' to quit.");
//	       do {
//	          c = (char) br.read();
//	          print(c);
//	       } while(c != 'q');
	       

//	       System.out.write('Z');
//	       System.out.write('\n');

//	       BufferedReader bReader = new BufferedReader(new InputStreamReader(System.in));
//	       String string2;
//	       print("Enter line of text and enter 'end' to quit.");
//	       do {
//	    	   str = bReader.readLine();
//	    	   print(str);
//	       }while(!str.equals("end"));
	       
	       

	       
	}
	
	
	public static void ioStream() throws IOException {
		

//	       ByteArrayInputStream bArray1 = new ByteArrayInputStream(BYTES);
//	       ByteArrayInputStream bArray2 = new ByteArrayInputStream(BYTES, 1, 3);
//	       
//	       while((INT = bArray1.read())!= -1) {print((char) INT); }
//	       reset();
//	       
//	       
//	       ByteArrayOutputStream output = new ByteArrayOutputStream(12);
//	       
//	       while(output.size()!= 10 ){output.write(System.in.read());}
//	      
//	       
//	       for(byte item : output.toByteArray()){ print((char) item); }
//	 
//	       ByteArrayInputStream input = new ByteArrayInputStream(output.toByteArray());
//	       
//          while((INT = input.read())!= -1){ print((char) INT);}
//          input.reset();
//          
//          reset();
//	       
//          
          
//		   DataInputStream 	input3 	= new DataInputStream(new FileInputStream(INPUT_FILE));
//		   DataOutputStream output3 = new DataOutputStream(new FileOutputStream(OUTPUT_FILE));
//	
//			
//			while((STRING = input3.readLine()) != null){
//				
//				String u = STRING.toUpperCase();
//				
//				System.out.println(u);
//				
//				output3.writeBytes(u + "  ,");
//			}
//			
//			input3.close();
//			output3.close();
		
		
//	       InputStream f1 = new FileInputStream(INPUT_FILE);
//	       InputStream f2 = new FileInputStream(new File(INPUT_FILE));

//	     OutputStream output10 = new FileOutputStream(OUTPUT_FILE);
//	      
//	      for(byte item : BYTES){
//	    	  output10.write((char) item); 
//	      }
//	      output10.close();
	      
      
//		  reset();
//	      InputStream input10 = new FileInputStream(INPUT_FILE);
//	      input10.available();
//	 
//	      while((INT = input10.read()) != -1){
//	         print((char) input10.read());
//	      }
//	      
//	      input10.close();

		
//		FileOutputStream fop = new FileOutputStream(FILE);
//
//		OutputStreamWriter writer = new OutputStreamWriter(fop, "UTF-8");
//	
//		writer.append("中文输入"); writer.append("\r\n"); writer.append("English");
//	
//		writer.close();
//		fop.close();
//	
// 
//		FileInputStream fip = new FileInputStream(FILE);
//		InputStreamReader reader = new InputStreamReader(fip, "UTF-8");
//
//		StringBuffer sb = new StringBuffer();
//		
//		while (reader.ready()) {
//			sb.append((char) reader.read());
//	
//		}
//		
//		System.out.println(sb.toString());
//		
//		reader.close();
//		fip.close();

	   
		
//		File file = new File("Hello1.txt");
//
//	      file.createNewFile();
//	      
//	      FileWriter writer = new FileWriter(file); 
//	      
//	      // 向文件写入内容
//	      String data = "This\nis\nan\nexample\nhehe haha";
//	      writer.write(data); 
//	      writer.flush();
//	      writer.close();
//	      
//	      FileReader fr = new FileReader(file); 
//	      
//	      char[] chars = new char[data.length()];
//	      fr.read(chars);
//	      
//	      for(char c : chars) { out.print(c); }
//	   
//	      fr.close();
	      
	      
		String dirname = "/tmp/user/java/bin";
	      File file = new File(dirname);
	      // 现在创建目录
	      file.mkdirs();
	      file.isDirectory();
	      file.list();
	      
	 
	}
	
	
	public static void arrayToCollection() throws IOException {
		
		
//	      String[] data = {"aaa", "bbb", "ccc"};
//	      for(String item : data){ print(item); }
//	      
//	      List<String> list = Arrays.asList(data); 
//	     
//	      for(String item : list){ print(item); }


	      
	      
	}
	


	

}








