<?php
defined('SMARTY') or exit('Access denied');
/*  cartController.php
 * 
 *  Контроллер работы с корзиной (/cart/)
 */
class CartController extends BaseClient   {

    protected function input($smarty, $param){
        
    	parent::input($smarty, $param);
        
        /* Формирование страницы корзины
         * @link /cart/
         */
        if (!isset($param['action'])) {
            
            $itemsIds = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();

            if (!empty($itemsIds))
                $rsProducts = $this->ob_m_product->getProductsFromArray($itemsIds);
            else 
                $rsProducts = null;

            $smarty->assign('pageTitle', 'Корзина');
            $smarty->assign('rsProducts', $rsProducts);

            $this->loadTemplate($smarty, 'header');
            $this->loadTemplate($smarty, 'cart');
            $this->loadTemplate($smarty, 'footer');
        }
        
        /* Добавление продукта в корзину */
        if (isset($param['action']) && $param['action'] == 'addtocart') {
            $itemId = isset($param['id']) ? intval($param['id']) : null; 
            if(! $itemId) return false;

            $resData = array();

            // если значение не найдено, то добавляем
            if(isset($_SESSION['cart']) && array_search($itemId, $_SESSION['cart']) === false){
                $_SESSION['cart'][] = $itemId;
                $resData['cntItems'] = count($_SESSION['cart']);
                $resData['success'] = 1;
            } else {
                $resData['success'] = 0;
            }
            echo json_encode($resData);
        }
        
        // Удаление продукта из корзины
        if (isset($param['action']) && $param['action'] == 'removefromcart') {            
            $itemId = isset($param['id']) ? intval($param['id']) : null; 
            if(! $itemId) exit();

            $resData = array();
            $key = array_search($itemId, $_SESSION['cart']);
            if($key !== false){
                unset($_SESSION['cart'][$key]);
                $resData['success'] = 1;
                 $resData['cntItems'] = count($_SESSION['cart']);
            } else {
                $resData['success'] = 0;
            }
            echo json_encode($resData);
        }
 
         /* Формирование страницы заказа */
         if (isset($param['action']) && $param['action'] == 'order'){
        	// получаем массив идентификаторов (ID) продуктов корзины
            $itemsIds = isset($_SESSION['cart']) ? $_SESSION['cart'] : null;
        	// если корзина пуста то редиректим в корзину
            if(! $itemsIds){
               header("Location: " . SITE_URL);
               exit();
        	}
        	
        	// получаем из массива $_POST количество покупаемых товаров
            $itemsCnt = array();
            foreach($itemsIds as $item){
        		// формируем ключ для массива POST
                $postVar = 'itemCnt_' . $item;
        		// создаем элемент массива количества покупаемого товара
        		// ключ массива - ID товара, значение массива - количество товара
        		// $itemsCnt[1] = 3;  товар с ID == 1 покупают 3 штуки
                $itemsCnt[$item] = isset($_POST[$postVar]) ? $_POST[$postVar] : null;
            }
        	
        	// получаем список продуктов по массиву корзины
            $rsProducts = $this->ob_m_product->getProductsFromArray($itemsIds);
        	
        	// добавляем каждому продукту дополнительное поле 
        	// "realPrice = количество продуктов * на цену продукта"
        	// "cnt" = количество покупаемого товара
        	
        	// &$item - для того чтобы при изменении переменной $item 
        	// менялся и элемент массива $rsProducts
        	$i = 0;
            foreach($rsProducts as &$item){
                $item['cnt'] = isset($itemsCnt[$item['id']]) ? $itemsCnt[$item['id']] : 0;
                if($item['cnt']){
                    $item['realPrice'] = $item['cnt'] * $item['price'];
                } else {
        			// если вдруг получилось так что товар в корзине есть, а количество == нулю,
        			// то удаляем этот товар
                    unset($rsProducts[$i]);
                }
                $i++;
            }
        	
        	if(! $rsProducts){
        		echo "Корзина пуста";
        		return;
        	}
        	
        	// полученный массив покупаемых товаров помещаем в сессионную переменную
            $_SESSION['saleCart'] = $rsProducts;
        	            
        	// hideLoginBox переменная - флаг для того чтобы спрятать блоки логина и регистрации 
        	// в боковой панели
        	if(! isset($_SESSION['user'])){
        		$smarty->assign('hideLoginBox', 1);
        	}
        	
            $smarty->assign('pageTitle', 'Заказ');
            $smarty->assign('rsProducts', $rsProducts);
             
            $this->loadTemplate($smarty, 'header');
            $this->loadTemplate($smarty, 'order');
            $this->loadTemplate($smarty, 'footer');
        	
         }
         
          /*  AJAX функция сохраниение заказа
          * 
          * @param array $_SESSION['saleCart'] массив покупаемых продуктов
          * @return json информация о результате выполнения 
          */
         if (isset($param['action']) && $param['action'] == 'saveorder') {
        	 // получаем массив покупаемых товаров
        	$cart = isset($_SESSION['saleCart']) ? $_SESSION['saleCart'] : null;
        	// если корзина пуста, то формируем ответ с ошибкой, отдаем его в формате 
        	// json и выходим из функции 
        	if(! $cart){
        		$resData['success'] = 0; 
                $resData['message'] = 'Нет товаров для заказа'; 
        		echo json_encode($resData);
        		return;
        	}
        	
        	$name	= $_POST['name'];
        	$phone	= $_POST['phone'];
        	$adress = $_POST['adress'];
        	
        	// создаем новый заказ и получаем его ID
        	$orderId = makeNewOrder($name, $phone, $adress);
        	
        	// если заказ не создан, то выдаем ошибку и завершаем функцию
        	if(! $orderId){
        		$resData['success'] = 0; 
                $resData['message'] = 'Ошибка создания заказа'; 
        		echo json_encode($resData);
        		return;
        	} 
        	
        	// сохраняем товары для созданного заказа
        	$res = setPurchaseForOrder($orderId, $cart);
        	
        	// если успешно, то формируем ответ, удаляем переменные корзины
        	if($res){
        		$resData['success'] = 1; 
                $resData['message'] = 'Заказ сохранен';
        		unset($_SESSION['saleCart']);
        		unset($_SESSION['cart']);
        	} else {
                $resData['success'] = 0; 
                $resData['message'] = 'Ошибка внесеня данных для заказа № ' . $orderId; 
            }
        	
        	echo json_encode($resData);
         }
        }
    }
          