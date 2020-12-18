public class Main {

    public static void main(String[] args) {

        //int[] arr = new int[10];
        //int[] scores = new int[]{232, 34, 434};
        //for(int score: scores) System.out.println(score);


        Array<Integer> arr = new Array(20);
        for(int i = 0; i < 10; i++){
            arr.addLast(i);
        }

        System.out.println(arr);

        arr.add(1, 100);
        System.out.println(arr);


        arr.addFirst(-1);
        System.out.println(arr);

        arr.removeFirst();
        System.out.println(arr);

        arr.removeLast();
        System.out.println(arr);

        arr.removeElement(100);
        System.out.println(arr);

    }
}
