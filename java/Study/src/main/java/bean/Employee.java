package bean;

import java.io.Serializable;

class Employee implements Serializable{

    public String name;
    public double salary;
    public String birthday;

    public Employee(String name, double salary, String birthday){
        this.name = name;
        this.salary = salary;
        this.birthday = birthday;
    }

    @Override
    public String toString(){
        return "{name:" + name +", salary:"+ salary + ", birthday:" + birthday + "}";
    }

}


class Manger extends Employee {

    public Employee secretary;

    public Manger(String name, double salary, String birthday) {
        super(name, salary, birthday);
    }

    public void setSecretary(Employee secretary){
        this.secretary = secretary;
    }
}
