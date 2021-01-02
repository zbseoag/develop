#!/usr/bin/env node

'use strict';

// CommonJS规范
// 每个js文件都是一个模块，它们内部各自使用的变量名和函数名都互不冲突，例如，hello.js和main.js都申明了全局变量var s = 'xxx'，但互不影响。
// 一个模块想要对外暴露变量（函数也是变量），可以用 module.exports = variable; 引用其他模块暴露的变量，用var ref = require('module_name');

process.cwd();
process.chdir('/resource');

//@hello.js
function greet(name) {
    console.log(s + 'hello, ' + name );
}
module.exports = greet;


//引入模块,如果只写模块名,依次在内置模块->全局模块->当前模块下查找
var greet = require('./hello');
greet('Michael'); // Hello, Michael


//读文件(异步)
fs.readFile('sample.txt', 'utf-8', function (err, data) {
    if (err) {
        console.log(err);
    } else {
        console.log(data);
    }
});

//读文件(同步)
try {
    fs.readFileSync('sample.txt', 'utf-8');
} catch (err) {

}

// Buffer -> String
var text = data.toString('utf-8');
// String -> Buffer
var buf = Buffer.from(text, 'utf-8');

//写文件
var data = 'Hello, Node.js';
fs.writeFile('output.txt', data, function (err) {
    if (err) {
        console.log(err);
    } else {
        console.log('ok.');
    }
});


//文件详情fs.stat/statSync
fs.stat('sample.txt', function (err, stat) {
    if (err) {
        console.log(err);
    } else {
        // 是否是文件
        stat.isFile();
        // 是否是目录
        stat.isDirectory();
        // 文件大小
        stat.size;
        // 创建时间, Date对象
        stat.birthtime;
        // 修改时间, Date对象
        stat.mtime;
        
    }
});


//流是一种抽象的数据结构
//只需要响应流的事件, data 事件表示流的数据已经可以读取了
// 打开一个流
var rs = fs.createReadStream('sample.txt', 'utf-8');

rs.on('data', function (chunk) {
    console.log('DATA:')
    console.log(chunk);
});

rs.on('end', function () {
    console.log('END');
});

rs.on('error', function (err) {
    console.log('ERROR: ' + err);
});

//以流的形式写入文件
var ws1 = fs.createWriteStream('output1.txt', 'utf-8');
ws1.write('使用Stream写入文本数据...\n');
ws1.write('END.');
ws1.end();

var ws2 = fs.createWriteStream('output2.txt');
ws2.write(new Buffer('使用Stream写入二进制数据...\n', 'utf-8'));
ws2.write(new Buffer('END.', 'utf-8'));
ws2.end();


//管道
var rs = fs.createReadStream('sample.txt');
var ws = fs.createWriteStream('copied.txt');
rs.pipe(ws);
//默认情况下，当Readable流的数据读取完毕，end事件触发后，将自动关闭Writable流。如果不希望自动关闭Writable流，需要传入参数：
readable.pipe(writable, { end: false });



// 创建http server，并传入回调函数:
var server = http.createServer(function (request, response) {
    // 回调函数接收request和response对象,
    // 获得HTTP请求的method和url:
    console.log(request.method + ': ' + request.url);
    // 将HTTP响应200写入response, 同时设置Content-Type: text/html:
    response.writeHead(200, {'Content-Type': 'text/html'});
    // 将HTTP响应的HTML内容写入response:
    response.end('<h1>Hello world!</h1>');
});

// 让服务器监听8080端口:
server.listen(8080);
console.log('Server is running at http://127.0.0.1:8080/');

// url解析
console.log(url.parse('http://user:pass@host.com:8080/path/to/file?query=string#hash'));


/****** 创建一个静态资源服务器 ********/
// node file_server.js /www/static/

// 从命令行参数获取root目录，默认是当前目录:
var root = path.resolve(process.argv[2] || '.');

console.log('Static root dir: ' + root);

// 创建服务器:
var server = http.createServer(function (request, response) {
    // 获得URL的path，类似 '/css/bootstrap.css':
    var pathname = url.parse(request.url).pathname;
    // 获得对应的本地文件路径，类似 '/srv/www/css/bootstrap.css':
    var filepath = path.join(root, pathname);
    // 获取文件状态:
    fs.stat(filepath, function (err, stats) {
        if (!err && stats.isFile()) {
            // 没有出错并且文件存在:
            console.log('200 ' + request.url);
            // 发送200响应:
            response.writeHead(200);
            // 将文件流导向response:
            fs.createReadStream(filepath).pipe(response);
        } else {
            // 出错了或者文件不存在
            console.log(request.url + ' 404');
            // 发送404响应
    
            response.writeHead(404);
            response.write('Error: ' + request.url + ' is not found');
            response.end("\n");
        }
    });
});

server.listen(8080);
console.log('Server is running at http://127.0.0.1:8080/');


// 加密和哈希算法
const crypto = require('crypto');
const hash = crypto.createHash('md5');
// 可任意多次调用update():
hash.update('Hello, world!');
hash.update('Hello, nodejs!');
console.log(hash.digest('hex')); 

const hmac = crypto.createHmac('sha256', '这是一个密钥');
hmac.update('Hello, world!');
hmac.update('Hello, nodejs!');
console.log(hmac.digest('hex'));


//AES是一种常用的对称加密算法，加解密都用同一个密钥

function aesEncrypt(data, key) {
    const cipher = crypto.createCipher('aes192', key);
    var crypted = cipher.update(data, 'utf8', 'hex');
    crypted += cipher.final('hex');
    return crypted;
}

function aesDecrypt(encrypted, key) {
    const decipher = crypto.createDecipher('aes192', key);
    var decrypted = decipher.update(encrypted, 'hex', 'utf8');
    decrypted += decipher.final('utf8');
    return decrypted;
}

var data = 'Hello, this is a secret message!';
var key = 'Password!';
var encrypted = aesEncrypt(data, key);
var decrypted = aesDecrypt(encrypted, key);

console.log('Plain text: ' + data);
console.log('Encrypted text: ' + encrypted);
console.log('Decrypted text: ' + decrypted);

/*************** Diffie-Hellman ***************** */
// xiaoming's keys:
var ming = crypto.createDiffieHellman(512);
var ming_keys = ming.generateKeys();

var prime = ming.getPrime();
var generator = ming.getGenerator();

console.log('Prime: ' + prime.toString('hex'));
console.log('Generator: ' + generator.toString('hex'));

// xiaohong's keys:
var hong = crypto.createDiffieHellman(prime, generator);
var hong_keys = hong.generateKeys();

// exchange and generate secret:
var ming_secret = ming.computeSecret(hong_keys);
var hong_secret = hong.computeSecret(ming_keys);

// print secret:
console.log('Secret of Xiao Ming: ' + ming_secret.toString('hex'));
console.log('Secret of Xiao Hong: ' + hong_secret.toString('hex'));



/********************RSA算法*************************************/

// 生成一个RSA密钥对：
// openssl genrsa -aes256 -out rsa-key.pem 2048
// 根据提示输入密码，这个密码是用来加密RSA密钥的，加密方式指定为AES256，生成的RSA的密钥长度是2048位。执行成功后，我们获得了加密的rsa-key.pem文件
// 第二步，通过上面的rsa-key.pem加密文件，我们可以导出原始的私钥，命令如下：
// openssl rsa -in rsa-key.pem -outform PEM -out rsa-prv.pem
// 输入第一步的密码，我们获得了解密后的私钥。类似的，我们用下面的命令导出原始的公钥：
// openssl rsa -in rsa-key.pem -outform PEM -pubout -out rsa-pub.pem
// 这样，我们就准备好了原始私钥文件rsa-prv.pem和原始公钥文件rsa-pub.pem，编码格式均为PEM

const
    fs = require('fs'),
    crypto = require('crypto');

// 从文件加载key:
function loadKey(file) {
    // key实际上就是PEM编码的字符串:
    return fs.readFileSync(file, 'utf8');
}

let
    prvKey = loadKey('./rsa-prv.pem'),
    pubKey = loadKey('./rsa-pub.pem'),
    message = 'Hello, world!';

// 使用私钥加密:
let enc_by_prv = crypto.privateEncrypt(prvKey, Buffer.from(message, 'utf8'));
console.log('encrypted by private key: ' + enc_by_prv.toString('hex'));


let dec_by_pub = crypto.publicDecrypt(pubKey, enc_by_prv);
console.log('decrypted by public key: ' + dec_by_pub.toString('utf8'));


////////////接下来我们使用公钥加密，私钥解密：

// 使用公钥加密:
let enc_by_pub = crypto.publicEncrypt(pubKey, Buffer.from(message, 'utf8'));
console.log('encrypted by public key: ' + enc_by_pub.toString('hex'));

// 使用私钥解密:
let dec_by_prv = crypto.privateDecrypt(prvKey, enc_by_pub);
console.log('decrypted by private key: ' + dec_by_prv.toString('utf8'));



