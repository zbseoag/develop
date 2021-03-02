import beans.Student;
import com.zaxxer.hikari.HikariConfig;
import com.zaxxer.hikari.HikariDataSource;
import org.junit.jupiter.api.*;
import org.junit.jupiter.api.function.Executable;

import javax.sql.DataSource;
import java.io.IOException;
import java.sql.*;
import java.util.ArrayList;
import java.util.List;

import static org.junit.jupiter.api.Assertions.*;

public class MysqlTest{

    static final String jdbcUrl = "jdbc:mysql://localhost/learnjdbc?useSSL=false&characterEncoding=utf8";
    static final String jdbcUsername = "root";
    static final String jdbcPassword = "123456";


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
    void insertd() throws IOException, SQLException{

        try(Connection conn = Mysql.getConnection(); Statement state = conn.createStatement()){

            state.executeUpdate("create table IF NOT EXISTS greetings(message char (20))");
            state.executeUpdate("insert into greetings values ('hello world!')");

            try(ResultSet result = state.executeQuery("select * from greetings")){

                state.getUpdateCount();
                if(result.next()){
                    Out.println(result.getString(1));
                    assertNotNull(result.getString(1));
                }
            }

            state.executeUpdate("drop table greetings");
            SQLWarning w = state.getWarnings();
            while(w != null){
                Out.println(w);
                w = w.getNextWarning();
            }

        }



    }

    @Test
    void pre() throws IOException, SQLException{

        try(Connection conn = Mysql.getConnection()){

            String sql = "select * from user where name = ? and id = ?";

            try(PreparedStatement state = conn.prepareStatement(sql)){

                state.setString(1, "tom");
                state.setInt(2, 3);

                try(ResultSet result = state.executeQuery()){

                    while(result.next()){
                        long id = result.getLong("id");
                        Out.println(id);
                    }
                }

            }

            sql = "update greetings set name = ? where id = ?";
            try(PreparedStatement state = conn.prepareStatement(sql)){

                state.setString(1, "tom");
                state.setInt(2, 3);
                int count = state.executeUpdate();
                Out.println("更新记录行数：" + count);

            }

        }

    }

    @Test
    public void insert() throws IOException, SQLException{

        try (Connection conn = Mysql.getConnection()) {

            String sql = "INSERT INTO students (grade, name, gender) VALUES (?,?,?)";
            try (PreparedStatement ps = conn.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {

                ps.setObject(1, 1); // grade
                ps.setObject(2, "Bob"); // name
                ps.setObject(3, "M"); // gender
                int n = ps.executeUpdate(); // 1

                try (ResultSet rs = ps.getGeneratedKeys()) {
                    if (rs.next()) {
                        long id = rs.getLong(1); // 注意：索引从1开始
                    }
                }
            }

        }

    }

    @Test
    public void update() throws IOException, SQLException{

        try (Connection conn = Mysql.getConnection()) {

            try (PreparedStatement ps = conn.prepareStatement("UPDATE students SET name=? WHERE id=?")) {
                ps.setObject(1, "Bob"); // 注意：索引从1开始
                ps.setObject(2, 999);
                int n = ps.executeUpdate(); // 返回更新的行数
            }
        }
    }

    @Test
    public void delete() throws IOException, SQLException{

        try (Connection conn = Mysql.getConnection()) {
            try (PreparedStatement ps = conn.prepareStatement("DELETE FROM students WHERE id=?")) {
                ps.setObject(1, 999); // 注意：索引从1开始
                int n = ps.executeUpdate(); // 删除的行数
            }
        }

    }

    @Test
    public void insertStudents() throws SQLException, IOException{

        try (Connection conn = Mysql.getConnection()) {

            boolean defAutoCommit = conn.getAutoCommit();
            conn.setAutoCommit(false); // 关闭自动提交事务

            String sql = "INSERT INTO students (name, gender, grade, score) VALUES (?, ?, ?, ?)";
            int score = 100;
            try (PreparedStatement ps = conn.prepareStatement(sql)) {
                ps.setString(1, "tom");
                ps.setInt(4, 100);
                int n = ps.executeUpdate();
                System.out.println(n + " inserted.");
            }
            if (score > 100) {
                conn.rollback();
            } else {
                conn.commit();
            }
            conn.setAutoCommit(defAutoCommit); // 恢复AutoCommit设置
        }
    }

    @Test
    /*
       批量执行
     */
    public void batch() throws SQLException, IOException{

        Connection conn = Mysql.getConnection();

        try (PreparedStatement ps = conn.prepareStatement("INSERT INTO students (name, gender, grade, score) VALUES (?, ?, ?, ?)")) {

            // 对同一个PreparedStatement反复设置参数并调用addBatch():
            for (int i = 1; i < 10; i++) {
                ps.setString(1, "tom" + i);
                ps.setInt(4, 100);
                ps.addBatch(); // 添加到batch
            }

            // 执行batch
            int[] ns = ps.executeBatch();
            for (int n : ns) {
                System.out.println(n + " inserted."); // batch中每个SQL执行的结果数量
            }
        }

    }


    @Test
    /**
     * 连接池
     */
    public void pools() throws Exception {

        HikariConfig config = new HikariConfig();
        config.setJdbcUrl(jdbcUrl);
        config.setUsername(jdbcUsername);
        config.setPassword(jdbcPassword);
        config.addDataSourceProperty("cachePrepStmts", "true");
        config.addDataSourceProperty("prepStmtCacheSize", "100");
        config.addDataSourceProperty("maximumPoolSize", "10");
        DataSource ds = new HikariDataSource(config);
        List<Student> students = queryStudents(ds);
        students.forEach(System.out::println);

    }

    static List<Student> queryStudents(DataSource ds) throws SQLException {
        List<Student> students = new ArrayList<>();
        try (Connection conn = ds.getConnection()) { // 在此获取连接
            try (PreparedStatement ps = conn
                    .prepareStatement("SELECT * FROM students WHERE grade = ? AND score >= ?")) {
                ps.setInt(1, 3); // 第一个参数grade=?
                ps.setInt(2, 90); // 第二个参数score=?
                try (ResultSet rs = ps.executeQuery()) {
                    while (rs.next()) {
                        students.add(extractRow(rs));
                    }
                }
            }
        } // 在此“释放”连接
        return students;
    }

    static Student extractRow(ResultSet rs) throws SQLException {
        var std = new Student();
        std.setId(rs.getLong("id"));
        std.setName(rs.getString("name"));
        std.setGender(rs.getBoolean("gender"));
        std.setGrade(rs.getInt("grade"));
        std.setScore(rs.getInt("score"));
        return std;
    }



}
