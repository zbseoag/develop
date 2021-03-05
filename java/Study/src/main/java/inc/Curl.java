package inc;

import java.io.BufferedReader;
import java.io.DataOutputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLEncoder;
import java.util.HashMap;
import java.util.Map;

public class Curl {

    public String url = "";

    public String method = "get";

    public Map<String, String> data = new HashMap<String, String>();

    public Map<String, String> header = new HashMap<String, String>();

    public int responseCode;


    public Curl url(String value){

        this.url = value;
        return this;
    }


    public Curl data(String value){

        data.put("", value);
        return this;
    }


    public Curl data(String key, String value){

        data.put(key, value);
        return this;
    }


    public String post() throws Exception {

        method("post");
        return exec();
    }

    public String get() throws Exception {

        method("get");
        return exec();
    }

    public Curl method(String value){

        this.method = value;
        return this;
    }

    public Curl header(String name, String value){

        header.put(name, value);
        return this;
    }


    public String  exec() throws Exception {

        HttpURLConnection con = (HttpURLConnection) new URL(url).openConnection();

        //添加请求头
        con.setRequestMethod(method.toUpperCase());
        for(Map.Entry entry : header.entrySet()){
            con.setRequestProperty(entry.getKey().toString(), entry.getValue().toString());
        }

        //添加请求参数
        String param = data.size() == 1 && data.containsKey("")? data.get("") : toQueryString(data);

        con.setDoOutput(true);
        DataOutputStream wr = new DataOutputStream(con.getOutputStream());
        wr.writeBytes(param);
        wr.flush();
        wr.close();

        responseCode = con.getResponseCode();

        BufferedReader in = new BufferedReader(new InputStreamReader(con.getInputStream()));

        String inputLine;
        StringBuffer content = new StringBuffer();

        while((inputLine = in.readLine()) != null){
            content.append(inputLine);
        }
        in.close();

        return content.toString();



    }


    public static String toQueryString(Map<?, ?> data) throws Exception {

        return toQueryString(data, "&", "=");
    }

    public static String toQueryString(Map<?, ?> data, String separator) throws Exception {

        return toQueryString(data, separator, "=");
    }

    /**
     * Map 转查询字符串
     * @param data 数据
     * @param separator 分割符
     * @param equal 键值对连接符
     * @return String
     * @throws Exception
     */
    public static String toQueryString(Map<?, ?> data, String separator, String equal) throws Exception {

        StringBuffer query = new StringBuffer();

        for(Map.Entry<?, ?> pair : data.entrySet()){

            if(pair.getKey().equals("")) continue;
            query.append(pair.getKey() + equal);
            query.append(URLEncoder.encode(pair.getValue().toString(), "UTF-8" ) + separator);
        }

        if(query.length () > 0){
            query.deleteCharAt(query.length () - 1);
        }

        return query.toString();
    }



}
