public class Student{

    private String name;
    private int score;

    public Student(String name, int score){
        this.name = name;
        this.score = score;
    }

    @Override
    public String toString(){
        return String.format("Student(name:%s, socre:%d)", name, score);
    }

    public static void main(String[] args){
        Array<Student> arr = new Array(2);

        System.out.println(arr);

        arr.addLast(new Student("Alice", 100));
        arr.addLast(new Student("Bob", 60));
        arr.addLast(new Student("Charlie", 87));
        arr.addLast(new Student("Charlie", 87));
        arr.addLast(new Student("Charlie", 87));
        System.out.println(arr);

        arr.removeLast();


        System.out.println(arr);

        arr.removeLast();


        System.out.println(arr);
        arr.removeLast();


        System.out.println(arr);
        arr.removeLast();




    }

}
