<?php
defined('SMARTY') or exit('Access denied');

class RouteController extends BaseController {	
    static $_instance;

    static function get_instance() {		
        if(self::$_instance instanceof self) {
                return self::$_instance;
        }		
        return self::$_instance = new self;
    }
    private function __construct() {
        // Назначаем модуль и действие по умолчанию.
        $this->controller = 'IndexController';
        // Массив параметров из URI запроса.
        $this->params = array();

        $zapros = $_SERVER['REQUEST_URI'];
        $path = substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'],'index.php')); //корень сайта

        // Если запрошен любой URI, отличный от корня сайта.
        if ($_SERVER['REQUEST_URI'] != $path) {
            try {
                $request_url = substr($zapros,strlen(SITE_URL)); //обрезаем корень сайта
                $url = explode('/',rtrim($request_url,'/'));

                $this->controller = ucfirst($url[0]).'Controller';
                $count = count($url);

                if(!empty($url[1])) {				
                    $key = array();
                    $value = array();
                    for($i = 1;$i < $count; $i++) {					
                        if($i%2 != 0) {
                            $key[] = $url[$i];
                        }
                        else {
                            $value[] = $url[$i];
                        }
                    }				
                    if(!$this->params = array_combine($key,$value)) {
                        throw new ContrException("Не правильный адрес",$zapros);
                    }
                }
            } catch (Exception $e) {
                $this->controller = '404';
            }
        }	
    }
}
//echo $module . '<br />' . $action; exit();