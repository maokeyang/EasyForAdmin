<?php
/*-----------------------------------------------------+
 * 系统错误码打印类
 * 
 +-----------------------------------------------------*/
class Error{

    //错误码的key不可以在其他地方被定义为宏，对应的值使用字符串
    private static $conf = array(
        'SUCCESS'           => '0',
        'FAIL'              => '-1',
        'BAD_ARGS'          => '-2',    // 参数错误
        'BAD_SIGN'          => '-3',    // 签名错误
        'USER_EXISTS'       => '-4',    // 用户已存在
        'USER_NOT_EXISTS'   => '-5',    // 用户不存在
        'TOKEN_ERROR'       => '-6',    // 谷歌登陆缺失token
        'MAIL_ERROR'        => '-7',    // 邮箱已被其他账号绑定
        'MAIL_ALREAD_BIND'  => '-8',    // 此账号绑定过邮箱
        'PWD_ERROR'         => '-9',    // 密码错误
        'MAIL_NOT_BIND'     => '-10',    // 邮箱未绑定
        'PHONE_NOT_BIND'    => '-11',    // 手机未绑定
    );

    /**
     * 取出指定键的数据并退出 调用方法Error::exitError(SUCCESS);
     * @param string $key 键名
     */
    public static function getErrorCode($code){
        return self::$conf[$code];
    }

    /**
     * 取出指定键的数据并退出 调用方法Error::exitError(SUCCESS);
     * @param string $key 键名
     */
    public static function exitError($code){
        exit(self::getErrorCode($code));
    }

    /**
     * 取出指定键的数据并退出 调用方法Error::exitJson(SUCCESS);
     * @param string $key 键名
     */
    public static function exitJson($code, $msg=''){
        $msg = $msg?$msg:$code;
        $rt = array("ret"=>self::$conf[$code], "msg"=>$msg);
        exit_json($rt);
    }

    /**
     * 加载数据
     */
    public static function init(){
        foreach(self::$conf as $k => $v){
            if (!defined($k)) {
                // 将错误码的key定义为宏
                define($k, $k);
            }
        }
    }
}

