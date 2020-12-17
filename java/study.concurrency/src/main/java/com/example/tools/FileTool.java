package tools;/*
package tools;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.FilenameFilter;
import java.io.IOException;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.util.ArrayList;
import java.util.List;


public class FileTool{
    
    private static final String FileTool = null;
    private File file = null;
    
    public FileTool(String file){
        
        this.file = new File(file);
    }
    
    public FileTool(File file){
        
        this.file = file;
    }
    
    */
/**
     * 创建文件
     * @param fileName
     * @throws IOException
     *//*

    public static void create(String file) throws IOException{
        
        create(new File(file));
    }
    
    public void create() throws IOException{
  
        create(file);
    }
    
    public static void create(File file) throws IOException{
        
        if(!file.exists()) file.createNewFile();
    }
    

    */
/**
     * 文件读取
     * @param file
     * @return
     *//*

    public static String read(String file){

        return read(new File(file));
    }
    
    public String read(){
        
        return read(file);
    }
    
    public static String read(File file){

        StringBuilder content = new StringBuilder();
        try(BufferedReader reader = new BufferedReader(new FileReader(file))){
            
            String line;
            while((line = reader.readLine()) != null){
                content.append(line + System.lineSeparator()); 
            }
        }catch(IOException e){
            e.printStackTrace();
        }
        
        return content.toString();
    }
    

    
  
    */
/**
     * 文件写入
     * @param file
     * @param content
     * @param append
     * @throws IOException
     *//*

    
    public static void write(File file, String content, boolean append) throws IOException{
        
        if(!file.exists()) file.createNewFile();
        
        FileWriter fileWritter = new FileWriter(file, append);
        BufferedWriter bufferWritter = new BufferedWriter(fileWritter);
        bufferWritter.write(content + System.lineSeparator());
        bufferWritter.close();

    }
    
    public static void write(String file, String content, boolean append) throws IOException{
        
        write(new File(file), content, append);

    }
    
    public static void write(String file, String content) throws IOException{
        
        write(new File(file), content, false);

    }
    
    
    public FileTool write(String content, boolean append) throws IOException{

        write(file, content, append);
        return this;
    }
    
    public FileTool write(String content) throws IOException{

        write(file, content, false);
        return this;
    }
    


    */
/**
     * 文件删除
     * @return
     *//*

    public static boolean delete(File file){
        
        if(file.exists()) return file.delete();
        return true;
    }
    
    public static boolean delete(String file){
        
        return delete(new File(file));
    }
    
    public boolean delete(){
        
        return delete(file);
    }
    

    */
/**
     * 文件重命令
     * @param fileName
     * @param newName
     * @return
     *//*

    public static boolean rename (File file, File newFile){
        

        if(!file.exists()){
            System.out.println("文件不存在");
            return false;
        }
        if(newFile.exists()){
            System.out.println("同名文已存在");
            return false;
        }
        if(!file.renameTo(newFile)){
            System.out.println("文件重命名失败");
        }
        return true;

    }
    
    public static boolean rename (String file, String newName){
        
        return rename(new File(file), new File(newName));

    }
    
    public boolean rename(String newName){
        
        return rename(file, new File(newName));
    }
    
 
    */
/**
     * 对象序列化到文件
     * @param <T>
     * @param object
     * @throws IOException
     *//*

    public static <T> void serialize(File file, T object) throws IOException{

        FileOutputStream fileOutputStream = new FileOutputStream(file);
        ObjectOutputStream objectOutputStream = new ObjectOutputStream(fileOutputStream);
        objectOutputStream.writeObject(object);
        objectOutputStream.close();

    }
    

    public static <T> void serialize(String file, T object) throws IOException {

        serialize(new File(file), object);

    }
    
    public <T> void serialize(T object) throws IOException {

        serialize(file, object);

    }

    
 
    */
/**
     * 文件反序列化到对象
     * @param file
     * @param object
     * @return
     * @throws ClassNotFoundException
     * @throws IOException
     *//*

    @SuppressWarnings("unchecked")
    public static <T> T deserialze(File file, T object) throws ClassNotFoundException, IOException {
        
            FileInputStream fileInputStream = new FileInputStream(file);
            ObjectInputStream objectInputStream = new ObjectInputStream(fileInputStream);
            object = (T) objectInputStream.readObject();
            objectInputStream.close();
            return object;

    }
    
    public static <T> T deserialze(String file, T object) throws ClassNotFoundException, IOException {
        
        return deserialze(new File(file), object);

    }
    
    public  <T> T deserialze(T object) throws ClassNotFoundException, IOException {
        
        return deserialze(file, object);

    }
    
    */
/**
     * 创建目录
     * @param file
     *//*

    public static void mkdirs(File file){
        
        file.mkdirs();
    }
    
    public static void mkdirs(String file){
        
        mkdirs(new File(file));
    }

    public void mkdirs(){
        
        mkdirs(file);
    }
    
    public static List<String> fileList(String dir, String ext) throws Exception{
        
        return fileList(new File(dir), ext);
    }
    
    public List<String> fileList(String ext) throws Exception{
        
        return fileList(file, ext);
    }
    
    public static List<String> fileList(File dir, String ext) throws Exception{
        
        if (dir.isDirectory() == false) {
            throw new Exception("Directory does not exists : " + dir.getName());
        }

        FileFilter fileFilter = new FileTool(dir).new FileFilter(ext);
        String[] list = dir.list(fileFilter);

        List<String> fileName = new ArrayList<String>();
        
        for(String file : list){
            fileName.add(file);
        }
        
        return fileName;
        
    }

    
    //内部类
    public class FileFilter implements FilenameFilter {
        
        private final String ext;
 
        public FileFilter(String ext) {
            this.ext = ext;
        }
 
        @Override
        public boolean accept(File dir, String name) {
            return (name.endsWith(ext));
        }
    }
    

}
*/
