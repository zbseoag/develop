package web.servlet;

import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import jakarta.servlet.http.HttpSession;

import java.io.IOException;
import java.io.PrintWriter;
import java.util.Map;

@WebServlet("/login")
public class Login extends HttpServlet{

    // 模拟一个数据库
    public Map<String, String> users = Map.of("bob", "123", "alice", "123", "tom", "123");

    protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException{

        response.setContentType("text/html");
        response.setCharacterEncoding("UTF-8");
        PrintWriter pw = response.getWriter();
        pw.write("<h1>登录</h1>");
        pw.write("<form action=\"/login\" method=\"post\">");
        pw.write("<p>Username: <input name=\"username\"></p>");
        pw.write("<p>Password: <input name=\"password\" type=\"password\"></p>");
        pw.write("<p><button type=\"submit\">登录</button>&nbsp;<a href=\"/\">取消</a></p>");
        pw.write("</form>");
        pw.flush();

    }

    protected void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {

        String name = request.getParameter("username");
        String password = request.getParameter("password");

        if (name != null && password.equals(users.get(name))) {
            //登录成功
            HttpSession session = request.getSession();
            session.setAttribute("name", name);
            session.setAttribute("password", password);

            response.sendRedirect("/admin");
        } else {
            response.sendError(HttpServletResponse.SC_FORBIDDEN);
        }
    }


}
