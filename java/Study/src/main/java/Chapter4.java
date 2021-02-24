import java.awt.*;
import java.io.*;
import java.net.*;
import java.nio.charset.StandardCharsets;
import java.util.Base64;
import java.util.List;
import java.util.Map;
import java.util.Scanner;


public class Chapter4 extends Common {

    public static void main(String[] args) throws IOException{
        socket();

        var url = new URL("www.baidu.com");
        URLConnection connection = url.openConnection();
        connection.connect();
        connection.setDoOutput(true);
        Base64.Encoder encoder = Base64.getEncoder();
        String encoding = encoder.encodeToString("aaa".getBytes(StandardCharsets.UTF_8));
        connection.setRequestProperty("Authorization", "Base"+ encoding);
        String key = connection.getHeaderFieldKey(2);
        Map<String, List<String>> headerFields = connection.getHeaderFields();

    }


    public static void socket() throws IOException{
        var socket = new Socket("time-a.nist.gov", 13);
        socket.setSoTimeout(1000);

        try{
            InputStream in = socket.getInputStream();
        }catch(SocketTimeoutException e){
            throw e;
        }

        var s = new Socket();
        s.connect(new InetSocketAddress("www.baidu.com", 80), 1000);

        InetAddress address = InetAddress.getByName("www.baidu.com");
        byte[] addressbyte = address.getAddress();

        InetAddress[] addresses = InetAddress.getAllByName("www.baidu.com");

        p(InetAddress.getLocalHost());//获取本地ip地址


    }



    public static void code() throws IOException{
        var s = new Socket("time-a.nist.gov", 13);
        InputStream in = s.getInputStream();

    }


}

class SocketTest extends Common {

    public static void main(String[] args) throws IOException{
        try(var s = new Socket("time-a.nist.gov", 13); var in = new Scanner(s.getInputStream(), StandardCharsets.UTF_8)){

            while(in.hasNext()){
                String line = in.nextLine();
                p(line);
            }

        }

        try(var socket = new Socket()){
            var in = new Scanner(socket.getInputStream(), StandardCharsets.UTF_8);
            var writer = new PrintWriter(socket.getOutputStream());
            writer.println("abc");
            writer.flush();
            socket.shutdownOutput();
            while(in.hasNextLine()){
                String line = in.nextLine();
            }
        }

    }
}

class EchoServer{

    public static void main(String[] args) throws IOException{
        try(var s = new ServerSocket(8189)){
            try(Socket incoming = s.accept()){
                InputStream instream = incoming.getInputStream();
                OutputStream outstream = incoming.getOutputStream();
                try(var in = new Scanner(instream, StandardCharsets.UTF_8)){
                    var out = new PrintWriter(new OutputStreamWriter(outstream, StandardCharsets.UTF_8), true);
                    out.println("hello! enter bye to exit.");

                    var done = false;
                    while(!done && in.hasNextLine()){
                        String line = in.nextLine();
                        out.println("echo " + line);
                        if(line.trim().equals("bye")) done = true;

                    }
                }
            }
        }
    }
}

class ThreadEchoServer extends Common{

    public static void main(String[] args) throws IOException{

        try(var s = new ServerSocket(8189)){
            int i = 1;
            while(true){
                Socket incoming = s.accept();
                p("Spawning" + i);
                Runnable r = new ThreadEchoHandler(incoming);
                var t = new Thread(r);
                t.start();
                i++;
            }
        }
    }
}

class ThreadEchoHandler implements Runnable{

    private Socket incomming;
    public ThreadEchoHandler(Socket incomming){

        this.incomming = incomming;
    }

    @Override
    public void run(){

        try(InputStream inputStream = incomming.getInputStream();
            OutputStream outputStream = incomming.getOutputStream();
            var in = new Scanner(inputStream, StandardCharsets.UTF_8);
            var out = new PrintWriter(new OutputStreamWriter(outputStream, StandardCharsets.UTF_8), true)
        ){
            out.println("hello enter exit to exit");
            var done = false;
            while(!done && in.hasNextLine()){
                String line = in.nextLine();
                out.println("output: " + line);
                if(line.trim().equals("exit")) done = true;
            }


        }catch(IOException e){
            e.printStackTrace();
        }
    }
}

class InterruptibleSocket extends Common{

    public static void main(String[] args){
        EventQueue.invokeLater(() -> {


        });
    }
}

class UrlConnectTest extends Common{

    public static void main(String[] args) throws IOException{

        var url = new URL("http://www.baidu.com");
        URLConnection connection = url.openConnection();

        String input = "tom:123456";
        Base64.Encoder encoder = Base64.getEncoder();
        String encoding = encoder.encodeToString(input.getBytes(StandardCharsets.UTF_8));
        connection.setRequestProperty("Authorization", "Base " + encoding);
        connection.connect();
        Map<String, List<String>> headers = connection.getHeaderFields();
        for(Map.Entry<String, List<String>> entry : headers.entrySet()){
            String key = entry.getKey();
            for(String value : entry.getValue()){
                System.out.println(key + " : " + value);
            }
        }

        connection.getContentType();
        connection.getContentLength();
        connection.getDate();

        try(var in = new Scanner(connection.getInputStream(), connection.getContentEncoding())){
            for(int n = 1; in.hasNextLine() && n <= 10; n++){
                p(in.nextLine());
            }
        }

//        connection.setDoOutput(true);
//        var out = new PrintWriter(connection.getOutputStream(), StandardCharsets.UTF_8);
//        URLEncoder.encode("3333", StandardCharsets.UTF_8);
//        out.close();


    }
}