import org.w3c.dom.CharacterData;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.xml.sax.SAXException;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import java.io.IOException;
import java.util.Map;
import java.util.Scanner;

public class XmlTest{

}

class JSONConverter extends Common{

    public static void main(String[] args) throws ParserConfigurationException, IOException, SAXException{

        String filename;
        if(args.length == 0){
            try(var in = new Scanner(System.in)){
                p("Input file:");
                filename = in.nextLine();

            }
        }else{
            filename = args[0];
            DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();
            DocumentBuilder builder = factory.newDocumentBuilder();
            Document doc = builder.parse(filename);
            Element root = doc.getDocumentElement();
            p(convert(root, 0));

        }
    }

    public static StringBuilder convert(Node node, int level){

        if(node instanceof Element){
            return elementObject((Element) node, level);
        }else if(node instanceof CharacterData){
            //return characterString((CharacterData) node, level);
        }else{
            return pad(new StringBuilder(), level).append(jsonEscape(node.getClass().getName()));
        }
        return new StringBuilder();
    }
    private static Map<Character, String> replacements = Map.of('\b', "\\b", '\f', "\\f", '\n', "\\n", '\r', "\\r", '\t',"\\t", '"', "\\\"", '\\', "\\\\");

    public static StringBuilder jsonEscape(String str){

        var result = new StringBuilder("\"");
        for(int i = 0; i < str.length(); i++){
            char ch = str.charAt(i);
            String replacement = replacements.get(ch);
            if(replacement == null) result.append(ch);
            else result.append(replacement);
        }
        result.append("\"");
        return result;
    }

    public static StringBuilder elementObject(Element elem, int level){

        var result = new StringBuilder();
        pad(result, level).append("{\n");
        pad(result, level + 1).append("\"name\":");
        result.append(jsonEscape(elem.getTagName()));
        return result;
    }
    public static StringBuilder pad(StringBuilder builder, int level){

        for(int i = 0; i < level; i++){
            builder.append(" ");
        }
        return builder;
    }

}
