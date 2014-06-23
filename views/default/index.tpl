{* шаблон главной страницы *} 	

   {foreach $rsProducts as $item}
   	<div class="product">
            <a  href="{$siteUrl}product/id/{$item['id']}/">
   		<img src="{$siteUrl}userfile/{$item['image']}" width="100" />
            </a><br />
            <a href="{$siteUrl}product/id/{$item['id']}/" class="prod_name">{$item['name']}</a>
   	</div>
   {/foreach}