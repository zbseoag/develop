package servlet;

import bean.School;
import jakarta.servlet.*;
import jakarta.servlet.http.*;
import jakarta.servlet.annotation.*;

import java.io.IOException;

@WebServlet(name = "User", value = "/User")
public class User extends HttpServlet{

    @Override
    protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException{

        School school = new School("No.1 Middle School", "101 North Street");
        bean.User user = new bean.User(123, "Bob", school);
        request.setAttribute("user", user);
        request.getRequestDispatcher("/user.jsp").forward(request, response);

    }

    @Override
    protected void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException{

    }
}
