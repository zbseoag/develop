package web.servlet;

import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.Cookie;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;

import java.io.IOException;
import java.io.PrintWriter;
import java.util.Map;
import java.util.Set;
import java.util.concurrent.ConcurrentHashMap;

@WebServlet("/")
public class Index extends HttpServlet {

    private String message;
    private final Map<Integer, String> map = new ConcurrentHashMap<>();
    private static final Set<String> LANGUAGES = Set.of("en", "zh");

    public void init(){
        message = "Index Page!";
    }

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response) throws IOException {

        String lang = request.getParameter("lang");
        if (lang != null && LANGUAGES.contains(lang)) {
            // 创建一个新的Cookie
            Cookie cookie = new Cookie("lang", lang);
            // 该Cookie生效的路径范围
            cookie.setPath("/");
            // 该Cookie有效期
            cookie.setMaxAge(8640000); // 8640000秒
            // 将该Cookie添加到响应
            response.addCookie(cookie);
        }


        response.setContentType("text/html");
        response.setCharacterEncoding("UTF-8");

        PrintWriter pw = response.getWriter();
        pw.write("<h1>"+ message +"</h1>");

        String name = (String) request.getSession().getAttribute("name");
        pw.write("<h1>Welcome, " + (name != null ? name : "Guest") + "</h1>");

        if (name == null) {
            pw.write("<p><a href=\"/login\">登录</a></p>");
        } else {
            pw.write("<p><a href=\"/logout\">退出</a></p>");
        }
        pw.flush();

    }

    public void doPost(HttpServletRequest request, HttpServletResponse response) throws IOException, ServletException{

        // 注意读写map字段是多线程并发的
        this.map.put(1, "jim");

        //重定向
        String name = request.getParameter("name");//获取 GET 参数
        response.sendRedirect("/hello" + (name == null ? "" : "?name=" + name));

        //header 跳转
        response.setStatus(HttpServletResponse.SC_MOVED_PERMANENTLY); //301永久重定向
        response.setHeader("Location", "/hello");

        //内部转发
        request.getRequestDispatcher("/hello").forward(request, response);

    }

    private String languageFromCookie(HttpServletRequest req){

        Cookie[] cookies = req.getCookies();

        if (cookies != null) {
            for(Cookie cookie : cookies) {
                if (cookie.getName().equals("lang")) {
                    return cookie.getValue();
                }
            }
        }
        return "cn";

    }



}
