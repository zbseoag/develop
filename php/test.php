<?php

//$redis = new Redis();
//$redis->connect('localhost', 6379);
//$redis->auth('app123456'); //密码验证


function timeWindow($rule, $redis, $zkey) {


    foreach($rule as $key => $item){

        $score = time();
        $redis->multi();
        $redis->zRemRangeByScore($zkey, 0, $score - $key);//移除窗口以外的数据
        $redis->zRange($zkey, 0, -1, true);
        $record = $redis->exec();

        p($record);
        if(empty($record[1]) || count($record[1]) < $item){
            $redis->zAdd($zkey, $score, mt_rand(1000, 9999) . substr($score, -5));
            p('==================================================> true');
            return true;

        }else{
            continue;
        }

    }

    p(false);
    return false;

}


$rules = array(
    10 => 2,
    20 => 3,
);


//
//while(1){
//
//    timeWindow($rules, $redis, 'aaaa' . 1);
//    sleep(1);
//}
//


/*
 * Reflection
•Reflection::export — Exports
•Reflection::getModifierNames — 获取修饰符的名称

 * public static Reflection::export( Reflector $reflector[, bool $return = false] ) : string
 * public static Reflection::getModifierNames( int $modifiers) : array
 *
 *
•ReflectionClass::__construct — 初始化 ReflectionClass 类
•ReflectionClass::export — 导出一个类
•ReflectionClass::getConstant — 获取定义过的一个常量
•ReflectionClass::getConstants — 获取一组常量
•ReflectionClass::getConstructor — 获取类的构造函数
•ReflectionClass::getDefaultProperties — 获取默认属性
•ReflectionClass::getDocComment — 获取文档注释
•ReflectionClass::getEndLine — 获取最后一行的行数
•ReflectionClass::getExtension — 根据已定义的类获取所在扩展的 ReflectionExtension 对象
•ReflectionClass::getExtensionName — 获取定义的类所在的扩展的名称
•ReflectionClass::getFileName — 获取定义类的文件名
•ReflectionClass::getInterfaceNames — 获取接口（interface）名称
•ReflectionClass::getInterfaces — 获取接口
•ReflectionClass::getMethod — 获取一个类方法的 ReflectionMethod。
•ReflectionClass::getMethods — 获取方法的数组
•ReflectionClass::getModifiers — 获取类的修饰符
•ReflectionClass::getName — 获取类名
•ReflectionClass::getNamespaceName — 获取命名空间的名称
•ReflectionClass::getParentClass — 获取父类
•ReflectionClass::getProperties — 获取一组属性
•ReflectionClass::getProperty — 获取类的一个属性的 ReflectionProperty
•ReflectionClass::getReflectionConstant — Gets a ReflectionClassConstant for a class's constant
•ReflectionClass::getReflectionConstants — Gets class constants
•ReflectionClass::getShortName — 获取短名
•ReflectionClass::getStartLine — 获取起始行号
•ReflectionClass::getStaticProperties — 获取静态（static）属性
•ReflectionClass::getStaticPropertyValue — 获取静态（static）属性的值
•ReflectionClass::getTraitAliases — 返回 trait 别名的一个数组
•ReflectionClass::getTraitNames — 返回这个类所使用 traits 的名称的数组
•ReflectionClass::getTraits — 返回这个类所使用的 traits 数组
•ReflectionClass::hasConstant — 检查常量是否已经定义
•ReflectionClass::hasMethod — 检查方法是否已定义
•ReflectionClass::hasProperty — 检查属性是否已定义
•ReflectionClass::implementsInterface — 接口的实现
•ReflectionClass::inNamespace — 检查是否位于命名空间中
•ReflectionClass::isAbstract — 检查类是否是抽象类（abstract）
•ReflectionClass::isAnonymous — 检查类是否是匿名类
•ReflectionClass::isCloneable — 返回了一个类是否可复制
•ReflectionClass::isFinal — 检查类是否声明为 final
•ReflectionClass::isInstance — 检查类的实例
•ReflectionClass::isInstantiable — 检查类是否可实例化
•ReflectionClass::isInterface — 检查类是否是一个接口（interface）
•ReflectionClass::isInternal — 检查类是否由扩展或核心在内部定义
•ReflectionClass::isIterable — Check whether this class is iterable
•ReflectionClass::isIterateable — 检查是否可迭代（iterateable）
•ReflectionClass::isSubclassOf — 检查是否为一个子类
•ReflectionClass::isTrait — 返回了是否为一个 trait
•ReflectionClass::isUserDefined — 检查是否由用户定义的
•ReflectionClass::newInstance — 从指定的参数创建一个新的类实例
•ReflectionClass::newInstanceArgs — 从给出的参数创建一个新的类实例。
•ReflectionClass::newInstanceWithoutConstructor — 创建一个新的类实例而不调用它的构造函数
•ReflectionClass::setStaticPropertyValue — 设置静态属性的值
•ReflectionClass::__toString — 返回 ReflectionClass 对象字符串的表示形式。


•ReflectionClassConstant::__construct — Constructs a ReflectionClassConstant
•ReflectionClassConstant::export — Export
•ReflectionClassConstant::getDeclaringClass — Gets declaring class
•ReflectionClassConstant::getDocComment — Gets doc comments
•ReflectionClassConstant::getModifiers — Gets the class constant modifiers
•ReflectionClassConstant::getName — Get name of the constant
•ReflectionClassConstant::getValue — Gets value
•ReflectionClassConstant::isPrivate — Checks if class constant is private
•ReflectionClassConstant::isProtected — Checks if class constant is protected
•ReflectionClassConstant::isPublic — Checks if class constant is public
•ReflectionClassConstant::__toString — Returns the string representation of the ReflectionClassConstant object



•ReflectionExtension::__clone — Clones
•ReflectionExtension::__construct — Constructs a ReflectionExtension
•ReflectionExtension::export — Export
•ReflectionExtension::getClasses — Gets classes
•ReflectionExtension::getClassNames — 获取类名称
•ReflectionExtension::getConstants — 获取常量
•ReflectionExtension::getDependencies — 获取依赖
•ReflectionExtension::getFunctions — 获取扩展中的函数
•ReflectionExtension::getINIEntries — 获取ini配置
•ReflectionExtension::getName — 获取扩展名称
•ReflectionExtension::getVersion — 获取扩展版本号
•ReflectionExtension::info — 输出扩展信息
•ReflectionExtension::isPersistent — 返回扩展是否持久化载入
•ReflectionExtension::isTemporary — 返回扩展是否是临时载入
•ReflectionExtension::__toString — To string


•ReflectionFunctionAbstract::__clone — 复制函数
•ReflectionFunctionAbstract::getClosureScopeClass — Returns the scope associated to the closure
•ReflectionFunctionAbstract::getClosureThis — 返回本身的匿名函数
•ReflectionFunctionAbstract::getDocComment — 获取注释内容
•ReflectionFunctionAbstract::getEndLine — 获取结束行号
•ReflectionFunctionAbstract::getExtension — 获取扩展信息
•ReflectionFunctionAbstract::getExtensionName — 获取扩展名称
•ReflectionFunctionAbstract::getFileName — 获取文件名称
•ReflectionFunctionAbstract::getName — 获取函数名称
•ReflectionFunctionAbstract::getNamespaceName — 获取命名空间
•ReflectionFunctionAbstract::getNumberOfParameters — 获取参数数目
•ReflectionFunctionAbstract::getNumberOfRequiredParameters — 获取必须输入参数个数
•ReflectionFunctionAbstract::getParameters — 获取参数
•ReflectionFunctionAbstract::getReturnType — Gets the specified return type of a function
•ReflectionFunctionAbstract::getShortName — 获取函数短名称
•ReflectionFunctionAbstract::getStartLine — 获取开始行号
•ReflectionFunctionAbstract::getStaticVariables — 获取静态变量
•ReflectionFunctionAbstract::hasReturnType — Checks if the function has a specified return type
•ReflectionFunctionAbstract::inNamespace — 检查是否处于命名空间
•ReflectionFunctionAbstract::isClosure — 检查是否是匿名函数
•ReflectionFunctionAbstract::isDeprecated — 检查是否已经弃用
•ReflectionFunctionAbstract::isGenerator — 判断函数是否是一个生成器函数
•ReflectionFunctionAbstract::isInternal — 判断函数是否是内置函数
•ReflectionFunctionAbstract::isUserDefined — 检查是否是用户定义
•ReflectionFunctionAbstract::isVariadic — Checks if the function is variadic
•ReflectionFunctionAbstract::returnsReference — 检查是否返回参考信息
•ReflectionFunctionAbstract::__toString — 字符串化


•ReflectionFunction::__construct — Constructs a ReflectionFunction object
•ReflectionFunction::export — Exports function
•ReflectionFunction::getClosure — Returns a dynamically created closure for the function
•ReflectionFunction::invoke — Invokes function
•ReflectionFunction::invokeArgs — Invokes function args
•ReflectionFunction::isDisabled — Checks if function is disabled
•ReflectionFunction::__toString — To string


Table of Contents
•ReflectionMethod::__construct — ReflectionMethod 的构造函数
•ReflectionMethod::export — 输出一个回调方法
•ReflectionMethod::getClosure — 返回一个动态建立的方法调用接口，译者注：可以使用这个返回值直接调用非公开方法。
•ReflectionMethod::getDeclaringClass — 获取被反射的方法所在类的反射实例
•ReflectionMethod::getModifiers — 获取方法的修饰符
•ReflectionMethod::getPrototype — 返回方法原型 (如果存在)
•ReflectionMethod::invoke — Invoke
•ReflectionMethod::invokeArgs — 带参数执行
•ReflectionMethod::isAbstract — 判断方法是否是抽象方法
•ReflectionMethod::isConstructor — 判断方法是否是构造方法
•ReflectionMethod::isDestructor — 判断方法是否是析构方法
•ReflectionMethod::isFinal — 判断方法是否定义 final
•ReflectionMethod::isPrivate — 判断方法是否是私有方法
•ReflectionMethod::isProtected — 判断方法是否是保护方法 (protected)
•ReflectionMethod::isPublic — 判断方法是否是公开方法
•ReflectionMethod::isStatic — 判断方法是否是静态方法
•ReflectionMethod::setAccessible — 设置方法是否访问
•ReflectionMethod::__toString — 返回反射方法对象的字符串表达


•ReflectionParameter::allowsNull — Checks if null is allowed
•ReflectionParameter::canBePassedByValue — Returns whether this parameter can be passed by value
•ReflectionParameter::__clone — Clone
•ReflectionParameter::__construct — Construct
•ReflectionParameter::export — Exports
•ReflectionParameter::getClass — 获得类型提示类。
•ReflectionParameter::getDeclaringClass — Gets declaring class
•ReflectionParameter::getDeclaringFunction — Gets declaring function
•ReflectionParameter::getDefaultValue — Gets default parameter value
•ReflectionParameter::getDefaultValueConstantName — Returns the default value's constant name if default value is constant or null
•ReflectionParameter::getName — Gets parameter name
•ReflectionParameter::getPosition — Gets parameter position
•ReflectionParameter::getType — Gets a parameter's type
•ReflectionParameter::hasType — Checks if parameter has a type
•ReflectionParameter::isArray — Checks if parameter expects an array
•ReflectionParameter::isCallable — Returns whether parameter MUST be callable
•ReflectionParameter::isDefaultValueAvailable — 检查是否有默认值。
•ReflectionParameter::isDefaultValueConstant — Returns whether the default value of this parameter is a constant
•ReflectionParameter::isOptional — Checks if optional
•ReflectionParameter::isPassedByReference — Checks if passed by reference
•ReflectionParameter::isVariadic — Checks if the parameter is variadic
•ReflectionParameter::__toString — To string

ReflectionProperty 类报告了类的属性的相关信息。
Table of Contents
•ReflectionProperty::__clone — Clone
•ReflectionProperty::__construct — Construct a ReflectionProperty object
•ReflectionProperty::export — Export
•ReflectionProperty::getDeclaringClass — Gets declaring class
•ReflectionProperty::getDefaultValue — Returns the default value declared for a property
•ReflectionProperty::getDocComment — Gets the property doc comment
•ReflectionProperty::getModifiers — Gets the property modifiers
•ReflectionProperty::getName — Gets property name
•ReflectionProperty::getType — Gets a property's type
•ReflectionProperty::getValue — Gets value
•ReflectionProperty::hasDefaultValue — Checks if property has a default value declared
•ReflectionProperty::hasType — Checks if property has a type
•ReflectionProperty::isDefault — Checks if property is a default property
•ReflectionProperty::isInitialized — Checks whether a property is initialized
•ReflectionProperty::isPrivate — Checks if property is private
•ReflectionProperty::isProtected — Checks if property is protected
•ReflectionProperty::isPublic — Checks if property is public
•ReflectionProperty::isStatic — Checks if property is static
•ReflectionProperty::setAccessible — Set property accessibility
•ReflectionProperty::setValue — Set property value
•ReflectionProperty::__toString — To string

ReflectionType 类用于获取函数、类方法的参数或者返回值的类型。
•ReflectionType::allowsNull — Checks if null is allowed
•ReflectionType::isBuiltin — Checks if it is a built-in type
•ReflectionType::__toString — To string

 *
 */

//$foo = new ReflectionMethod('Testing', 'foo');
//echo implode(' ', Reflection::getModifierNames($foo->getModifiers())) . "\n";

class Testing{

    private const aaa = 'aval';
    public const bbb = 'bval';

    private $fprivate = 'filed';
    public $fpublic = 'filed';

    private function aaa(string $aaa, int $bbb=111, int ...$other){}
    public static function bbb(){}
    protected function ccc(int $c=3){}
}


$rf = new ReflectionClass('ReflectionClass');

$class['const'] = $rf->getReflectionConstants();
foreach($class['const'] as $key => $item){

    $consts[$key]['name'] = $item->getName();
    $consts[$key]['value'] = $item->getValue();
    $consts[$key]['txt'] = sprintf("const %s = %s;", $consts[$key]['value'], $consts[$key]['name']);
}
$class['const'] = $consts;


$fields = $rf->getProperties();
$class['field'] = $fields;



$class['method'] = $rf->getMethods();
foreach($class['method'] as $key => $item){

    $methods[$key]['acc'] = implode(' ', Reflection::getModifierNames($item->getModifiers()));
    $methods[$key]['name'] = $item->name;

    $params = $item->getParameters();

    $methods[$key]['param'] = '';
    if($params){
        foreach($params as $item1){

            $name = $item1->getName();
            $methods[$key]['param'] .= $item1->getType() . " $name";
            if($item1->isDefaultValueAvailable()) $methods[$key]['param'] .= '='.$item1->getDefaultValue() . ', ';
            else $methods[$key]['param'] .= ', ';
        }
        $methods[$key]['param'] = rtrim($methods[$key]['param'], ', ');
    }
    $methods[$key]['txt'] = $methods[$key]['acc'] . " $item->name(" .  $methods[$key]['param'] . '){}';

}


$class['method'] = $methods;

p($class);






