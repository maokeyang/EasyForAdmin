<?php
/**----------------------------------------------------+
 * 应用核心类
 +-----------------------------------------------------*/

class App{
    /**
     * 框架运行
     */
    public static function run(){
        $actionFile = self::getActionFile();
        include $actionFile;
        $actionName = 'Act_'.CURRENT_ACTION;
        if(!class_exists($actionName, false)){
            throw new Exception("文件中没有定义类: {$actionName}");
        }

        $action = new $actionName();

        if(!($action instanceof Action)){
            throw new Exception("'{$actionName}'不是一个动作类");
        }

        if(!method_exists($action, 'process')) {
            throw new Exception('没有定义动作类的执行入口');
        }

        self::doAuth($action);

        try{
            $action->process();
        }
        catch(Notice $e){
            $e->raiseMsg();
        }
        // 捕获所有的异常，转换后重新抛出
        catch(Exception $e){
            $action->_exception->_message = $e->getMessage();
            $action->_exception->_code = $e->getCode();
            throw $action->_exception;
        }
    }

    /** 
     * 分析URL，返回用户请求中指向的Action文件
     */
    public static function getActionFile(){
        $a = isset($_REQUEST['act'])? $_REQUEST['act'] : 'index';
        define('CURRENT_ACTION', $a);
        if(isset($_REQUEST['mod'])){
            define('CURRENT_MODULE', $_REQUEST['mod']);
            define('APP_ROOT', APP_DIR.$_REQUEST['mod'] );
        }
        else{
            define('APP_ROOT', APP_DIR.'default');
        }

        
        $file = APP_ROOT.'/'.$a.'.act.php';

        // echo $file;die();

        if(!file_exists($file)){
            throw new Exception("无效地址，访问失败");
        }
        return $file;
    }

    /**
     * 生成一个影射到指定模块和动作的URL
     *
     * @param string $action 动作标识字串，空字串表示当前动作
     * @param string $module 模块标识字串，空字串表示当前模块
     * @param array $params 附加到url的参数
     * @param bool $useParamCache 是否使用缓存中的Url参数(被保存在SESSION中)
     * @return string $url URL字串
     */
    public static function url($action=null, $module=null, $params=null, $useParamCache=false) {
        if (is_array($params) && $useParamCache && isset($_SESSION['param_cache']) && is_array($_SESSION['param_cache'])) {
            $params= array_merge($_SESSION['param_cache'], $params);
        }
        else if($useParamCache && isset($_SESSION['param_cache']) && is_array($_SESSION['param_cache'])) {
            $params= stripQuotes($_SESSION['param_cache']);
        }

        $action= $action ? $action : CURRENT_ACTION;
        if(defined('CURRENT_MODULE')){
            $module= $module ? $module : CURRENT_MODULE;
            $url= array("?mod=$module&act=$action");
        }else{
            $url= array("?act=$action");
        }

        if (is_array($params)) {
            foreach ($params as $k => $v) {
                $url[] = urlencode($k).'='.urlencode($v);
            }
        }
        $url = implode($url, '&');
        return preg_match('!^'.$_SERVER['SCRIPT_NAME'].'!', $_SERVER['REQUEST_URI']) ? $_SERVER['SCRIPT_NAME'].$url : $url;
    }

    // 页面重定向
    public static function redirect($url) {
        header('Location:'.$url);
        exit();
    }

    // 执行权限验证
    private static function doAuth($action){
        $e = $action->_exception;
        switch($action->_AuthLevel){
			// 该模块对所有人开放
			case ACT_OPEN:
				break;
			// 该模块需要登录才能访问
			case ACT_NEED_LOGIN:
				self::checkLogin($action);
				break;
			// 该模块需要进行权限验证
			case ACT_NEED_AUTH:
				self::checkLogin($action);
				if(!isset($_SESSION['group_id'])){
					$e->_code = 401;
					$e->raiseMsg('%>_<% 你所在的分组没有权限访问 ');
				}
				// if(!self::hasPerms($_SESSION['group_id'])){
				// 	$e->_code = 401;
				// 	$e->raiseMsg('%>_<% 你所在的分组没有权限访问 ');
				// }
				break;
			default:
				$e->_code = 401;
				$e->raiseMsg('当前模块访问权限设置有误');
			}
    }

    /**
     * 验证登录
     */
    private static function checkLogin($action){
        if(!isset($_SESSION['user_name'])){
            if($action->_redirect){
                self::redirect($action->_redirect);
            }else{
                $e = $action->_exception;
                $e->_code = 401;
                if(isset($e->links)){
                    $e->array = array('登录'=> App::url('login'));
                }                
				$ref = $_SERVER['REQUEST_URI'];
				if(strpos($ref, 'mod=') !==false){
                    $ref_arr = array('redirect'=>$ref);   
                }      
                //$e->links = array('返回登录' => App::url('login', 'default', $ref_arr));
                //$e->raiseMsg('登录失效，需要重新登录后才能继续');    
				self::redirect(App::url('login', 'default', $ref_arr));           
            }
        }
    }

    /**
     * 权限认证
     * @param $group_id
     */
    private static function hasPerms($group_id) {
		$url = '?mod='.CURRENT_MODULE.'&act='.CURRENT_ACTION;
		if(isset($_SESSION['rights'])){
			if(in_array($url,$_SESSION['rights'])){
				return true;
			}else{
				return false;
			}
		}
		
        return false;
    }
    
	/**
	 * 获取特定模块的权限验证
	 * @param string $group_id 用户组
	 * @param string $mod 模块
	 * @param string $act action
	 * @return bool
	 */
    public static function getPerms($group_id, $mod, $act) {
        //是超级管理员，直接通过验证
        if('SUPER' == $group_id) return true;
        $authCfg = include APP_DIR.'/auth.cfg.php';
        if(isset($mod)){
            if(isset($authCfg[$mod]) && 'ALL' == $authCfg[$mod]){
                return true;
            }
            else if(isset($authCfg[$mod][$act])){
                return 'ALL' == $authCfg[$mod][$act]
                    ? true
                    : in_array($group_id, $authCfg[$mod][$act]);
            }
            else if(isset($authCfg[$mod])){
                return in_array($group_id, $authCfg[$mod]);
            }
        } else{
            if(isset($authCfg[$act])){
                return 'ALL' == $authCfg[$act]
                    ? true
                    : in_array($group_id, $authCfg[$act]);
            }
        }
        return false;
    }
    public static function isDeleteAllowed() {
        if(!isset($_SESSION['group_id'])) return false;
       switch ($_SESSION['group_id']) {
    		case 'SUPER':
    		case 'MGR':
			case 'OPR':
            case 'PLR':
                return true;
            case 'GM':
            case 'SRV':
            case 'CLT':
            case 'ART':
            default:
                return false;
    	}     
    } 
    /**
     * 获取后台系统导航菜单列表     
     * @return array 解析后的配置数值
     * @todo 权限其其它菜单管理
     * @access private
     */
    public static function getMenu() {   
        $menu = array();
		$menu_cnf = require CONFIG_DIR.'menu.php';
		if(isset($_SESSION['menu'])){
			$menu = $_SESSION['menu'];
			return $menu;
		}else{
			$group_info = Admin_User_Right::getAdminGroupById($_SESSION['group_id']);
			if(!empty($group_info)){
				$group_rights = json_decode($group_info['rights'],true);
				foreach($menu_cnf as $mkey => $mval){
					foreach($mval['sub'] as $key => $val){
						if($group_info['rights'] == 'all'){//管理员组
							if($val['is_show'] === false){
								unset($menu_cnf[$mkey]['sub'][$key]);
							}
						}else{//非管理员组
							if($val['is_show'] === false || !in_array($mkey.'_'.$key,$group_rights)){
								unset($menu_cnf[$mkey]['sub'][$key]);
							}
						}
					}
				}

				foreach($menu_cnf as $mkey => $mval){
					if(is_array($mval['sub']) && count($mval['sub']) < 1){
						unset($menu_cnf[$mkey]);
					}
				}
				
				$_SESSION['menu'] = $menu = $menu_cnf;
			}else{
				throw new Exception("无效的菜单配置");
			}
		}
        
    	return $menu;
    }
    /**
     * 生成平台跳转登陆key 
     * 这里特殊说明一下：为了摆脱请求接口的权限限制，我们以一个权限比较高的账号和接口去通信
     * ver系统只有我们内部才可以访问，目前用的是运营账号edward
     * @param int $ts 时间
     * @return string
     */
	public static function getTicket($ts) {
		$key = Config::getInstance()->get('admin_key');//登陆验证KEY
		/*$user_id = 10;
		$uname = 'edward';*/
		$user_id = $_SESSION['user_id'];
		$uname = $_SESSION['user_name'];
		if(!$user_id || !$uname)return '';
		$pwd = Db::getInstance()->getOne("select passwd from `ver_user` where id = ".$user_id);		
		return md5($key.$uname.$pwd.$ts);
	}
	/**
     * 生成平台跳转登陆key 
     * 这里特殊说明一下：为了摆脱请求接口的权限限制，我们以一个权限比较高的账号和接口去通信ward
     * 这里写死了如果用超级管理员身份登陆，则使用admin用户跳转，前提是保证全服都有admin用户，并且保证与ver里的admin密码一致
     * 注意与AppCommon类一致
     * @param int $ts 时间
     * @return string
     */
	public static function getTicketByPlatfrom($ts,$platform=null) {
		$db=Db::getInstance();
		if(!$platform){
			$key = Config::getInstance()->get('admin_key');//登陆验证KEY
		}else{
			$key = $db->getOne("select admin_key from `ver_template_srv_list` where platform = '$platform'");
		}
		/*$user_id = 10;
		$uname = 'edward';*/
		//$user_id = $_SESSION['user_id'];
		if('SUPER'==$_SESSION['group_id']){
			$uname = 'admin';
		}else{
			$uname = $_SESSION['user_name'];
		}
		if(!$uname)return '';
		$pwd = $db->getOne("select passwd from `ver_user` where user_name = '{$uname}'");		
		return md5($key.$uname.$pwd.$ts);
	}

	/**
	 * 扩展类似JS的alert函数，响应后直接退出php执行脚本
	 * @param $msg 提示信息
	 * @param $act 默认动作返回上一页，其它：href转到链接，close关闭当前窗口
	 * @param $href 网址
	 * @return null
	 */
	public static function alert($msg = '操作失败 :-(', $act = 'href', $href = '') {
		$js = '';
		switch ($act) {
			case 'href' :
				if (! $href)
					$href = $_SERVER ['HTTP_REFERER'];
				$js = "location.href='$href';";
				break;
			case 'close' :
				$js = "window.open('','_parent','');window.close();";
				break;
			default :
				$js = "history.go(-1);";
		}
		//避免因字符编码问题
		echo '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			  <body><script type="text/javascript">
			  alert("' . $msg . '");' . $js . '
			  </script></body></html>';
		exit ();
	}
}
