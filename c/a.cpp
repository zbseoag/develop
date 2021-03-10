#include <iostream>
#include <cmath>
#include <ctime>
#include <cassert>
#include "Student.h"

using namespace std;

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


namespace SortHelper {

    //验证排序是否正确
    template<typename T> bool isSorted(T arr[], int n){

        for(int i = 0; i < n -1; i++){
            if(arr[i] > arr[i + 1]) return false;
        }
        return true;
    }

    //生成有 n 个元素的随机数组，元素取值范围为[rangeL, rangeR]
    int* generateRandomArray(int n, int rangeL, int rangeR){

        assert(rangeL <= rangeR);
        int *arr = new int[n];

        srand(time(NULL));

        for(int i = 0; i < n; i++){
            arr[i] = rand() % (rangeR - rangeL + 1) + rangeL;
        }
        return arr;
    }

    template<typename T> void testSort(string name, void(*sort)(T[], int), T arr[], int n){

        clock_t startTime = clock();
        sort(arr, n);
        clock_t endTime = clock();

        assert(isSorted(arr, n));
        std::cout << name << " (" << n <<") : " << double(endTime - startTime) / CLOCKS_PER_SEC << " s" << std::endl;

    }





}


/**
从当前区间找出最小元素，与当前第一个元素交换。
**/
template<typename T>
void selectionSort(T arr[], int n){

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



int binarySearch(int arr[], int n, int target){

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



int main(){

    int n = 10000;
    int *a = SortHelper::generateRandomArray(n, 1, n);
    //Student a[4] = {{"A", 90}, {"B", 70}, {"C", 80}, {"X", 70} };

    SortHelper::testSort("Selection Sort", selectionSort, a, n);

    delete[] a;
    return 0;

}
