var url = '/';
/*  Функция добавления товара в корзину
 *  
 *  @param integer itemId ID продукта
 *  @return в случае успеха обновятся данные корзины на странице
 */
function addToCart(itemId){
    console.log("js - addToCart()");
    $.ajax({
        type: 'POST',
        url: url + 'cart/action/addtocart/id/' + itemId + '/',
        dataType: 'json',
        success: function(data){
            if(data['success']){
                $('#cartCntItems').html(data['cntItems']);
                $('#addCart_'+ itemId).hide();
                $('#removeCart_'+ itemId).show();
            }
        }
    });   
}

/* Удаление продукта из корзины
 * 
 * @param integer itemId ID продукта
 * @return в случае успеха обновятся данные корзины на странице
 */
function removeFromCart(itemId){
    console.log("js - removeFromCart("+itemId+")");
    $.ajax({
        type: 'POST',
        async: false,
        url: url + 'cart/action/removefromcart/id/' + itemId + '/',
        dataType: 'json',
        success: function(data){
            if(data['success']){
                $('#cartCntItems').html(data['cntItems']);
                $('#addCart_'+ itemId).show();
                $('#removeCart_'+ itemId).hide();
            }
        }
    });    
}

/* Подсчет стоимости купленного товара
 * 
 * @param integer itemId ID продукта
 */
function conversionPrice(itemId){
    var newCnt = $('#itemCnt_' + itemId).val();
    var itemPrice = $('#itemPrice_' + itemId).attr('value');
    var itemRealPrice = newCnt * itemPrice;

    $('#itemRealPrice_' + itemId).html(itemRealPrice);
}

/**
 * Получение данных с формы
 * 
 */
function getData(obj_form){
    var hData = {};
    $('input, textarea, select',  obj_form).each(function(){
        if(this.name && this.name!=''){
            hData[this.name] = this.value;
            console.log('hData[' + this.name + '] = ' + hData[this.name]);
        }
    });
    return hData;
};

/**
 * Регистрация нового пользователя
 * 
 */
function registerNewUser(){
    var postData = getData('#registerBox');
    
     $.ajax({
        type: 'POST',
        url: url + "user/action/register/",
        data: postData,
        dataType: 'json',
        success: function(data){
            if(data['success']){
                alert('Регистрация прошла успешно');

                //> блок в левом столбце
                $('#registerBox').hide();
                $('#loginBox').hide();
                $('#userRedact').attr('href', url + 'user/action/index/');    
                $('#userLink').html(data['userName']);
                $('#userBox').show();
                                
                $('#btnSaveOrder').show();
                //<
            } else {
                alert(data['message']);
            }
        }
    });   
}

/* Авторизация пользователя */
function login(){
    var email = $('#loginEmail').val();
    var pwd   = $('#loginPwd').val();
    
    var postData = "email="+ email +"&pwd=" +pwd;
    
     $.ajax({
        type: 'POST',
        url: url + "user/action/login/",
        async: false,
        data: postData,
        //data: {email:email, pwd:pwd},
        dataType: 'json',
        success: function(res){
            if(res['success']){
    alert('login() - ' + postData);
                $('#registerBox').hide();
                $('#loginBox').hide();
 
                $('#userLink').html(res['displayName']);
                $('#userBox').show();

                //> заполняем поля на странице заказа
                $('#name').val(res['name']);
                $('#phone').val(res['phone']);
                $('#adress').val(res['adress']);
                //<

                $('#btnSaveOrder').show();

            } else {
                alert('Error: ' + res['message']);
            }
        },
        error: function(res){
            if(res['success'] == 1){
                alert('login() - error - ' + res['message']);
            }
            else {
                alert('login() - error - 0 -' + res['message']);
            }
        }
    }); 
}

/* Обновление данных пользователя */
function updateUserData(){
    console.log("js - updateUserData()");
    var phone  = $('#newPhone').val();
    var adress = $('#newAdress').val();
    var pwd1   = $('#newPwd1').val();
    var pwd2   = $('#newPwd2').val();
    var curPwd = $('#curPwd').val();
    var name   = $('#newName').val();
    
    var postData = {phone: phone, adress: adress, pwd1: pwd1, pwd2: pwd2, curPwd: curPwd, name: name};
				
    $.ajax({
        type: 'POST',
        async: false,
        url: url + "user/action/update/",
        data: postData,
        dataType: 'json',
        success: function(data){
            if(data['success']){
                $('#userLink').html(data['userName']);
                alert(data['message']);
            } else {
                alert(data['message']);
            }
	}   
    });   
}


/* Показывать или прятать форму регистрации */
function showRegisterBox(){
    if( $("#registerBoxHidden").css('display') != 'block' ) {
        $("#registerBoxHidden").show().css('text-decoration', 'none');
        
    } else {
        $("#registerBoxHidden").hide();
    }
}

 /* Сохранение заказа */
function saveOrder(){
    var postData = getData('form');
     $.ajax({
        type: 'POST',
        async: false,
        url: url + "cart/action/saveorder/",
        data: postData,
        dataType: 'json',
        success: function(data){
            if(data['success']){
                alert(data['message']);
                document.location = url;
            } else {
                alert(data['message']);
            }
        }
    });
}

/* Показывать или прятать данные о заказе */
function showProducts(id){
    var objName = "#purchasesForOrderId_" + id;
    if( $(objName).css('display') != 'table-row' ) {
        $(objName).show();
    } else {
        $(objName).hide();
    }
}