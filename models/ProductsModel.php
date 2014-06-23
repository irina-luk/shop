<?php

/* Модель для таблицы продукции (products) */
    defined('SMARTY') or die('Access denied');
       
class ProductsModel {	
    static $instance;    	
    private $ins_driver;    	

    static function get_instance() {
        if(self::$instance instanceof self) {
            return self::$instance;
        }
        return self::$instance = new self;
    }

    private function __construct() {    		
        try {
            $this->ins_driver = ModelDriver::get_instance();
        }
        catch(Exception $e) {
            exit();
        }
    }

    /* Получаем последние добавленные товары
     * 
     * @param integer $limit Лимит товаров
     * @return array Массив товаров 
     */
    public function getLastProducts($limit = null){
        $sql = "SELECT *
                FROM `products` 
                ORDER BY id DESC";
        if($limit){
            $sql .= " LIMIT {$limit}";
        }

        $rs = $this->ins_driver->select($sql); 

       return $this->createSmartyRsArray($rs); 
    }

    /**
     * Получить продукты для категории $itemId
     * 
     * @param integer $itemId ID категории
     * @return array массив продуктов 
     */
    public function getProductsByCat($itemId)    {
       $itemId = intval($itemId);
       $sql = "SELECT * 
                FROM products
                WHERE category_id = '{$itemId}'";

       $rs = $this->ins_driver->select($sql); 

       return $this->createSmartyRsArray($rs);   
    }

    /**
     * Получить данные продукта по ID 
     * 
     * @param integer $itemId ID продукта
     * @return array массив данных продукта 
     */
    public function getProductById($itemId)    {
       $itemId = intval($itemId);
       $sql = "SELECT * 
                FROM products
                WHERE id = '{$itemId}'";

       $rs = $this->ins_driver->select($sql); 
       return $rs->fetch_assoc();   
    }
    
    /* Получить список продуктов из массива идентификаторов (ID`s)
     * 
     * @param array $itemsIds массив идентификаторов продуктов
     * @return array массив данных продуктов 
     */
    public function getProductsFromArray($itemsIds)    {
        $strIds = implode($itemsIds, ', ');
        $sql = "SELECT * 
                FROM products
                WHERE id in ({$strIds})";		
        $rs = $this->ins_driver->select($sql); 

       return $this->createSmartyRsArray($rs); 
    }
    
    /* Преобразорвание результата работы функции выборки в ассоциативный массив
     * 
     * @param recordset $rs набор строк - результат работы SELECT
     * @return array 
     */
    public function createSmartyRsArray($rs)    {
        if(! $rs) return false;

        $smartyRs = array();
        while ($row = $rs->fetch_assoc()) {
            $smartyRs[] = $row;
        }
        return $smartyRs;
    }
}