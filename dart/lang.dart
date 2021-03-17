


import 'dart:html';

void main(List args) {

  var list = ["2", "e"];
  var list2 = listTimes(list, (str){ return str * 3;});
  //print(list2);

  var rect = new Rectangle();
  rect.height = 10;
  rect.width = 4;
  print(rect.area);
  rect.area = 200;
  print(rect.width);

  //persion?.work();
  //(obj as Persion).work();
  //if(persion is! Persion) persion.work();  
  //persion..name = "tom" ..age = 111 ..work();



}

List listTimes(List list, String times(str)){
  for(var index = 0; index < list.length; index++){
    list[index] = times(list[index]);
  }
  return list;
}

class Rectangle {

  num width = 0, height = 0;

  //计算属性
  num get area{
    return width * height;
  }

  set area(value){
    width = value / 20;
  }

  String call(){
    return "obj call as function.";
  }


  bool operator > (Rectangle other){
    return this.area > other.area;
  }


  num operator [] (String str){

    if(str == "width") return width;
    return 0;

  }

  @override
  bool operator == (Object other){
    return identical(this, other) || other is Rectangle && runtimeType == other.runtimeType && area == other.area;
  }

  @override
  int get hashCode => area.hashCode;
  

}







