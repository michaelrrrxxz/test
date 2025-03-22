import java.util.Scanner;

public class Calculator {
    private int solution;
    private int x;
    private int y;
    private char operator;

    public Calculator() {
        solution = 0;
    }

    // public int addition(int a, int b) {
    //     return a + b;
    // }

    // public int subtraction(int a, int b) {
    //     return a - b;
    // }

    // public int multiplication(int a, int b) {
    //     return a * b;
    // }

    // public int division(int a, int b) {
    //     if (b != 0) {
    //         solution = a / b;
    //     } else {
    //         System.out.println("Error: Division by zero.");
    //         solution = 0;
    //     }
        return solution;
    // }

    public void main(String[] args) {
        Scanner scanner = new Scanner(System.in);

        System.out.println("What operation? ('+', '-', '*', '/')"); 
        operator = scanner.next().charAt(0);

        System.out.println("Enter two numbers:");
        System.out.print("Operand 1: ");
        a = scanner.nextInt();
        
        System.out.print("Operand 2: ");
        b = scanner.nextInt();

        switch (operator) {
            case '+':
                System.out.println("Result: " + addition(a, b));
                break;
            case '-':
                System.out.println("Result: " + subtraction(a, b));
                break;
            case '*':
                System.out.println("Result: " + multiplication(a, b));
                break;
            case '/':
                System.out.println("Result: " + division(a, b));
                break;
            default:
                System.out.println("Invalid operator.");
        }

        scanner.close();
    }
}
