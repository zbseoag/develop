package web.servlet;

import bean.Student;
import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.HttpServlet;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;

import java.io.IOException;

@WebServlet("user")
public class User extends HttpServlet{

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException{

        Student student = new Student("Bob", "No.1 Middle School");
        request.setAttribute("user", student);
        request.getRequestDispatcher("/user.jsp").forward(request, response);

    }


}
