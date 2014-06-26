<?php
defined('SMARTY') or exit('Access denied');

abstract class BaseClient extends BaseController {
    protected $ob_m_category;	
    protected $ob_m_product;	
    protected $ob_m_user;
    protected $ob_m_order;
    protected $ob_m_purchase;
    protected $content;
    protected $style;	
    protected $script;	

    protected function input($smarty, $param) {	
        foreach($this->styles as $style) {
            $this->style[] = TEMPLATE . $style;
        }		
        foreach($this->scripts as $script) {
            $this->script[] = TEMPLATE . $script;
        }		
        $smarty->assign('styles', $this->style);
        $smarty->assign('scripts', $this->script);
        
        $this->ob_m_category = CategoriesModel::get_instance();		//модель 
        $this->ob_m_product = ProductsModel::get_instance();

             // получить все категории 
        $rsCategories = $this->ob_m_category->getAllMainCatsWithChildren();
        $smarty->assign('rsCategories', $rsCategories);
        // инициализируем переменную шаблонизатора количества элементов в корзине
        $smarty->assign('cartCntItems', count($_SESSION['cart'])); 
        // если в сессии есть данные об авторизированном пользователе, то передаем их в шаблон
        if(isset($_SESSION['user'])){
            $smarty->assign('arUser', $_SESSION['user']); 
        }

        // пути к файлам шаблонов в вебпространстве
        $smarty->assign('siteUrl', SITE_URL);    // домен
        
        $smarty->setTemplateDir(TemplatePrefix);
        	
    }	
}