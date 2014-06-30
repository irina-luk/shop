<?php

/* Контроллер функций пользователя */
defined('SMARTY') or exit('Access denied');

class UserController  extends BaseClient   {
    	    
    protected function input($smarty, $param){        
    	parent::input($smarty, $param);
        
        $this->ob_m_user = UserModel::get_instance();
        $this->ob_m_order = OrdersModel::get_instance();
        
        /* AJAX регистрация пользователя.
         * Инициализация сессионнной переменной ($_SESSION['user'])
         * 
         * @return json массив данных нового пользователя
         */
        if (isset($param['action']) && $param['action'] == 'register') {
            
            $email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : null;
            $pwd1 = isset($_REQUEST['pwd1']) ? trim($_REQUEST['pwd1']) : null;
            $pwd2 = isset($_REQUEST['pwd2']) ? trim($_REQUEST['pwd2']) : null;

            $phone  = isset($_REQUEST['phone'])  ? trim($_REQUEST['phone'])  : null;
            $adress = isset($_REQUEST['adress']) ? trim($_REQUEST['adress']) : null;
            $name   = isset($_REQUEST['name'])   ? trim($_REQUEST['name'])   : null;


            $resData = null;
            $resData = $this->ob_m_user->checkRegisterParams($email, $pwd1, $pwd2); 

            if(! $resData && $this->ob_m_user->checkUserEmail($email)){
                $resData['success'] = false; 
                $resData['message'] = "Пользователь с таким email('{$email}') уже зарегистрирован"; 
            }
            //d($resData);
            if(! $resData ){
                $pwdMD5 = md5($pwd1);
                $userData = $this->ob_m_user->registerNewUser($email, $pwdMD5, $name, $phone, $adress);
                if($userData['success']){
                    $resData['message'] = 'Пользователь успешно зарегистрирован'; 
                    $resData['success'] = 1; 

                    $resData['userName'] = $name ? $name : $email;
                    $resData['userEmail'] = $email;

                    $_SESSION['user'] = $userData;
                    $_SESSION['user']['displayName'] = $resData['userName'];
                } else {
                    $resData['success'] = 0; 
                    $resData['message'] = 'Ошибка регистрации'; 
                }  
            }
            echo json_encode($resData);
        }
        
        /* Разлогинивание пользователя */
        if (isset($param['action']) && $param['action'] == 'logout'){
            if(isset($_SESSION['user'])){
                unset($_SESSION['user']);
                unset($_SESSION['cart']);
            }
            $this->redirect();
        }
        
        /*  AJAX авторизация пользователя
         * 
         *  @return json массив данных подльзователя
         */
        if (isset($param['action']) && $param['action'] == 'login'){
            $email = isset($_REQUEST['email']) ? $this->clear_str($_REQUEST['email']) : null;
            $pwd = isset($_REQUEST['pwd']) ? $this->clear_str($_REQUEST['pwd']) : null;

            $userData = $this->ob_m_user->loginUser($email, $pwd);

            if($userData['success']){
                $userData = $userData[0];

                $_SESSION['user'] = $userData;
                $_SESSION['user']['displayName'] = $userData['name'] ? $userData['name'] : $userData['email'];

                $resData = $_SESSION['user'];
                $resData['success'] = 1;
                $resData['message'] = 'верный логин или пароль'; 

                $resData['userName'] = $userData['name'] ? $userData['name'] : $userData['email'];
                $resData['userEmail'] = $email;
            } else {
                $resData['success'] = 0; 
                $resData['message'] = 'Неверный логин или пароль'; 
            }
            d($resData);
            echo json_encode($resData);
        }
        
        /* Формирование главной страницы пользователя 
         * 
         * @link /user/
         * @param object $smarty шаблонизатор  
         */ 
        if (isset($param['action']) && $param['action'] == 'index'){
            // если пользователь не залогинен, то редирект на главную стрницу
            if(! isset($_SESSION['user'])){
                header("Location: " . SITE_URL);
                exit();
            }
            // получаем список заказов пользователя
            $rsUserOrders = $this->ob_m_order->getCurUserOrders();
              //  d($rsUserOrders);
            $smarty->assign('pageTitle', 'Страница пользователя');
            $smarty->assign('rsUserOrders', $rsUserOrders);

            $this->loadTemplate($smarty, 'header');
            $this->loadTemplate($smarty, 'user');
            $this->loadTemplate($smarty, 'footer');
        }
        
        /* Обновление данных пользователя
         * 
         * @return json результаты выполнения функции
         */
        if (isset($param['action']) && $param['action'] == 'update'){
                //> если пользователь не залогинен, то выходим
            if(! isset($_SESSION['user'])){
                $this->redirect();
            }
                //> инициализация переменных
            $resData = array();
            $phone  = isset($_REQUEST['phone'])  ? $this->clear_str($_REQUEST['phone'])	: null;
            $adress = isset($_REQUEST['adress']) ? $this->clear_str($_REQUEST['adress']): null;
            $name   = isset($_REQUEST['name'])   ? $this->clear_str($_REQUEST['name'])	: null;
            $pwd1   = isset($_REQUEST['pwd1'])	 ? $_REQUEST['pwd1']	: null;
            $pwd2   = isset($_REQUEST['pwd2'])	 ? $_REQUEST['pwd2']	: null;
            $curPwd = isset($_REQUEST['curPwd']) ? $_REQUEST['curPwd']	: null;
                //<

            // проверка правильности пароля (введенный и тот под которым залогинились)
            $curPwdMD5 = md5($curPwd);
            if( ! $curPwd || ($_SESSION['user']['pwd'] != $curPwdMD5) ){
                $resData['success'] = 0;
                $resData['message'] = 'Текущий пароль не верный';
                echo json_encode($resData);
                return false;
            }

            // обновление данных пользователя 
            $res = $this->ob_m_user->updateUserData($name, $phone, $adress, $pwd1, $pwd2, $curPwdMD5);
            if($res){
                $resData['success'] = 1;
                $resData['message'] = 'Данные сохранены';
                $resData['userName'] = $name;

                $_SESSION['user']['name']   = $name;
                $_SESSION['user']['phone']  = $phone;
                $_SESSION['user']['adress'] = $adress;

                $newPwd = $_SESSION['user']['pwd'];
                if( $pwd1 && ($pwd1 == $pwd2) ){
                    $newPwd = md5(trim($pwd1));
                }
                $_SESSION['user']['pwd'] = $newPwd;

                $_SESSION['user']['displayName'] = $name ? $name : $_SESSION['user']['email'];
            } else {
                $resData['success'] = 0;
                $resData['message'] = 'Ошибка сохранения данных';
            }
            echo json_encode($resData);
        }
    }
}
