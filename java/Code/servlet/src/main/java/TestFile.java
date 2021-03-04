import java.io.*;
import java.nio.charset.StandardCharsets;
import java.time.LocalDate;
import java.util.Locale;
import java.util.Scanner;
import java.util.zip.ZipEntry;
import java.util.zip.ZipInputStream;
import java.util.zip.ZipOutputStream;

public class TestFile extends Common {

    public static void main(String[] args) throws IOException {
        var staff = new Employee[3];
        staff[0] = new Employee("carl", 7500, "1985-04-21");
        staff[1] = new Employee("harry", 7500, "1956-04-05");
        staff[2] = new Employee("tony", 7500, "1985-03-17");

        try(var out = new PrintWriter("employee.txt", StandardCharsets.UTF_8)){
            writeData(staff, out);
        }

        try(var in = new Scanner(new FileInputStream("employee.txt"), "UTF-8")){
            Employee[] newStaff = readData(in);
            for(Employee e : newStaff){
                p(e);
            }
        }

    }

    private static void writeData(Employee[] employees, PrintWriter out){
        for(Employee e : employees){
            writeEmployee(out, e);
        }
    }

    private static Employee[] readData(Scanner in){
        int n = in.nextInt();
        in.nextLine();

        var employess = new Employee[n];
        for(int i = 0; i < n; i++){
            employess[i] = readEmployee(in);
        }
        return employess;
    }

    public static void writeEmployee(PrintWriter out, Employee e){
        out.println(e.name + "|" + e.salary + e.birthday);
    }

    public static Employee readEmployee(Scanner in){
        String line = in.nextLine();
        String[] tokens = line.split("\\|");
        String name = tokens[0];
        double salary = Double.parseDouble(tokens[1]);
        LocalDate birthday = LocalDate.parse(tokens[2]);
        int year = birthday.getYear();
        int month = birthday.getMonthValue();
        int date = birthday.getDayOfMonth();
        return new Employee(name, salary, tokens[2]);
    }


}




class RandomAccess {

    public static void main(String[] args) throws IOException, ClassNotFoundException {
        try(var out = new DataOutputStream(new FileOutputStream("employee.txt"))){

        }

        try(var in = new RandomAccessFile("employee.txt", "r")){

        }

        var zip = new ZipInputStream(new FileInputStream("empl.zip"));
        ZipEntry entry;
        while((entry = zip.getNextEntry()) != null){
            entry.getName();
            zip.closeEntry();
        }
        zip.close();

        var zout = new ZipOutputStream(new FileOutputStream("test.zip"));
        {
            var ze = new ZipEntry("tom.txt");
            zout.putNextEntry(ze);
            zout.closeEntry();
        }
        zout.close();

        var out = new ObjectOutputStream(new FileOutputStream("employee.dat"));
        out.writeObject(new String("aaaa"));

        var in = new ObjectInputStream(new FileInputStream("employee.txt"));
        in.readObject();

    }

}