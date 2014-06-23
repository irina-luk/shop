<html>
    <head>
        <title>{$pageTitle}</title>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
            
        {if isset($styles)}
            {foreach $styles as $style}
                <link rel="stylesheet" type="text/css" href="{$style}" />
            {/foreach}
        {/if}

        {if isset($scripts)}
            {foreach $scripts as $script}
                <script type="text/javascript" src="{$script}"></script>
            {/foreach}
        {/if}
            
    </head>    
    <body>
	<header>
            <h1>My shop - интернет магазин</h1>
            <hr />
        </header>
        {include file='leftcolumn.tpl'}
        {include file='rightcolumn.tpl'}  

        <article>