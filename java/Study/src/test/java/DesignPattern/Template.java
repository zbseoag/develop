package DesignPattern;

import io.lettuce.core.RedisClient;
import io.lettuce.core.api.StatefulRedisConnection;
import io.lettuce.core.api.sync.RedisCommands;
import org.junit.jupiter.api.Test;

import java.util.HashMap;
import java.util.Map;

public class Template {

    abstract class AbstractSetting {

        public final String getSetting(String key) {
            String value = lookupCache(key);
            if (value == null) {
                value = readFromDatabase(key);
                System.out.println("[DEBUG] load from db: " + key + " = " + value);
                putIntoCache(key, value);
            } else {
                System.out.println("[DEBUG] load from cache: " + key + " = " + value);
            }
            return value;
        }

        protected abstract String lookupCache(String key);

        protected abstract void putIntoCache(String key, String value);

        private String readFromDatabase(String key) {
            return Integer.toHexString(0x7fffffff & key.hashCode());
        }
    }

    class LocalSetting extends AbstractSetting {

        Map<String, String> cache = new HashMap<>();

        @Override
        protected String lookupCache(String key) {
            return cache.get(key);
        }

        @Override
        protected void putIntoCache(String key, String value) {
            cache.put(key, value);
        }

    }

    class RedisSetting extends AbstractSetting {

        private RedisClient client = RedisClient.create("redis://localhost:6379");

        @Override
        protected String lookupCache(String key) {
            try (StatefulRedisConnection<String, String> connection = client.connect()) {
                RedisCommands<String, String> commands = connection.sync();
                return commands.get(key);
            }
        }

        @Override
        protected void putIntoCache(String key, String value) {
            try (StatefulRedisConnection<String, String> connection = client.connect()) {
                RedisCommands<String, String> commands = connection.sync();
                commands.set(key, value);
            }
        }
    }




    @Test
    void main() throws Exception {

        AbstractSetting setting1 = new LocalSetting();
        System.out.println("test = " + setting1.getSetting("test"));
        System.out.println("test = " + setting1.getSetting("test"));
        AbstractSetting setting2 = new RedisSetting();
        System.out.println("autosave = " + setting2.getSetting("autosave"));
        System.out.println("autosave = " + setting2.getSetting("autosave"));
    }

}
