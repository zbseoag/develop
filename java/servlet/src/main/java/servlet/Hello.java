package servlet;

import java.io.*;
import java.util.Map;
import java.util.concurrent.ConcurrentHashMap;

import jakarta.servlet.ServletException;
import jakarta.servlet.http.*;
import jakarta.servlet.annotation.*;

@WebServlet("/hello-servlet")
public class Hello extends HttpServlet{

    private String message;

    private Map<String, String> map = new ConcurrentHashMap<>();


    public void init(){
        message = "Hello World Servlet";
    }

    public void doGet(HttpServletRequest request, HttpServletResponse response) throws IOException, ServletException{

        // 注意读写map字段是多线程并发的:
        //this.map.put(key, value);

        // 发送重定向响应
        //String name = request.getParameter("name");//获取 GET 参数
        //response.sendRedirect("/hello" + (name == null ? "" : "?name=" + name));

        //response.setStatus(HttpServletResponse.SC_MOVED_PERMANENTLY); //301永久重定向
        //response.setHeader("Location", "/hello");

        //Forward 内部转发
        //request.getRequestDispatcher("/hello").forward(request, response);

        request.getQueryString();
        request.getSession();

        response.setContentType("text/html");

        PrintWriter out = response.getWriter();
        out.println("<html><body>");
        out.println("<h1>" + message + "</h1>");
        out.println("</body></html>");
    }

    public void destroy(){

    }
}