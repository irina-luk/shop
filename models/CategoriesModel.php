<?php

/* Модель для таблицы категорий (categories) */
    defined('SMARTY') or die('Access denied');
       
class CategoriesModel {	
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

    public function getAllMainCatsWithChildren(){
        $sql = 'SELECT * FROM categories WHERE parent_id = 0';

        $rs = $this->ins_driver->select($sql); 

        $smartyRs = array();
        while ($row = $rs->fetch_assoc()) {
            $rsChildren = $this->getChildrenForCat($row['id']);

            if($rsChildren){
                $row['children'] = $rsChildren;
            }
            $smartyRs[] = $row;
        }		
        return $smartyRs;
    }

     /* Получить дочернии категории для категории $catId
     * 
     * @param integer $catId ID категории
     * @return array массив дочерних категорий 
     */

     public function getChildrenForCat($catId){
       $sql = "SELECT * 
                FROM categories
                WHERE parent_id = '{$catId}'";

       $rs = $this->ins_driver->select($sql);   

       return $this->createSmartyRsArray($rs); 
    }
    /* Получить данные категории по id
     * 
     * @param integer $catId ID категории
     * @return array массив - строка категории 
     */
    public function getCatById($catId) { 
       $sql = "SELECT * 
                FROM categories
                WHERE id = '{$catId}'";

       $rs = $this->ins_driver->select($sql); 

       return $rs->fetch_assoc();    
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