{* левый столбец *}

<nav>
    <a href="{$siteUrl}">Главная</a><br /><br />
    <div id="leftMenu">
        <div class="menuCaption">Меню:</div>
            {foreach $rsCategories as $item}
            <a href="{$siteUrl}category/id/{$item['id']}/">{$item['name']}</a><br />
            {if isset($item['children'])}
                {foreach $item['children'] as $itemChild}
                    -- <a href="{$siteUrl}category/id/{$itemChild['id']}/">{$itemChild['name']}</a><br />
                {/foreach}
            {/if}
        {/foreach}
    </div>  

</nav>