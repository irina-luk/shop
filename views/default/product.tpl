{* страница продукта *}
<h3>{$rsProduct['name']}</h3>
    
<img src="{$siteUrl}userfile/{$rsProduct['image']}">  
<br />
Стоимость: {$rsProduct['price']}
 
<a id="addCart_{$rsProduct['id']}" {if  $itemInCart}class="hideme"{/if} href="#" 
    onClick="addToCart({$rsProduct['id']}); return false;" alt="Добавить в корзину">Добавить в корзину</a>     
<a id="removeCart_{$rsProduct['id']}" {if ! $itemInCart}class="hideme"{/if} href="#" 
    onClick="removeFromCart({$rsProduct['id']}); return false;" alt="Удалить из корзины">Удалить из корзины</a>
<p> Описание <br />{$rsProduct['description']}</p>