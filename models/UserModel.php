<?php

/* Модель для таблицы пользователей (users) */
class UserModel {
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
    /* Проверка параметров для регистрации пользователя
     * 
     * @param string $email email 
     * @param string $pwd1 пароль
     * @param string $pwd2 повтор пароля
     * @return array результат
     */
    public function checkRegisterParams($email, $pwd1, $pwd2)    {
        $res = null;

        if(! $email){
            $res['success'] = false; 
            $res['message'] = 'Введите email'; 
        }
        if(! $pwd1){
            $res['success'] = false; 
            $res['message'] = 'Введите пароль'; 
        }
        if(! $pwd2){
            $res['success'] = false; 
            $res['message'] = 'Введите повтор пароля'; 
        }
        if($pwd1 != $pwd2){
            $res['success'] = false; 
            $res['message'] = 'Пароли не совпадают'; 
        }
        return $res;
    }
    
    /* Проверка почты (есть ли email адрес в БД)
     * 
     * @param string $email
     * @return array  массив - строка из таблицы users, либо пустой массив
     */
    public function checkUserEmail($email)    {
         $sql = "SELECT id FROM users WHERE email = '{$email}'";

         $rs = $this->ins_driver->select($sql);
         //print_r($rs); exit();
         $rs = $this->createSmartyRsArray($rs);

         return $rs;
    }

    /* Регистрация нового пользователя
     * 
     * @param string $email почта
     * @param string $pwdMD5 пароль зашифрованный в MD5
     * @param string $name имя пользователя
     * @param string $phone телефон
     * @param string $adress адрес пользователя
     * @return array массив данных нового пользователя 
     */
    public function registerNewUser($email, $pwdMD5, $name, $phone, $adress)    {
       $email   = htmlspecialchars(trim($email));
       $name    = htmlspecialchars(trim($name));
       $phone   = htmlspecialchars(trim($phone));
       $adress  = htmlspecialchars(trim($adress));

       $sql = "INSERT INTO users (`email`, `pwd`, `name`, `phone`, `adress`)  
               VALUES ('{$email}', '{$pwdMD5}', '{$name}', '{$phone}', '{$adress}')";

       $rs = $this->ins_driver->insert($sql); 

       $res = array();
       if($rs){
            $res['success'] = 1;
       } else {
            $res['success'] = 0;
       }
       return $res;   
    }
    
    /* Авторизация пользователя
     * 
     * @param string $email почта (логин)
     * @param string $pwd пароль
     * @return array массив данных пользователя
     */
    public function loginUser($email, $pwd)    {
        $pwd     = md5($pwd);

        $sql = "SELECT * FROM users  
                WHERE (`email` = '{$email}' and `pwd` = '{$pwd}')
                LIMIT 1";

        $rs = $this->ins_driver->select($sql); 

        $rs = $this->createSmartyRsArray($rs);
        if(isset($rs[0])){
            $rs['success'] = 1;
        } else {
            $rs['success'] = 0;
        }
        return $rs;
    }

    /**
     * Изменение данных пользователя
     * 
     * @param string $name имя пользователя
     * @param string $phone телефон
     * @param string $adress адрес
     * @param string $pwd1 новый пароль
     * @param string $pwd2 повтор нового пароля
     * @param string $curPwd текущий пароль
     * @return boolean TRUE в случае успеха  
     */
    public function updateUserData($name, $phone, $adress, $pwd1, $pwd2, $curPwd)    {
       $email   = $_SESSION['user']['email'];

       $newPwd = null;
       if( $pwd1 && ($pwd1 == $pwd2) ){
            $newPwd = md5($pwd1);
       }
       $sql = "UPDATE users 
                SET ";
       if($newPwd){
            $sql .= "`pwd` = '{$newPwd}', ";
       }
      $sql .= " `name` = '{$name}', 
                `phone` = '{$phone}', 
                `adress` = '{$adress}'
               WHERE 
                `email` = '{$email}' AND `pwd` = '{$curPwd}'
               LIMIT 1";

       $rs = $this->ins_driver->update($sql); 
        return $rs;
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
