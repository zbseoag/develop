package DesignPattern;

import java.util.Scanner;

/**
 * 允许一个对象在其内部状态改变时改变它的行为。对象看起来似乎修改了它的类。
 *
 */
public class States {

    static class BotContext {

        private State state = new DisconnectedState();

        public String chat(String input) {

            //根据聊天内容切换状态
            if ("hello".equalsIgnoreCase(input)) {

                state = new ConnectedState();
                return state.init();

            } else if ("bye".equalsIgnoreCase(input)) {
                state = new DisconnectedState();
                return state.init();

            }

            //不同状态，有不同聊天返回内容
            return state.reply(input);
        }
    }

    static class ConnectedState implements State {

        @Override
        public String init() {
            return "Hello, I'm Bob.";
        }

        @Override
        public String reply(String input) {

            if (input.endsWith("?")) {
                return "Yes. " + input.substring(0, input.length() - 1) + "!";
            }
            if (input.endsWith(".")) {
                return input.substring(0, input.length() - 1) + "!";
            }
            return input.substring(0, input.length() - 1) + "?";
        }
    }

    static class DisconnectedState implements State {

        @Override
        public String init() {
            return "Bye!";
        }

        @Override
        public String reply(String input) {
            return "";
        }
    }

    interface State {

        String init();
        String reply(String input);
    }

    public static void main(String[] args) {

        @SuppressWarnings("resource")
        Scanner scanner = new Scanner(System.in);
        BotContext bot = new BotContext();

        for (;;) {
            System.out.print("> ");
            String input = scanner.nextLine();
            String output = bot.chat(input);
            System.out.println(output.isEmpty() ? "(no reply)" : "< " + output);
        }

    }


}
