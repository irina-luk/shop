<?php
defined('SMARTY') or exit('Access denied');

abstract class BaseController {	
    protected $controller;	
    protected $params;	
	protected $styles,$styles_admin;	
	protected $scripts,$scripts_admin;	
    
    public function route($smarty) {      //метод переадресации на нужный контролер
        if(class_exists($this->controller)) {			
            $ref = new ReflectionClass($this->controller);

            if($ref->hasMethod('request')) {				
                if($ref->isInstantiable()) {
                    $class = $ref->newInstance();
                    $method = $ref->getMethod('request');
                    $method->invoke($class, $smarty, $this->params);
                }
            }			
        }
        else {
            throw new Exception('Такой страницы не существует - Контроллер - '.$this->controller);
        }
    }	
    
	public function init() {		
		global $conf;		
		if(isset($conf['styles'])) {
			foreach($conf['styles'] as $style) {
				$this->styles[] = trim($style,'/');
			}
		}
		if(isset($conf['scripts'])) {
			foreach($conf['scripts'] as $script) {
				$this->scripts[] = trim($script,'/');
			}
		}		
		if(isset($conf['styles_admin'])) {
		}		
		if(isset($conf['scripts_admin'])) {
		}
	}	
        
    protected function input() {	}
    
    /* Формирование запрашиваемой страницы    */
    public function request($smarty, $params = array()){	
	$this->init();        
        $this->input($smarty, $params);
        return;  
    }
    
    public function clear_str($var) {		
        if(is_array($var)) {
            $row = array();
            foreach($var as $key=>$item) {
                $row[$key] = strip_tags(trim($item));
            }			
            return $row;
        }
        return strip_tags(trim($var));
    }
    
    /* Загрузка шаблона
     * 
     * @param object $smarty объект шаблонизатора
     * @param string $templateName название файла шаблона 
     */
    public function loadTemplate($smarty, $templateName) {
        $smarty->display($templateName . TemplatePostfix);
    }
    // Функция отладки. 
    public function print_arr($arr){
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
    }
   /* ===Редирект=== */
   public function redirect(){
       $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : SITE_URL;
       header("Location: $redirect");
       exit();
   }
}
