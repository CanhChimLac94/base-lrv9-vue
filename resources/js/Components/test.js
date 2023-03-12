// Bài 1:

function tinhTong(n){
  if(n <= 1) return n;
  return n + tinhTong(n - 1);
}

// Bài 2:

const dataList = [1, 25, 7, -7, -3, 12, -22, 0];

function sortASC(list){
  return list.sort();
}