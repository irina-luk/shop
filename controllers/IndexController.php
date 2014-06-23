<?php

/* Контроллер главной страницы */
defined('SMARTY') or exit('Access denied');

class IndexController extends BaseClient   {
    	    
    protected function input($smarty, $param){
        
    	parent::input($smarty, $param);
    		
        $rsProducts = $this->ob_m_product->getLastProducts(9);

        $smarty->assign('pageTitle', 'Главная страница сайта');
        $smarty->assign('rsProducts', $rsProducts);
                
        $this->loadTemplate($smarty, 'header');
        $this->loadTemplate($smarty, 'index');
        $this->loadTemplate($smarty, 'footer');        
    }
}

