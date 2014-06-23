<?php
    //для запрет прямого обращения
    define('SMARTY', TRUE);
   
//   include_once 'library/mainFunctions.php'; // Основные функции
    require('library/Smarty/libs/Smarty.class.php');
    session_start();
    // если в сессии нет массива корзины то создаем его
    if(! isset($_SESSION['cart'])){
        $_SESSION['cart'] = array();    
    }
    
    // подключение файла конфигурации
    require_once 'config/config.php';
        
    // внесение изменений в интерпритатор php для подключаемых файлов
    set_include_path(get_include_path()
                  			.PATH_SEPARATOR.CONTROLLER
                  			.PATH_SEPARATOR.MODEL
                                        .PATH_SEPARATOR.SMARTYCLASS
                        		);
    //echo get_include_path();
    //что работал autoload, когда используется smarty
    spl_autoload_register('autoload');  //если используется smarty
    function autoload($class_name) {
    	if(!include_once ($class_name . ".php")) {		
            try {
                throw new Exception($class_name.' - Не правильный файл для подключения');
            }
            catch(Exception $e) {
                echo $e->getMessage();
            }
    	}
     //   echo $class_name . "<br />";
    }
    
    try{
    	$obj = RouteController::get_instance();
        $smarty = new Smarty();
        
        $smarty->setCompileDir('tmp/smarty/templates_c');
        $smarty->setCacheDir('tmp/cache');
        $smarty->setConfigDir('library/configs');
        
    	$obj->route($smarty);
    }
    catch(Exception $e) {
    	return;
    }
    
/**
 * Функция отладки. Останавливает работу програамы выводя значение переменной
 * $value
 * 
 * @param variant $value переменная для вывода ее на страницу 
 */
function d($value = null, $die = 1) {
    echo 'Debug: <br /><pre>';
    print_r($value);
    echo '</pre>';
    
    if($die) die;
}
    
   
 //  loadPage($smarty, $db, $module, $action, $params);