import java.io.IOException;
import java.io.InputStream;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.util.HashMap;
import java.util.Map;
import java.util.Properties;
import java.util.function.Function;

public class Mysql{

    public static Connection getConnection() throws IOException, SQLException{

        var props = new Properties();
        try(InputStream in = Mysql.class.getResourceAsStream("db.properties")){
            props.load(in);
        }

        //注册驱动,多个驱动用冒号分割
        System.setProperty("jdbc.drivers", props.getProperty("jdbc.drivers"));
        return DriverManager.getConnection(props.getProperty("jdbc.url"), props.getProperty("jdbc.username"), props.getProperty("jdbc.password"));

    }



}
