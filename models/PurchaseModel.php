<?php
/* Модель для таблицы продукции (purchase) */
defined('SMARTY') or die('Access denied');
       
class PurchaseModel {	
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
    
    /**
     *  Внесение в БД данных продуктов с привязкой к заказу
     *
     * @param integer $orderId ID заказа
     * @param array $cart массив корзины 
     * @return boolean TRUE в случае успешного добавления в БД
     */
    public function setPurchaseForOrder($orderId, $cart) {
        $sql = "INSERT INTO purchase (order_id, product_id, price, amount) 
                VALUES ";

        $values = array();
        // формируем массив строк для запроса для каждого товара
        foreach ($cart as $item) {
            $values[] = "('{$orderId}', '{$item['id']}', '{$item['price']}', '{$item['cnt']}')";
        }
        // преобразовываем массив в строку
        $sql .= implode($values, ', ');
        $rs = $this->ins_driver->insert($sql); 

        return $rs; 
    }
}


function getPurchaseForOrder($orderId)
{
    $sql = "SELECT `pe`.*, `ps`.`name` 
            FROM purchase as `pe`
            JOIN products as `ps` ON `pe`.product_id = `ps`.id
            WHERE `pe`.order_id = {$orderId}";
   
    $rs = mysql_query($sql); 
    return createSmartyRsArray($rs); 
}