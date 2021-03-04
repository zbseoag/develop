import javax.imageio.ImageIO;
import javax.naming.InitialContext;
import javax.naming.NamingException;
import javax.sql.DataSource;
import javax.sql.rowset.CachedRowSet;
import javax.sql.rowset.RowSetProvider;
import java.awt.*;
import java.awt.image.RenderedImage;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.sql.*;
import java.util.Properties;

public class Chapter5 {

    public static void main(String[] args) throws ClassNotFoundException, IOException, SQLException, NamingException{

        try(Connection conn = getConnection(); Statement state = conn.createStatement()){
            state.executeUpdate("create table greetings(message char (20))");
            state.executeUpdate("insert into greetings values ('hello world!')");

            try(ResultSet result = state.executeQuery("select * from greetings")){

                state.getUpdateCount();


                if(result.next()){
                    p(result.getString(1));

                    //result.getDouble("price");
                }
            }

            state.executeUpdate("drop table greetings");

            SQLWarning w = state.getWarnings();
            while(w != null){
                p(w);
                w = w.getNextWarning();
            }

            PreparedStatement s = conn.prepareStatement("select * from user where name = ? and id = ?");
            s.setString(1, "tom");
            s.setInt(2, 3);
            try(ResultSet result = s.executeQuery()){
                while(result.next()){
                    Blob blob = result.getBlob(2);
                    Image image = ImageIO.read(blob.getBinaryStream());

                    Blob blob2 = conn.createBlob();
                    OutputStream out = blob2.setBinaryStream(0);
                    ImageIO.write((RenderedImage) image, "PNG", out);
                    PreparedStatement stat = conn.prepareStatement("insert into cover values(?,?)");
                    stat.setString(1, "aaa");
                    stat.setBlob(2, blob2);

                }


                CachedRowSet crs = RowSetProvider.newFactory().createCachedRowSet();
                crs.populate(result);

                crs.setUrl("jdbc:mysql://localhost:3306/test");
                crs.setUsername("root");
                crs.setPassword("123456");
                crs.setCommand("select * from user where id = ?");
                crs.setInt(1, 12);
                crs.setPageSize(20);
                crs.execute();

                crs.nextPage();
                crs.acceptChanges();


            }

            ResultSet rs = state.executeQuery("select * from user");
            ResultSetMetaData meta = rs.getMetaData();
            for(int i = 1; i <= meta.getColumnCount(); i++){
                String columename = meta.getColumnLabel(i);
                int columewidth = meta.getColumnDisplaySize(i);
            }

            //boolean autocommit = conn.getAutoCommit();
            //conn.setAutoCommit(false);
            //state.addBatch("create table user");
            //state.addBatch("insert into user values(1,2)");
            //state.executeBatch();
            //Savepoint point = conn.setSavepoint();
            //conn.commit();
            //conn.rollback(point);
            //conn.setAutoCommit(autocommit);

            var source = (DataSource) new InitialContext().lookup("java:comp/env/jdbc/creajava");
            Connection conn2 = source.getConnection();
        }


    }

    public static Connection getConnection() throws IOException, SQLException, ClassNotFoundException{

        var props = new Properties();
        try(InputStream in = Chapter5.class.getResourceAsStream("db.properties")){
            //加载配置文件内容
            props.load(in);
        }
        //try(InputStream in = Files.newInputStream(Paths.get("db.properties"))){
        //    props.load(in);
        //}
        //注册驱动,多个驱动用冒号分割
        System.setProperty("jdbc.drivers", props.getProperty("jdbc.drivers"));
        //Class.forName(props.getProperty("jdbc.drivers"));
        return DriverManager.getConnection(props.getProperty("jdbc.url"), props.getProperty("jdbc.username"), props.getProperty("jdbc.password"));

    }
}

