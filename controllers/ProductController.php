<?php

/*  ProductController.php
 * 
 *  Контроллер страницы товара (/product/1)
 */
defined('SMARTY') or exit('Access denied');

class ProductController extends BaseClient   {
        	    
    protected function input($smarty, $param){
        
    	parent::input($smarty, $param);
 
        $itemId = isset($param['id']) ? $param['id'] : null;
        if($itemId) $itemId = intval($itemId);
        if($itemId == null) exit();

             // получить данные продукта
        $rsProduct = $this->ob_m_product->getProductById($itemId);
        
	 $smarty->assign('itemInCart', 0);
        if(in_array($itemId, $_SESSION['cart'])){
            $smarty->assign('itemInCart', 1);
        }

        $smarty->assign('pageTitle', '');
        $smarty->assign('rsProduct', $rsProduct);

        $this->loadTemplate($smarty, 'header');
        $this->loadTemplate($smarty, 'product');
        $this->loadTemplate($smarty, 'footer');
    }

}