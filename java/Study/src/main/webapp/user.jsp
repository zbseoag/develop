<%@ page import="bean.*"%>
<%
    Student student = (Student) request.getAttribute("user");
%>

<html>
<head>
    <title>Hello World</title>
</head>
<body>
<h1>Hello <%= student.name %>!</h1>
<p>School Name:
    <span style="color:red">
        <%= student.school %>
    </span>
</p>
</body>
</html>
