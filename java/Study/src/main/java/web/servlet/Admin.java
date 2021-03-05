package web.servlet;

import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;

import java.io.IOException;
import java.io.PrintWriter;

@WebServlet("/admin")
public class Admin extends HttpServlet {

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response) throws IOException {

        response.setCharacterEncoding("UTF-8");
        String name = (String) request.getSession().getAttribute("name");
        if(name == null){
            response.sendRedirect("/login");
        }

        response.setContentType("text/html");
        PrintWriter pw = response.getWriter();
        pw.write("<h1>Hello "+ name  +"</h1><a href=\"/\">首页</a>");
        pw.flush();

    }


}
