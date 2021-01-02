
#数组定义：
$array = 'Zero','One','Two','Three'
Write-Output Zero One Two Three
[string[]] $array = @('Zero','One','Two','Three')

[Object[]]::new(4)
[int[]]::new(4) #元素值默认为0
@(1) * 4

#对象数组
$data = @(
    [pscustomobject]@{FirstName='Kevin';LastName='Marquette'}
    [pscustomobject]@{FirstName='John'; LastName='Doe'}
)

#数组属性
$data.count #元素个数
$data.GetUpperBound(0) #最大索引号

#访问元素
$data[0,-1] #首尾两个元素
$data[3..1]  #索引从 3 到 1 即 $data[3,2,1]
$data[0..-1] #索引从 0 到 -1 即 $data[0，-1]


#遍历数组
for($index = 0; $index -lt $data.count; $index++){
    "Item: {0}" -f $data[$index] # 格式化输出
}

foreach($node in $data ){ $node  }

$data.foreach({$PSItem}) # $PSItem 管道中的当前对象
$data.foreach { $_ }
$data | ForEach-Object { $PSItem } 

$data.where({$_.FirstName -eq 'Kevin'})
$data | Select-String One


#对象数组遍历
$data | ForEach-Object {$_.LastName}

$data | Select-Object -ExpandProperty LastName

$data.LastName #全部对象的 LastName 属性值组成的数组

$data | Where-Object { $_.FirstName -eq 'Kevin' }

$data | Where FirstName -eq Kevin

#预期的是用新对象替换掉 $data 数组中的所有对象元素，然而却行不通。
#因为 $person 原本指向 $data 数组中的某个对象
#但是当 $person 重新赋值后，指向了新的对象，而与原对象毫不相干了
#正确的做法是通过索引去修改：$data[0] = Object 
foreach($person in $data){
    $person = [pscustomobject]@{ FirstName='Kevin'}
}


#数组转字符串
$data -join '-' #数组元素通过 '-' 串连在一起
-join $data #数组元素串连在一起
"Data is $($data -join ',')"
$data -replace 'Fin','To'
$data -eq 'green' #返回数组中等于 'green' 的元素，类似于查找元素
$data -match 'SQL' #返回数组中所有匹配的元素，组成的数组


$sb = [System.Text.StringBuilder]::new()
[void]$sb.Append( 'Was it a car ' )
[void]$sb.AppendLine( 'or a cat I saw?' )
$sb.ToString()


#泛型列表
$mylist = [System.Collections.Generic.List[string]]::new()
$mylist = [System.Collections.Generic.List[int]]::new()
$mylist = [System.Collections.Generic.List[int]]@(1,2,3) #将数组强制转换为列表
$list = [List[PSObject]]::new() #不知道对象的类型时就用 PSObject 类型


#使用全名空间,可能缩短语法
#using namespace System.Collections.Generic
$myList = [List[int]]@(1,2,3)


$myList.Add(10)
$myList[10]

[list[System.Management.Automation.PSDriveInfo]]$drives = Get-PSDrive
$drives.remove($drives[2]) #删除的对象



$newData = switch($data){
    'One' { '111' }
    'Three' {'333' }
    Default{ 'Unknow' }
}


#比较运算符：-eq 等于 -ne 不等于 -lt 小于 -gt 大于 -le 小于等于 -ge 大于等于
#相似比较：-like | -notlike ?|* 
#正则比较：-match | -notmatch  
#集合包含：-contains | -notcontains 
#在集合中：-in | notin
#注意：以上这些运算符，默认都是以不区分大小写的方式比较，可以加上加上字母 c（区分） 或 i（不区分）作为前缀表示是否区分大小写，如：-cgt|-igt, -clike|-ilike， -cnotlike|-inotlike
#类型判断：-is | -isnot 

#逻辑运算符 ! -and -or  -not -xor


#集合运算
1..6 -gt 3      #返回 4,5,6
1,2,3,4 -eq 3   #返回 3
1,2,3 -ne 4     #返回 1,2,3


#布尔值是： $true $false
#数组不等于 $null, $null 值这么比较： $null -eq $value 

#在子表达式中执行变量赋值时，会传递到管道。
($second = 2)

$x = $null
$x ?? 100   #空合并运算符       $x = 100
$x ??= 100  #空合并赋值运算符   $x = 100 



$Endpoint = "tom"
$snowSqlParam = @(
    '--username', $Endpoint
    '--option', 'friendly=false'
    if($true){ '--option', 'log_level=DEBUG'}
    if($false){'--filename', 'test.txt'  }else{ '--query', 'QueryString' }
)



$data | Get-Member #看到的是成员对象详情
,$data | Get-Member #相当于组成一个新数组，让它展开
Write-Output -NoEnumerate $data | Get-Member #若要防止数组展开，可以使用 Write-Object -NoEnumerate 


#如果将输出分配给变量，则仍可获取一个数组，如果要防止数组展开，可以使用  Write-Output -NoEnumerate $array  或 return ,$array 来解决

#哈希表

$ageList = @{
    Kevin = 36
    Alex  = 9
}
$ageList = @{}
$ageList.add( 'Alex', 9 )
$ageList['Alex']

#提供一个键数组来获取多个值
$environments[@('QA','DEV')]
$environments[('QA','DEV')]
$environments['QA','DEV']
$ageList.values
$ageList.keys 
$ageList.GetEnumerator #获取一个元素

$ageList.values | Measure-Object -Average


$ageList.keys | ForEach-Object{
    $message = '{0} is {1} years old!' -f $_, $ageList[$_]
}


$ageList.GetEnumerator() | ForEach-Object{
    $message = '{0} is {1} years old!' -f $_.key, $_.value
}

$environments = @{
    Prod = 'SrvProd05'
    QA   = 'SrvQA02'
    Dev  = 'SrvDev12'
}

#哈希表在枚举时无法修改，要修改先克隆键
$environments.Keys.Clone() | ForEach-Object {
    $environments[$_] = 'SrvDev03'
}


$person = @{
    name = 'Kevin'
    age  = 36
}

$person.city = 'Austin'
$person.state = 'TX'

$person.ContainsKey('age') #是否包含键
$person.clear()

#按顺序存入
$person = [ordered]@{
    name = 'Kevin'
    age  = 36
}

#用表达式计算属性
Get-PSDrive | Where Used | Select-Object -Property Name, Root, @{ name='Total'; expression={($_.used + $_.free) / 1GB}}

$data = @( @{name='a'; age=5}, @{name='c';age=52}, @{name='d';age=566})
$data | Sort-Object -Property @{ expression = {$_.name} }

$Params = @{ ClassName = 'Win32_Bios'; ComputerName = $ComputerName }
if($Credential){ $Params.Credential = $Credential }
#使用 @ 符号放在 Params 对象前面会自动展开成一个字符串,以便让命令行调用
Get-CIMInstance @Params


$person = @{ name = 'Kevin';  age  = 36;  location = @{ city  = 'Austin'; state = 'TX' } }

foreach($name in $people.keys){

    $person = $people[$name]
    '{0}, age {1}, is in {2}' -f $name, $person.age, $person.city
}

$people | ConvertTo-Json


$person = [pscustomobject]@{name = 'Kevin'; age  = 36 }

$person = @{ name = 'Kevin';  age  = 36}
[pscustomobject]$person

保存到 CSV
$person | ForEach-Object{ [pscustomobject]$_ } | Export-CSV -Path $path


哈希表以 json 格式保存到文件，然后再次将其读入
$people | ConvertTo-JSON | Set-Content -Path $path
$people = Get-Content -Path $path -Raw | ConvertFrom-JSON



将 JSON 转换为哈希表
[Reflection.Assembly]::LoadWithPartialName("System.Web.Script.Serialization")
$JSSerializer = [System.Web.Script.Serialization.JavaScriptSerializer]::new()
$JSSerializer.Deserialize($json, 'Hashtable')

导入以 PowerShell 语法包含哈希表文件
$content = Get-Content -Path $Path -Raw -ErrorAction Stop
$scriptBlock = [scriptblock]::Create( $content )
$scriptBlock.CheckRestrictedLanguage( $allowedCommands, $allowedVariables, $true )
$hashtable = ( & $scriptBlock )


$person = @{
    'full name' = 'Kevin Marquette'
    '#' = 3978
}

$person.'full name'
$key = 'full name'
$person.$key

正则表达式匹配的结果数组 $Matches
$message = 'My SSN is 123-45-6789.'
$message -match 'My SSN is (.+)\.'
$Matches[1]


示例中，?<Name> 是给表达式命名， 此值将被放入 $Matches.Name 属性
$message = 'Name is Kevin'
if($message -match 'Name is (?<Name>.+)'){
    $Matches.Name
}

数据集中的列转换为哈希表
Import-CSV $Path | Group-Object -AsHashtable -Property email, name

每个变量只是对对象的引用。

$orig = @{name='orig'}
$orig.Clone()


Clone 只是复制基本属性，如果属性指向了其他引用类型（如其他哈希表），则是共用的。
$orig = @{
    person=@{
        name='orig'
    }
}
$copy = $orig.Clone()
$copy.person.name = 'copy'
'Copy: [{0}]' -f $copy.person.name
'Orig: [{0}]' -f $orig.person.name

创建深层副本
function Get-DeepClone {
    [CmdletBinding()]
    param(
        $InputObject
    )
    process
    {
        if($InputObject -is [hashtable]) {
            $clone = @{}
            foreach($key in $InputObject.keys)
            {
                $clone[$key] = Get-DeepClone $InputObject[$key]
            }
            return $clone
        } else {
            return $InputObject
        }
    }
}




setx 创建或修改环境变量,可以从注册表或文件作为来源, 设置仅在后续命令行生效。支持的注册表单元只有 HKEY_CURRENT_USER 和 HKEY_LOCAL_MACHINE，对 REG_MULTI_SZ 类型只提取第一项的值。
setx [/s <Computer 计算机名或IP地址> [/u [<Domain 帐户凭据,默认系统权限>\]<UserName 用户名> [/p [<Password 用户密码>]]]] ...
1=> <Variable 环境变量名> <Value 环境变量值> [/m 系统级环境变量]
2=> [<Variable 环境变量名>] /k <Path 从注册表项中查找内容作为环境变量值> [/m 系统级环境变量]
3=> /f <FileName 文件> { [<Variable>] { /a 绝对坐标  <X>,<Y> | /r 相对坐标 <X>,<Y> <String> } [/m 系统级环境变量] | /x 显示文件坐标  } [/d <Delimiters 分隔符>]

setx 环境变量名 环境变量值 /m
setx /s 计算机名 /u maindom\用户名 /p 用户密码  环境变量名 环境变量值 

环境变量设置为从注册表项中找到的值
setx 环境变量名 /k HKEY_LOCAL_MACHINE\Software\Microsoft\Windows NT\CurrentVersion\CurrentBuildNumber /m


set 显示、设置或删除环境变量
set [<Variable 环境变量>=[<String 环境变量值>]]
set /p <Variable 环境变量>=[<PromptString 提示消息>]
set /a <Variable 环境变量>=<Expression 计算表达式>

全部变量
$global: variable = <value>

脚本变量
$script: variable = <value>

局部变量
$variable = <value>


脚本引用
. "./Write-Log.ps1"
Write-Log -Message 'It's a test message'

位置参数
$args[0]

function Say-Hello
{
    Param
    (
        [parameter(Mandatory=$true, ValueFromPipeline=$true)]
        $name
    )
    process
    {
        Write-Host("Hello {0}!" -f $name)
    }
}


function Say-Hello
{
    Param
    (
        $name,
        $count=3
    )

    for(;$count -gt 0;$count--)
    {
        Write-Host("Hello {0}!" -f $name)
    }   
}

function getddd-version {
    $PSVersionTable.PSVersion
}


查看函数列表
Get-ChildItem -Path Function:\Get-*Version

查看函数列表，再删除
Get-ChildItem -Path Function:\Get-*Version | Remove-Item

卸载模块
Remove-Module -Name <ModuleName>

查看通用参数
Get-Command -Name Test-MrParameter -Syntax
(Get-Command -Name Test-MrParameter).Parameters.Keys




function location {

    param(
        [switch]$dev,
        [string]$file
    )

    if($dev.ToBool()){
        $file = switch ($file){
            'test'      { 'E:\develop\test' }
            'bat'       { 'E:\develop\windows\bat'}
            'demo'      { 'D:\demo'}
            'desktop'   { "C:\Users\zbseoag\Desktop" }
            ''          { 'E:\develop' }
            default     { 'E:\develop\' + $file }
        }
    }

    if(!(Test-Path $file)){ 
        Write-Error "Dir is not exist: '$file'"
    }else{
        Set-Location -Path $file
    }

}


#添加 CmdletBinding 以将函数转换为高级函数
#添加 CmdletBinding 会自动添加通用参数。 CmdletBinding 需要一个 param 块，但 param 块可以为空。
#SupportsShouldProcess 会添加 WhatIf 和 Confirm 参数 
function Test-MrCmdletBinding {

    [CmdletBinding(SupportsShouldProcess)]  
    param (
        [Parameter(Mandatory)]
        [string[]]$ComputerName


        [ValidateNotNullOrEmpty()]
        [string[]]$ComputerName2 = $env:COMPUTERNAME
    )

    
      foreach ($Computer in $ComputerName) {

        Write-Verbose -Message "Attempting to perform some action on $Computer"
        Write-Output $Computer
     }


}

#Verbose 参数调用函数时，会显示详细输出
Test-MrVerboseOutput -ComputerName Server01, Server02 -Verbose


#Get-Command -Name Test-MrCmdletBinding -Syntax # Test-MrCmdletBinding [[-ComputerName] <Object>] [<CommonParameters>]

(Get-Command -Name Test-MrCmdletBinding).Parameters.Keys


子表达式运算符 $( )
数组子表达式运算符 @( )

调用运算符 & $cmd

调用带文件名带空格的脚本
& ".\script name with spaces.ps1"


一元数组表达式 (, $array)
function Test-Return {
    $array = 1,2,3
    return (, $array)
    #return Write-Output -NoEnumerate $array
}
Test-Return | Measure-Object


静态成员运算符 ::
调用.NET Framework类的静态属性和方法。若要查找对象的静态属性和方法，请使用Get-Membercmdlet 的Static参数。






