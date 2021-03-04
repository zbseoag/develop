import org.junit.jupiter.api.*;
import org.junit.jupiter.api.function.Executable;

import static org.junit.jupiter.api.Assertions.*;

public class CalculatorTest {

    Calculator calculator;

    static int count = 0;

    @BeforeAll
    public static void initCount() {
        count = 100;
    }

    @AfterAll
    public static void dropCount() {
        count = 0;
    }


    @BeforeEach
    public void setUp() {
        this.calculator = new Calculator();
    }

    @AfterEach
    public void tearDown() {
        this.calculator = null;
    }

    @Test
    void testAdd() {
        assertEquals(100, this.calculator.add(100));
        assertEquals(150, this.calculator.add(50));
        assertEquals(130, this.calculator.add(-20));
    }

    @Test
    void testSub() {
        assertEquals(-100, this.calculator.sub(100));
        assertEquals(-150, this.calculator.sub(50));
        assertEquals(-130, this.calculator.sub(-20));
    }

    //对异常进行测试
    @Test
    void testNegative() {

        assertThrows(IllegalArgumentException.class, new Executable() {
            @Override
            public void execute() throws Throwable {
                Factorial.fact(-1);
            }
        });
    }

    @Test
    void testNegativeV2() {
        assertThrows(IllegalArgumentException.class, () -> {
            Factorial.fact(-1);
        });
    }

    //@Disabled
    // @EnabledOnOs(OS.WINDOWS)
    // @EnabledOnOs({ OS.LINUX, OS.MAC })
    // @DisabledOnJre(JRE.JAVA_8) //Java 9或更高版本执行的测试
    // @EnabledIfSystemProperty(named = "os.arch", matches = ".*64.*") //64位操作系统上执行的测
    //@EnabledIfEnvironmentVariable(named = "DEBUG", matches = "true") //需要传入环境变量DEBUG=true才能执行的测试


    //@ParameterizedTest
    //@ValueSource(ints = { 0, 1, 5, 100 })
    //void testAbs(int x) {
    //    assertEquals(x, Math.abs(x));
    //}

}