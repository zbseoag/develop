package tools;

import java.sql.*;
import java.util.HashMap;
import java.util.Map;

/**

    ResultSet rs = new Database("root", "").connect().query("SELECT id, name, url FROM citys LIMIT 10");
    while(rs.next()){
        System.out.println(rs.getInt("id") + " => " + rs.getString("name"));
    }
    obj.close();

 */

public class Database {


    static final String DRIVER = "com.mysql.cj.jdbc.Driver";
    static final String URL = "jdbc:mysql://localhost:3306/test";

    public String user;
    public String password;

    public Connection connect;
    public Statement statement;


    public Database(String user, String password){

        this.user = user;
        this.password = password;

    }


    public Database connect(Map<String, Object> options) throws ClassNotFoundException, SQLException {

        StringBuffer buffer = new StringBuffer("?");

        for(Map.Entry<String, Object> entry : options.entrySet()){
            buffer.append(entry.getKey()).append("=").append(entry.getValue().toString()).append("&");
        }

        if(buffer.length () > 0) buffer.deleteCharAt(buffer.length () - 1);

        Class.forName(DRIVER);

        connect = DriverManager.getConnection(URL + buffer, user, password);
        statement = connect.createStatement();

        return this;
    }


    public Database connect() throws ClassNotFoundException, SQLException {

        Map<String, Object> options = new HashMap<String, Object>();

        options.put("characterEncoding", "utf8");
        options.put("useSSL", "false");
        options.put("serverTimezone", "UTC");

        return this.connect(options);

    }


    public ResultSet query(String sql) throws SQLException{

        return statement.executeQuery(sql);

    }

    public void close() throws SQLException{

        statement.close();
        connect.close();
    }



}