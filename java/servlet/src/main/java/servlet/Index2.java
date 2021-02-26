package servlet;

import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.Cookie;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;

import java.io.IOException;
import java.io.PrintWriter;

@WebServlet("/")
public class Index extends HttpServlet{

    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException{
        // 从HttpSession获取当前用户名:
        String user = (String) req.getSession().getAttribute("user");
        resp.setContentType("text/html");
        resp.setCharacterEncoding("UTF-8");
        resp.setHeader("X-Powered-By", "JavaEE Servlet");
        PrintWriter pw = resp.getWriter();
        pw.write("<h1>Welcome, " + (user != null ? user : "Guest") + "</h1>");
        if (user == null) {
            // 未登录，显示登录链接:
            pw.write("<p><a href=\"/login\">login</a></p>");
        } else {
            // 已登录，显示登出链接:
            pw.write("<p><a href=\"/loginout\">loginout</a></p>");
        }
        pw.flush();
    }

    public void logout(){

        //req.getSession().removeAttribute("user");
        //resp.sendRedirect("/");

        // 创建一个新的Cookie:
        Cookie cookie = new Cookie("lang", "cn");
        // 该Cookie生效的路径范围:
        cookie.setPath("/");
        // 该Cookie有效期:
        cookie.setMaxAge(8640000); // 8640000秒=100天
        // 将该Cookie添加到响应:
        //resp.addCookie(cookie);


        // 获取请求附带的所有Cookie:
        //Cookie[] cookies = req.getCookies();
        //// 如果获取到Cookie:
        //if (cookies != null) {
        //    // 循环每个Cookie:
        //    for (Cookie cookie : cookies) {
        //        // 如果Cookie名称为lang:
        //        if (cookie.getName().equals("lang")) {
        //            // 返回Cookie的值:
        //            return cookie.getValue();
        //        }
        //    }
        //}
    }

}
