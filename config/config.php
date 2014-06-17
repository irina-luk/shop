<?php
   /* Файл настроек */
defined('SMARTY') or exit('Access denied');
   
   //> Константы для обращения к контроллерам
   define ('CONTROLLER', 'controllers/');
   //define ('PathPostfix', 'Controller.php');
   //<
    define('MODEL', 'models/');    // модель
    define('SMARTYCLASS', 'library/Smarty/libs/');
    define('SITE_URL', '/shop/');    // домен
    define('TEMPLATE', SITE_URL . 'themes/default/');    // активный шаблон
    
    define('HOST', 'localhost');    // сервер БД    
    define('USER', 'root');    // пользователь    
    define('PASS', 'toor');    // пароль    
    define('DB', 'myshop');    // БД

   //> используемый шаблон 
   $template = 'default';
   
   // пути к файлам шаблонов (*.tpl)
   define ('TemplatePrefix', "views/{$template}/");
   define ('TemplatePostfix', '.tpl');
   
$conf = array(
    'styles' => array(
                        'css/main.css',
          //              'css/validationEngine.jquery.css',
          //              'css/template.css',
                      ),
    'scripts' => array(
                        'js/jquery-1.11.0.min.js',
         //               'js/jquery.messageWindow.js',
         //               'js/jquery.validationEngine.js',
                        'js/main.js',
                      ),
    'styles_admin' => array(),
    'scripts_admin' => array(),						
);