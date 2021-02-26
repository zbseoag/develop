import org.junit.jupiter.api.*;
import org.junit.jupiter.api.function.Executable;

import java.io.IOException;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

import static org.junit.jupiter.api.Assertions.*;

public class MysqlTest{

    @Test
    void connection() {

        assertDoesNotThrow(new Executable(){
            @Override
            public void execute() throws Throwable {
                Mysql.getConnection();
            }
        }, "连接数据库失败");

    }

    @Test
    void insert() throws IOException, SQLException{

        try(Connection conn = Mysql.getConnection(); Statement state = conn.createStatement()){

            state.executeUpdate("create table IF NOT EXISTS greetings(message char (20))");
            state.executeUpdate("insert into greetings values ('hello world!')");

            try(ResultSet result = state.executeQuery("select * from greetings")){

                state.getUpdateCount();
                if(result.next()){
                    Out.echo(result.getString(1));
                    assertNotNull(result.getString(1));
                }
            }
        }
    }



}
