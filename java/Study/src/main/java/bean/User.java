package bean;

public class User{

    public long id;
    public String name;
    public School school;
    public String number;

    public String email;
    public String password;
    public String description;

    public User() { }

    public User(String name, String number) {
        this.name = name;
        this.number = number;
    }

    public User(long id, String name, School school) {
        this.id = id;
        this.name = name;
        this.school = school;
    }

    public User(String email, String password, String name, String description) {

        this.email = email;
        this.password = password;
        this.name = name;
        this.description = description;
    }

    public String toString() {
        return name + "/" + number;
    }

}
