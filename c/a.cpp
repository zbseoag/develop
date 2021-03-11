#include <iostream>
#include <cmath>
#include <ctime>
#include <cassert>
#include "Student.h"


template<typename T> void print(T arr[], int n){
    for(int i = 0; i < n; i++){
        std::cout << arr[i];
    }
}

template<typename T> void println(T arr[], int n){
    for(int i = 0; i < n; i++){
        std::cout << arr[i] << std::endl;
    }
}


namespace Search {

    //二分查找法
    int binary(int arr[], int n, int target){

        int l = 0;
        int r = n - 1;
        while(l <= r){

            int mid = l + (r - l) / 2;
            if(arr[mid] == target) return mid;
            if(arr[mid] > target){
                r = mid - 1;
            }else{
                l = mid + 1;
            }
        }
        return -1;
    }


}


namespace Sort {

    //验证排序是否正确
    template<typename T> bool isSorted(T arr[], int n){

        for(int i = 0; i < n -1; i++){
            if(arr[i] > arr[i + 1]) return false;
        }
        return true;
    }

    //生成有 n 个元素的随机数组，元素取值范围为[rangeL, rangeR]
    int* create(int n, int rangeL, int rangeR){

        assert(rangeL <= rangeR);
        int *arr = new int[n];

        srand(time(NULL));

        for(int i = 0; i < n; i++){
            arr[i] = rand() % (rangeR - rangeL + 1) + rangeL;
        }
        return arr;
    }

    //生成近乎有序的数组
    int* createNearlyOrderArray(int n, int times){

        int *arr = new int[n];
        for(int i = 0; i < n; i++){
            arr[i] = i;
        }

        srand(time(NULL));

        for(int i = 0; i < times; i++){
            int x = rand() % n;
            int y = rand() % n;
            swap(arr[x], arr[y]);
        }

        return arr;

    }

    template<typename T> void test(string name, void(*sort)(T[], int), T arr[], int n){

        clock_t startTime = clock();
        sort(arr, n);
        clock_t endTime = clock();

        assert(isSorted(arr, n));
        std::cout << name << " (" << n <<") : " << double(endTime - startTime) / CLOCKS_PER_SEC << " s" << std::endl;

    }

    int* copyIntArray(int a[], int n){

        int* arr = new int[n];
        copy(a, a + n, arr);
        return arr;
    }


    //插入排序 O(n^2)
    //当排序元素近乎有序,内层循环可以提前结束,所以排序效率非常高
    template<typename T> void insertion(T arr[], int n){

        //从第二个元素开始
        for(int i = 1; i < n; i++){

            //第 i 个元素与他前面的元素一一比较，如果比它前面的元素小，则交换，再比较更前面的，如果发现比它前面的大，表示位置正合适，提前终止后续比较
            //交换比较耗时
            for(int j = i; j > 0 && arr[j] < arr[j - 1]; j--){
                swap(arr[j], arr[j - 1]);
            }

        }

    }

    //插入排序1 O(n^2)
    template<typename T> void insertion1(T arr[], int n){

        //从第二个元素开始
        for(int i = 1; i < n; i++){

            //第 i 个元素与他前面的元素一一比较，如果比它前面的元素小，则交换，再比较更前面的，如果发现比它前面的大，表示位置正合适，提前终止后续比较

            T e = arr[i];
            int j; //e 元素应该插入的位置
            for(j = i; j > 0 && arr[j - 1] > e; j--){
                arr[j] = arr[j - 1];
            }
            arr[j] = e;
        }

   }


    /**
    选择排序 O(n^2)
    从当前区间找出最小元素，与当前第一个元素交换。
    **/
    template<typename T> void selection(T arr[], int n){

        for(int i = 0; i < n; i++){

            //寻找从 [i, n) 区间的最小值
            int min = i;
            for(int j = i + 1; j < n; j++){
                if(arr[j] < arr[min]) min = j;
            }

            //当前第一个元素与区间最小元素交换
            swap(arr[i], arr[min]);
        }

    }

    //冒泡排序法
    template<typename T> void bubble(){


    }


    //希尔排序法
    template<typename T> void shell(){


    }



    //将 arr[l ... mid] 和 arr[mid+1 ... r] 两部分进行归并
    template<typename T> void _merge_(T arr[], int l, int mid, int r){

        T aux[r-l+1];
        for(int i = l; i<= r; i++){
            aux[i-l] = arr[i];
        }
        int i = l, j = mid + 1;
    }

    //递归使用归并排序, 对 arr[l ... r] 的范围进行排序
    template<typename T> void _merge(arr, int l, int r){
        if(l >= r) return;
        int mid = (l + r) / 2;
        _merge(arr, l, mid);
        _merge(arr, mid + 1, r);
        _merge_(arr, l, mid, r);
    }

    //归并排序法
    template<typename T> void merge(T arr[], int n){


        _merge(arr, 0, n-1);
    }





}


int main(){

    int n = 10000;
    int *arr1 = Sort::createNearlyOrderArray(n, 10);
    int *arr2 = Sort::copyIntArray(arr1, n);

    Sort::test("Sort::insert", Sort::insert1, arr1, n);
    Sort::test("Sort::selection", Sort::selection, arr2, n);

    delete[] arr1;
    delete[] arr2;

    return 0;

}
