import java.lang.reflect.InvocationHandler;
import java.lang.reflect.Method;
import java.lang.reflect.Proxy;



public class SupportService {

    public static void main(String[] args) {
        add(1,2);
    }

    /**
     * uid 给 feedId 点赞
     * @param uid
     * @param feedId
     * @return
     */
    public static String add(int uid, int feedId){
        YarClient yarClient = new YarClient();

        RewardScoreService rewardScoreService = (RewardScoreService) yarClient.proxy(RewardScoreService.class);

        return rewardScoreService.support(uid, feedId);

    }

}

/**
 * 点赞的积分服务接口
 */
interface RewardScoreService{
    String support(int uid,int feedId);
}


class YarClient {

    public final Object proxy(Class type) {
        YarClientInvocationHandler handler = new YarClientInvocationHandler();
        return Proxy.newProxyInstance(type.getClassLoader(), new Class[]{type}, handler);
    }
}

final class YarClientInvocationHandler implements InvocationHandler {

    @Override
    public Object invoke(Object proxy, Method method, Object[] args) throws Throwable {

        System.out.println("这里的动态调用实现了 php 的 __call 方法");

        System.out.println("method : " + method.getName());
        for (int i = 0; i < args.length; i++) {
            System.out.println("args["+ i +"] : " + args[i]);
        }

        return null;
    }

}