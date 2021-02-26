import javax.script.ScriptEngine;
import javax.script.ScriptEngineFactory;
import javax.script.ScriptEngineManager;
import javax.script.ScriptException;
import java.awt.*;

public class Chapter8 extends Common{

    public static void main(String[] args) throws ScriptException{

        ScriptEngineManager manager = new ScriptEngineManager();
        ScriptEngine engine = manager.getEngineByName("nashorn");
        Object result = engine.eval("alert(2);");
        p(result);

    }
}

class ScriptTest extends Common{

    public static void main(String[] args){

        EventQueue.invokeLater(() -> {
            try{
                var manager = new ScriptEngineManager();
                String language = "nashorn";
                for(ScriptEngineFactory factory : manager.getEngineFactories()){
                    p(factory.getEngineName());
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

