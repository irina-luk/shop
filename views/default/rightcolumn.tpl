{*правій столбец*}
<div class="rightbar">
    {if isset($arUser)}
        <div id="userBox">
            Добро пожаловать,<br /><span id="userLink">{$arUser['displayName']}</span><br />
            <a href="{$siteUrl}user/action/index/" id="userRedact">Вход в личный кабинет</a><br /><br />
            <a href="{$siteUrl}user/action/logout/" >Выход</a>
        </div>        
    {else}
        <div id="userBox" class="hideme">
            Добро пожаловать,<br /><span id="userLink"></span><br />
            <a href="{$siteUrl}user/action/index/" id="userRedact">Вход в личный кабинет</a><br /><br />
            <a href="{$siteUrl}user/action/logout/" >Выход</a>
        </div>
        
    	{if ! isset($hideLoginBox)}        
            <div id="loginBox">
                <div class="menuCaption">Авторизация</div>
                <input type="text" id="loginEmail" name="loginEmail"  placeholder="email"/><br />
                <input type="password" id="loginPwd" name="loginPwd" placeholder="пароль"/><br />
                <input type="button" onClick="login();" value="Войти"/>
            </div>    
            <div id="registerBox">
                <div class="menuCaption showHidden" onclick="showRegisterBox();">Регистрация</div>
                <div id="registerBoxHidden">
                    <input type="text" id="email" name="email" placeholder="email"/><br />
                    <input type="password" id="pwd1" name="pwd1"  placeholder="пароль"/><br />
                    <input type="password" id="pwd2" name="pwd2" placeholder="повторить пароль"/><br />
                    <input type="button" name="reg_small" onclick="registerNewUser();" value="Зарегистрироваться"/>
                </div>
            </div>
    	{/if}
    {/if}

    <div class="menuCaption">Корзина</div>
    <a href="{$siteUrl}cart/" title="Перейти в корзину">В корзине</a>
    <span id="cartCntItems">
        {if $cartCntItems > 0}{$cartCntItems}{else}пусто{/if}
    </span>

</div>