#include <stdio.h>
#include <limits.h> 
#include <float.h>

// 变量声明
// extern int a, b;
// extern int c;
// extern float f;
//  

#define LENGTH 10   
#define WIDTH  5
#define NEWLINE '\n'

int main()
{
   /* 我的第一个 C 程序 */
   printf("Hello, World! \n");
   printf("int 存储大小 : %lu \n", sizeof(int));  
   printf("float 存储最大字节数 : %lu \n", sizeof(float));
   printf("float 最小值: %E\n", FLT_MIN );
   printf("float 最大值: %E\n", FLT_MAX );
   printf("精度值: %d\n", FLT_DIG );
   printf("\n");
   /* 变量定义 */
  int a, b;
  int c;
  float f;
 
  /* 初始化 */
  a = 10;
  b = 20;
  
  c = a + b;
  printf("value of c : %d \n", c);
 
  f = 70.0/3.0;
  printf("value of f : %f \n", f);
   
  printf("\n");
  printf("Hello\tWorld\n\n");
 
   printf("\n");
   int area;  
  
   area = LENGTH * WIDTH;
   printf("value of area : %d", area);
   printf("%c", NEWLINE);


   return 0;
}
