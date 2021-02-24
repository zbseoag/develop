import java.io.IOException;
import java.io.InputStream;
import java.sql.*;
import java.util.Properties;

public class Chapter5 extends Common{

    public static void main(String[] args) throws ClassNotFoundException, IOException, SQLException{

        try(Connection connection = getConnection(); Statement statement = connection.createStatement()){
            statement.executeUpdate("create table greetings(message char (20))");
            statement.executeUpdate("insert into greetings values ('hello world!')");

            try(ResultSet result = statement.executeQuery("select * from greetings")){
                if(result.next()){
                    p(result.getString(1));
                }
            }

            statement.executeUpdate("drop table greetings");
        }




    }

    public static Connection getConnection() throws IOException, SQLException{

        var props = new Properties();

        try(InputStream in = Chapter5.class.getResourceAsStream("db.properties")){
            props.load(in);
        }
        System.setProperty("jdbc.drivers", props.getProperty("jdbc.drivers"));
        return DriverManager.getConnection(props.getProperty("jdbc.url"), props.getProperty("jdbc.username"), props.getProperty("jdbc.password"));


//        Properties properties = new Properties();
//        // 使用ClassLoader加载properties配置文件生成对应的输入流
//        InputStream in = Chapter5.class.getClassLoader().getResourceAsStream("db.properties");
//        // 使用properties对象加载输入流
//        properties.load(in);
//        //获取key对应的value值
//        p(properties.getProperty("mysqlDriver"));

//        Properties properties = new Properties();
//         // 使用InPutStream流读取properties文件
//        BufferedReader bufferedReader = new BufferedReader(new FileReader("db.properties"));
//        properties.load(bufferedReader);
//        // 获取key对应的value值
//        p(properties.getProperty("mysqlDriver"));
    }
}
