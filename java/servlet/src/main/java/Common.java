import java.io.IOException;
import java.io.InputStream;
import java.io.Serializable;
import java.nio.file.FileVisitOption;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;


class Employee implements Serializable {

    public String name;
    public double salary;
    public String birthday;

    public Employee(String name, double salary, String birthday){
        this.name = name;
        this.salary = salary;
        this.birthday = birthday;
    }

    @Override
    public String toString(){
        return "{name:" + name +", salary:"+ salary + ", birthday:" + birthday + "}";
    }

}

class Manger extends Employee {

    public Employee secretary;

    public Manger(String name, double salary, String birthday) {
        super(name, salary, birthday);
    }

    public void setSecretary(Employee secretary){
        this.secretary = secretary;
    }
}

class Test extends Common {

    public static void main(String[] args) throws IOException {

        Path path = Paths.get("/home", "aa", "bbb");

        p(Paths.get("/home", "aa", "bbb"));
        InputStream in = Files.newInputStream(Paths.get("aaa"));

        Files.walk(path , 10, FileVisitOption.FOLLOW_LINKS).forEach(p -> {
            p(p.getFileName());

        });

    }

}