import org.junit.jupiter.api.Test;

import javax.script.ScriptEngine;
import javax.script.ScriptEngineFactory;
import javax.script.ScriptEngineManager;
import javax.script.ScriptException;
import java.awt.*;

public class Chapter8 {


    @Test
    void main() throws ScriptException{

        {
            ScriptEngineManager manager = new ScriptEngineManager();
            ScriptEngine engine = manager.getEngineByName("nashorn");
            Object result = engine.eval("alert(2);");
            System.out.println(result);
        }

        {
            EventQueue.invokeLater(() -> {
                try{
                    var manager = new ScriptEngineManager();
                    String language = "nashorn";
                    for(ScriptEngineFactory factory : manager.getEngineFactories()){
                        System.out.println(factory.getEngineName());
                    }
                    final ScriptEngine engine = manager.getEngineByName(language);
                    if(engine == null){
                        System.err.println("no engine");
                        System.exit(1);
                    }

                }catch(Exception e){
                    e.printStackTrace();
                }
            });

            //int result = compiler.run(null, out, err, "-sourcepath", "src", "Test.java");
            //JavaCompiler.CompilationTask task = compiler.getTask(System.err, null, System.err, null, null, source);
        }


    }
}

