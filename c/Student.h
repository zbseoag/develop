
#ifndef SELECTIONSORT_STUDENT_H
    #define SELECTIONSORT_STUDENT_H

    #include <iostream>
    #include <cmath>
    #include <ctime>
    using namespace std;

    struct Student {

        string name;
        int score;

        bool operator < (const Student &other){
            return score == other.score ? false : score < other.score;
        }

        friend ostream & operator << (ostream &os, const Student &student){
            os<<"Student: " << student.name << " " << student.score << endl;
            return os;
        }

    };


    //Student arr1[4] = {{"A", 90}, {"B", 70}, {"C", 80}, {"X", 70} };

#endif

