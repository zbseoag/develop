package DesignPattern;

import org.junit.jupiter.api.Test;

import javax.sql.DataSource;
import java.io.PrintWriter;
import java.sql.*;
import java.util.Map;
import java.util.Properties;
import java.util.Queue;
import java.util.concurrent.ArrayBlockingQueue;
import java.util.concurrent.Executor;
import java.util.function.Supplier;
import java.util.logging.Logger;

public class Proxy {


    abstract class AbstractConnectionProxy implements Connection {

        protected abstract Connection getRealConnection();

        @Override
        public Statement createStatement() throws SQLException {
            return getRealConnection().createStatement();
        }

        @Override
        public PreparedStatement prepareStatement(String sql) throws SQLException {
            return getRealConnection().prepareStatement(sql);
        }

        @Override
        public <T> T unwrap(Class<T> iface) throws SQLException {
            return getRealConnection().unwrap(iface);
        }

        @Override
        public boolean isWrapperFor(Class<?> iface) throws SQLException {
            return getRealConnection().isWrapperFor(iface);
        }

        @Override
        public CallableStatement prepareCall(String sql) throws SQLException {
            return getRealConnection().prepareCall(sql);
        }

        @Override
        public String nativeSQL(String sql) throws SQLException {
            return getRealConnection().nativeSQL(sql);
        }

        @Override
        public void setAutoCommit(boolean autoCommit) throws SQLException {
            getRealConnection().setAutoCommit(autoCommit);
        }

        @Override
        public boolean getAutoCommit() throws SQLException {
            return getRealConnection().getAutoCommit();
        }

        @Override
        public void commit() throws SQLException {
            getRealConnection().commit();
        }

        @Override
        public void rollback() throws SQLException {
            getRealConnection().rollback();
        }

        @Override
        public void close() throws SQLException {
            getRealConnection().close();
        }

        @Override
        public boolean isClosed() throws SQLException {
            return getRealConnection().isClosed();
        }

        @Override
        public DatabaseMetaData getMetaData() throws SQLException {
            return getRealConnection().getMetaData();
        }

        @Override
        public void setReadOnly(boolean readOnly) throws SQLException {
            getRealConnection().setReadOnly(readOnly);
        }

        @Override
        public boolean isReadOnly() throws SQLException {
            return getRealConnection().isReadOnly();
        }

        @Override
        public void setCatalog(String catalog) throws SQLException {
            getRealConnection().setCatalog(catalog);
        }

        @Override
        public String getCatalog() throws SQLException {
            return getRealConnection().getCatalog();
        }

        @Override
        public void setTransactionIsolation(int level) throws SQLException {
            getRealConnection().setTransactionIsolation(level);
        }

        @Override
        public int getTransactionIsolation() throws SQLException {
            return getRealConnection().getTransactionIsolation();
        }

        @Override
        public SQLWarning getWarnings() throws SQLException {
            return getRealConnection().getWarnings();
        }

        @Override
        public void clearWarnings() throws SQLException {
            getRealConnection().clearWarnings();
        }

        @Override
        public Statement createStatement(int resultSetType, int resultSetConcurrency) throws SQLException {
            return getRealConnection().createStatement(resultSetType, resultSetConcurrency);
        }

        @Override
        public PreparedStatement prepareStatement(String sql, int resultSetType, int resultSetConcurrency)
                throws SQLException {
            return getRealConnection().prepareStatement(sql, resultSetType, resultSetConcurrency);
        }

        @Override
        public CallableStatement prepareCall(String sql, int resultSetType, int resultSetConcurrency) throws SQLException {
            return getRealConnection().prepareCall(sql, resultSetType, resultSetConcurrency);
        }

        @Override
        public Map<String, Class<?>> getTypeMap() throws SQLException {
            return getRealConnection().getTypeMap();
        }

        @Override
        public void setTypeMap(Map<String, Class<?>> map) throws SQLException {
            getRealConnection().setTypeMap(map);
        }

        @Override
        public void setHoldability(int holdability) throws SQLException {
            getRealConnection().setHoldability(holdability);
        }

        @Override
        public int getHoldability() throws SQLException {
            return getRealConnection().getHoldability();
        }

        @Override
        public Savepoint setSavepoint() throws SQLException {
            return getRealConnection().setSavepoint();
        }

        @Override
        public Savepoint setSavepoint(String name) throws SQLException {
            return getRealConnection().setSavepoint(name);
        }

        @Override
        public void rollback(Savepoint savepoint) throws SQLException {
            getRealConnection().rollback(savepoint);
        }

        @Override
        public void releaseSavepoint(Savepoint savepoint) throws SQLException {
            getRealConnection().releaseSavepoint(savepoint);
        }

        @Override
        public Statement createStatement(int resultSetType, int resultSetConcurrency, int resultSetHoldability)
                throws SQLException {
            return getRealConnection().createStatement(resultSetType, resultSetConcurrency, resultSetHoldability);
        }

        @Override
        public PreparedStatement prepareStatement(String sql, int resultSetType, int resultSetConcurrency,
                                                  int resultSetHoldability) throws SQLException {
            return getRealConnection().prepareStatement(sql, resultSetType, resultSetConcurrency);
        }

        @Override
        public CallableStatement prepareCall(String sql, int resultSetType, int resultSetConcurrency,
                                             int resultSetHoldability) throws SQLException {
            return getRealConnection().prepareCall(sql, resultSetType, resultSetConcurrency);
        }

        @Override
        public PreparedStatement prepareStatement(String sql, int autoGeneratedKeys) throws SQLException {
            return getRealConnection().prepareStatement(sql, autoGeneratedKeys);
        }

        @Override
        public PreparedStatement prepareStatement(String sql, int[] columnIndexes) throws SQLException {
            return getRealConnection().prepareStatement(sql, columnIndexes);
        }

        @Override
        public PreparedStatement prepareStatement(String sql, String[] columnNames) throws SQLException {
            return getRealConnection().prepareStatement(sql, columnNames);
        }

        @Override
        public Clob createClob() throws SQLException {
            return getRealConnection().createClob();
        }

        @Override
        public Blob createBlob() throws SQLException {
            return getRealConnection().createBlob();
        }

        @Override
        public NClob createNClob() throws SQLException {
            return getRealConnection().createNClob();
        }

        @Override
        public SQLXML createSQLXML() throws SQLException {
            return getRealConnection().createSQLXML();
        }

        @Override
        public boolean isValid(int timeout) throws SQLException {
            return getRealConnection().isValid(timeout);
        }

        @Override
        public void setClientInfo(String name, String value) throws SQLClientInfoException {
            getRealConnection().setClientInfo(name, value);
        }

        @Override
        public void setClientInfo(Properties properties) throws SQLClientInfoException {
            getRealConnection().setClientInfo(properties);
        }

        @Override
        public String getClientInfo(String name) throws SQLException {
            return getRealConnection().getClientInfo(name);
        }

        @Override
        public Properties getClientInfo() throws SQLException {
            return getRealConnection().getClientInfo();
        }

        @Override
        public Array createArrayOf(String typeName, Object[] elements) throws SQLException {
            return getRealConnection().createArrayOf(typeName, elements);
        }

        @Override
        public Struct createStruct(String typeName, Object[] attributes) throws SQLException {
            return getRealConnection().createStruct(typeName, attributes);
        }

        @Override
        public void setSchema(String schema) throws SQLException{
            getRealConnection().setSchema(schema);
        }

        @Override
        public String getSchema() throws SQLException {
            return getRealConnection().getSchema();
        }

        @Override
        public void abort(Executor executor) throws SQLException {
            getRealConnection().abort(executor);
        }

        @Override
        public void setNetworkTimeout(Executor executor, int milliseconds) throws SQLException {
            getRealConnection().setNetworkTimeout(executor, milliseconds);
        }

        @Override
        public int getNetworkTimeout() throws SQLException {
            return getRealConnection().getNetworkTimeout();
        }

    }

    class LazyConnectionProxy extends AbstractConnectionProxy {

        private Supplier<Connection> supplier;
        private Connection target = null;

        public LazyConnectionProxy(Supplier<Connection> supplier) {
            this.supplier = supplier;
        }

        @Override
        public void close() throws SQLException {
            if (target != null) {
                System.out.println("Close connection: " + target);
                super.close();
            }
        }

        @Override
        protected Connection getRealConnection() {
            if (target == null) {
                target = supplier.get();
            }
            return target;
        }
    }


    class LazyDataSource implements DataSource {

        private String url;
        private String username;
        private String password;

        public LazyDataSource(String url, String username, String password) {
            this.url = url;
            this.username = username;
            this.password = password;
        }

        @Override
        public Connection getConnection(String username, String password) throws SQLException {
            return new LazyConnectionProxy(() -> {
                try {
                    Connection conn = DriverManager.getConnection(url, username, password);
                    System.out.println("Open connection: " + conn);
                    return conn;
                } catch (SQLException e) {
                    throw new RuntimeException(e);
                }
            });
        }

        @Override
        public Connection getConnection() throws SQLException {
            return getConnection(this.username, this.password);
        }

        @Override
        public Logger getParentLogger() throws SQLFeatureNotSupportedException {
            throw new SQLFeatureNotSupportedException();
        }

        @Override
        public <T> T unwrap(Class<T> iface) throws SQLException {
            throw new SQLFeatureNotSupportedException();
        }

        @Override
        public boolean isWrapperFor(Class<?> iface) throws SQLException {
            throw new SQLFeatureNotSupportedException();
        }

        @Override
        public PrintWriter getLogWriter() throws SQLException {
            throw new SQLFeatureNotSupportedException();
        }

        @Override
        public void setLogWriter(PrintWriter out) throws SQLException {
            throw new SQLFeatureNotSupportedException();
        }

        @Override
        public void setLoginTimeout(int seconds) throws SQLException {
            throw new SQLFeatureNotSupportedException();
        }

        @Override
        public int getLoginTimeout() throws SQLException {
            throw new SQLFeatureNotSupportedException();
        }
    }


    class PooledConnectionProxy extends AbstractConnectionProxy {

        Connection target;
        Queue<PooledConnectionProxy> idleQueue;

        public PooledConnectionProxy(Queue<PooledConnectionProxy> idleQueue, Connection target) {
            this.idleQueue = idleQueue;
            this.target = target;
        }

        @Override
        public void close() throws SQLException {
            System.out.println("Fake close and released to idle queue for future reuse: " + target);
            idleQueue.offer(this);
        }

        @Override
        protected Connection getRealConnection() {
            return target;
        }
    }


    class PooledDataSource implements DataSource {

        private String url;
        private String username;
        private String password;

        private Queue<PooledConnectionProxy> idleQueue = new ArrayBlockingQueue<>(100);

        public PooledDataSource(String url, String username, String password) {
            this.url = url;
            this.username = username;
            this.password = password;
        }

        @Override
        public Connection getConnection(String username, String password) throws SQLException {
            PooledConnectionProxy conn = idleQueue.poll();
            if (conn == null) {
                conn = openNewConnection();
            } else {
                System.out.println("Return pooled connection: " + conn.target);
            }
            return conn;
        }

        private PooledConnectionProxy openNewConnection() throws SQLException {
            Connection conn = DriverManager.getConnection(url, username, password);
            System.out.println("Open new connection: " + conn);
            return new PooledConnectionProxy(idleQueue, conn);
        }

        @Override
        public Connection getConnection() throws SQLException {
            return getConnection(this.username, this.password);
        }

        @Override
        public Logger getParentLogger() throws SQLFeatureNotSupportedException {
            throw new SQLFeatureNotSupportedException();
        }

        @Override
        public <T> T unwrap(Class<T> iface) throws SQLException {
            throw new SQLFeatureNotSupportedException();
        }

        @Override
        public boolean isWrapperFor(Class<?> iface) throws SQLException {
            throw new SQLFeatureNotSupportedException();
        }

        @Override
        public PrintWriter getLogWriter() throws SQLException {
            throw new SQLFeatureNotSupportedException();
        }

        @Override
        public void setLogWriter(PrintWriter out) throws SQLException {
            throw new SQLFeatureNotSupportedException();
        }

        @Override
        public void setLoginTimeout(int seconds) throws SQLException {
            throw new SQLFeatureNotSupportedException();
        }

        @Override
        public int getLoginTimeout() throws SQLException {
            throw new SQLFeatureNotSupportedException();
        }
    }

    @Test
    void main() throws SQLException{


        String jdbcUrl = "jdbc:mysql://localhost/learnjdbc?useSSL=false&characterEncoding=utf8";
        String jdbcUsername = "learn";
        String jdbcPassword = "learnpassword";

        DataSource lazyDataSource = new LazyDataSource(jdbcUrl, jdbcUsername, jdbcPassword);
        System.out.println("get lazy connection...");
        try (Connection conn1 = lazyDataSource.getConnection()) {
            // 并没有实际打开真正的Connection
        }

        System.out.println("get lazy connection...");
        try (Connection conn2 = lazyDataSource.getConnection()) {
            try (PreparedStatement ps = conn2.prepareStatement("SELECT * FROM students")) { // 打开了真正的Connection
                try (ResultSet rs = ps.executeQuery()) {
                    while (rs.next()) {
                        System.out.println(rs.getString("name"));
                    }
                }
            }
        }
        DataSource pooledDataSource = new PooledDataSource(jdbcUrl, jdbcUsername, jdbcPassword);
        try (Connection conn = pooledDataSource.getConnection()) {
        }
        try (Connection conn = pooledDataSource.getConnection()) {
            // 获取到的是同一个Connection
        }
        try (Connection conn = pooledDataSource.getConnection()) {
            // 获取到的是同一个Connection
        }


    }



}
