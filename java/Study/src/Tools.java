import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.InetAddress;
import java.net.MalformedURLException;
import java.net.Socket;
import java.net.URL;
import java.net.URLConnection;
import java.net.UnknownHostException;
import java.sql.Time;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Date;
import java.util.List;

import sun.tools.jconsole.ProxyClient.Snapshot;



public class Tools {
	
	public static String oneString = "----------------------------------------------------------------------------------";
	public static String twoString = "==================================================================================";
	

	public static <T> String type(T a) {
		
		return a.getClass().getTypeName();
	}
	

	public static void printf(String format, Object... data){
		
		System.out.printf(format + "\n", data);
		System.out.println(twoString);
	
	}
	

	public static <T> void print(T data) {
		
			if(data.getClass().isArray()){
				
				String out = null;
				String type = type(data);
				
		       switch (type) {
				case "byte[]": 		out = Arrays.toString((byte[]) data);
					break;
				case "short[]": 	out = Arrays.toString((short[]) data);
					break;
				case "int[]": 		out = Arrays.toString((int[]) data);
					break;
				case "long[]": 		out = Arrays.toString((long[]) data);
					break;
				case "float[]": 	out = Arrays.toString((float[]) data);
					break;
				case "double[]": 	out = Arrays.toString((double[]) data);
					break;
				case "char[]": 		out = Arrays.toString((char[]) data);
					break;
				case "boolean[]": 	out = Arrays.toString((boolean[]) data);
					break;
				default:
					out = Arrays.toString((Object[]) data);
					break;
				}
						
				System.out.println(out);
				
			}else {
				System.out.println(data);
			}
			

		
		System.out.println(twoString);

	}
	

	
	public static String ip() throws UnknownHostException{
		
		return (InetAddress.getLocalHost()).getHostAddress();
	}
	
	
	public static String ip(String domain) throws UnknownHostException{
		
	    return (InetAddress.getByName(domain)).getHostAddress();

	}
	
	
	
	public static long modified(String file) throws MalformedURLException, IOException{
		
	      URLConnection uc = (new URL(file)).openConnection();
	      uc.setUseCaches(false);
	      
	      return uc.getLastModified();
	    
	}
	
	
	public static boolean download(String url, String file) throws IOException {
		
		
	      BufferedReader reader = new BufferedReader(new InputStreamReader(new URL(url).openStream()));
	      BufferedWriter writer = new BufferedWriter(new FileWriter(file));
	      
	      String line;
	      
	      while ((line = reader.readLine()) != null){
	    	  
	         writer.write(line);
	         writer.newLine();
	      }
	      
	      reader.close();
	      writer.close();
	      
	      return true;

	}
	
	
	public static boolean download(String url) throws IOException {
		
		return download(url, date() + ".txt");
		
	}
	
	
	  public static String date(String format, Long timestamp) {
		  
	        return (new SimpleDateFormat(format)).format(timestamp);
	  }
	 

	  public static String date(Long timestamp) {
		  
		   return date("yyyy-MM-dd HH:mm:ss", timestamp);
	  }
	  
	  
	  public static String date(String format) {

		   return date(format);
	  }
	  
	  public static String date() {
		  
	        return date( "yyyy-MM-dd HH:mm:ss", System.currentTimeMillis());
	      
	  }
	  
	  public static String thread() {
		  
	      return Thread.currentThread().getName();
	  }
	 
	   
	 public static <T> Object[] merge(T[] a, T[] b) {
		
	      List<T> list = new ArrayList<T>(Arrays.asList(a));
	      list.addAll(Arrays.asList(b));
	      
	      return list.toArray();

	}
	 
	 public static boolean rename(String file, String newfile) {
		 
	      return (new File(file)).renameTo(new File(newfile));

	}
	 
	public static void file(String filePath) {
		
		File file = new File(filePath);

	    System.out.println(file.canWrite());
	      
	      
	}
	
	
	   // 比较三个值并返回最大值
	@SafeVarargs
	public static <T extends Comparable<T>> T max(T... number){
		   
	      T max = number[0]; // 假设x是初始最大值
	      for(T i : number) {
	    	  
	          if(i.compareTo(max) > 0){
	 	         max = i;
	 	      }
	      }
	      
	      return max; // 返回最大对象
	      
	 }
	   
	@SafeVarargs
	public static <T extends Comparable<T>> T min(T... number){
		   
	      T min = number[0]; // 假设x是初始最大值
	      for(T i : number) {
	    	  
	          if(i.compareTo(min) < 0){
	        	  min = i;
	 	      }
	      }
	      
	      return min; // 返回最大对象
	      
	}
	
	
	public static Object getItem(List<?> data, int index){
		
		return data.get(index);
	}
	
	public static Number getUperNumber(List<? extends Number> data) {
		return data.get(0);
	}

	
	
}
