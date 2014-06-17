<?php   
    defined('SMARTY') or exit('Access denied');
    
    class ModelDriver {	
    	static $instance;	
    	public $ins_db;
    	
    	static function get_instance() {
            if(self::$instance instanceof self) {
                return self::$instance;
            }
            return self::$instance = new self;
    	}
    	
    	public function __construct() {
            $this->ins_db = new mysqli(HOST, USER, PASS, DB);
    		
            if($this->ins_db->connect_error) {
                echo "Не удалось подключиться к DB: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            }    		
            $this->ins_db->query("SET NAMES 'UTF8'");
    	}
      
        public function select($query) {
            $res = $this->ins_db->query($query) or die("Ошибка выборки из бд");
            return $res;
        }
        public function delete($query) {
            $res = $this->ins_db->query($query) or die("Ошибка удаления из бд");
            return true;
        }
        public function insert($query) {
            $res = $this->ins_db->query($query) or die("Ошибка вставки в бд");
		
            return $this->ins_db->insert_id; //возвращает id вставленной записи
        }
        public function update($query) {
            $res = $this->ins_db->query($query) or die("Ошибка обновления записи в бд");
            return true;
        }
        public function __destruct() {
            $this->ins_db->close();
        }
    }