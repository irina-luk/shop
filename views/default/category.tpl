{* Страница категории *}

    <h1>{$rsCategory['name']}</h1>
    {if isset($rsProducts) && !empty($rsProducts)}
         {foreach $rsProducts as $item}
              <div class="product">
               <a  href="{$siteUrl}product/id/{$item['id']}/">
                   <img src="{$siteUrl}userfile/{$item['image']}" width="100"  />
               </a><br />
               <a href="{$siteUrl}product/id/{$item['id']}/">{$item['name']}</a>
            </div>
         {/foreach}
    {elseif isset($rsChildCats) && !empty($rsChildCats)} 
       {foreach $rsChildCats as $item name=childCats}
           <h4><a href="{$siteUrl}category/id/{$item['id']}/">{$item['name']}</a></h4>
       {/foreach}
    {else}
       <p>Товара такой категории нет</p>
    {/if}