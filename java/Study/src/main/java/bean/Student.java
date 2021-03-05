package bean;

public class Student {

    public String name;
    public String school;
    public int score;

    public Student(){

    }

    public Student(String name, String school){
        this.name = name;
        this.school = school;
    }

    public Student(String name, int score){
        this.name = name;
        this.score = score;
    }

    public Student setName(String name){
        this.name = name;
        return this;
    }

    public Student setScore(int score){
        this.score = score;
        return this;
    }

    public Student setSchool(String school){
        this.school = school;
        return this;
    }
}
