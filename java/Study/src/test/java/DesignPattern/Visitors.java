package DesignPattern;

import org.junit.jupiter.api.Test;
import java.io.File;

/**
 * 访问者模式
 */
public class Visitors {

    interface Visitor {

        //访问文件夹
        default void visitDir(File dir){
            System.out.println("Visit dir: " + dir);
        }

        //访问文件
        default void visitFile(File file){
            System.out.println("Visit file: " + file);
        }

    }

    /**
     * 访问目录 和 .java文件
     */
    class JavaFileVisitor implements Visitor {

        public void visitFile(File file) {

            if (file.getName().endsWith(".java")) {
                System.out.println("Found java file: " + file);
            }
        }
    }


    /**
     * 访问目录 和 .class 文件
     */
    class ClassFileVisitor implements Visitor {

        public void visitFile(File file) {

            if (file.getName().endsWith(".class")) {
                System.out.println("Found class file: " + file);
            }
        }
    }


    class FileStructure {

        //根目录
        private File path;

        public FileStructure(String path) {
            this(new File(path));
        }

        public FileStructure(File path) {
            this.path = path;
        }

        /**
         * 设置访问者
         */
        public void handle(Visitor visitor) {
            scan(visitor);
        }

        public void scan(Visitor visitor){
            scan(this.path, visitor);
        }

        /**
         * 浏览目录
         */
        private void scan(File file, Visitor visitor) {

            if (file.isDirectory()) {

                visitor.visitDir(file);//访问目录
                for (File sub : file.listFiles()) {
                    scan(sub, visitor);//递归处理子文件夹
                }

            } else if(file.isFile()) {
                visitor.visitFile(file);//访问文件
            }
        }


    }


    @Test
    void main(){

        FileStructure fs = new FileStructure(".");
        fs.handle(new JavaFileVisitor());
        fs.handle(new ClassFileVisitor());

    }

}






