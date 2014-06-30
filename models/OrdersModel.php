<?php
/* Модель для таблицы заказов (orders) */
defined('SMARTY') or die('Access denied');
       
class OrdersModel {	
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
     * Создание заказа (без привязки товара)
     * 
     * @param string $name
     * @param string $phone
     * @param string $adress
     * @return integer ID созданного заказа 
     */
    public function makeNewOrder($name, $phone, $adress) {
            //> инициализация переменных
        $userId	 = $_SESSION['user']['id'];
        $comment = "id пользователя: {$userId}<br />
                    Имя: {$name}<br />
                    Тел: {$phone}<br />
                    Адрес: {$adress}";

        $dateCreated = date('Y.m.d H:i:s');
        $userIp	= $_SERVER['REMOTE_ADDR'];
            //<
        
        //обновление данных пользователя
        $sql = "UPDATE `users`
                SET `name`= '{$name}',`phone`='{$phone}',`adress`='{$adress}' 
                WHERE `id`='{$userId}'";
        $rs = $this->ins_driver->update($sql);

            // формирование запроса к БД
        $sql = "INSERT INTO orders (`user_id`, `date_created`,  
                                             `comment`, `user_ip`)  
                VALUES ('{$userId}', '{$dateCreated}', 
                                            '{$comment}', '{$userIp}')";
        // получить id созданного заказа
        $rs = $this->ins_driver->insert($sql);

        if($rs){
            return $rs;
        }
        return false;
    }

    /**
     * Получить данные заказа текущего пользователя
     * 
     * @return array массив заказов с привязкой к продуктам 
     */
    public function getCurUserOrders()    {
        $userId = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;
        $rs = $this->getOrdersWithProductsByUser($userId);

        return $rs;
    }
    
    /**
     * Получить список заказов с привязкой к продуктам для пользователя $userId
     * 
     * @param integer $userId ID пользователя
     * @return array массив заказов с привязкой к продуктам 
     */
    public function getOrdersWithProductsByUser($userId)    {	
        $userId = intval($userId);
        $sql = "SELECT * FROM orders
                WHERE `user_id` = '{$userId}' ORDER BY id DESC";

        $rs = $this->ins_driver->select($sql); 

        $smartyRs = array();
        while ($row = $rs->fetch_assoc()) {
            $rsChildren = $this->getPurchaseForOrder($row['id']);

            if($rsChildren){
                $row['children'] = $rsChildren;
                $smartyRs[] = $row;
            } 
        }
       return $smartyRs;	
    }

    public function getPurchaseForOrder($orderId)    {
        $sql = "SELECT `pe`.*, `ps`.`name` 
                FROM purchase as `pe`
                JOIN products as `ps` ON `pe`.product_id = `ps`.id
                WHERE `pe`.order_id = {$orderId}";

        $rs = $this->ins_driver->select($sql); 
        return $this->createSmartyRsArray($rs); 
    }
    
    /**
     * Преобразорвание результата работы функции выборки в ассоциативный массив
     * 
     * @param recordset $rs набор строк - результат работы SELECT
     * @return array 
     */
    private function createSmartyRsArray($rs)    {
        if(! $rs) return false;

        $smartyRs = array();
        while ($row = $rs->fetch_assoc()) {
            $smartyRs[] = $row;
        }
        return $smartyRs;
    }
}


