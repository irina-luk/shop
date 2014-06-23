<?php

/* categoryController.php
 * 
 *  Контроллер страницы категории (/category/1)
 */
defined('SMARTY') or exit('Access denied');

class CategoryController extends BaseClient   {
        	    
    protected function input($smarty, $param){
        
    	parent::input($smarty, $param);
        
        $catId = isset($param['id']) ? $param['id'] : null;
        if($catId) $catId = intval($catId);
        if(!$catId) exit();

        $rsProducts  = null;
        $rsChildCats = null;
        $rsCategory = $this->ob_m_category->getCatById($catId);

    // если главная категория то показываем дочернии категории, иначе показывает товар
        if($rsCategory['parent_id'] == 0){
             $rsChildCats = $this->ob_m_category->getChildrenForCat($catId);
        } else {
             $rsProducts = $this->ob_m_product->getProductsByCat($catId);
        }

        $smarty->assign('pageTitle', 'Товары категории ' . $rsCategory['name']);

        $smarty->assign('rsCategory', $rsCategory);
        $smarty->assign('rsProducts', $rsProducts);
        $smarty->assign('rsChildCats', $rsChildCats);     

        $this->loadTemplate($smarty, 'header');
        $this->loadTemplate($smarty, 'category');
        $this->loadTemplate($smarty, 'footer');
    }
}
