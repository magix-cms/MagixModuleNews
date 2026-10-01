{if isset($module_related_news) && $module_related_news|@count > 0}

    {* ==========================================
       1. AFFICHAGE POUR LES PAGES 
       (Exemple : Fond gris, pleine largeur)
       ========================================== *}
    {if $modulenews_current_module == 'pages'}
        <div class="magix-modulenews-widget py-5 bg-body-tertiary mt-5">
            <h3 class="mb-4 fw-bold">{#related_news_title#|default:'À lire également'}</h3>
            {include file="news/loop/news-grid.tpl" data=$module_related_news classType="normal"}
        </div>


        {* ==========================================
           2. AFFICHAGE POUR LES PRODUITS
           (Exemple : Plus compact, séparateur simple)
           ========================================== *}
    {elseif $modulenews_current_module == 'product'}
        <div class="magix-modulenews-widget my-5 pt-4 border-top">
            <h4 class="mb-4 fw-bold">{#related_news_product#|default:'Actualités liées à ce produit'}</h4>
            {* Si vous avez un template grid plus petit, vous pouvez changer classType *}
            {include file="news/loop/news-grid.tpl" data=$module_related_news classType="normal"}
        </div>


        {* ==========================================
           3. AFFICHAGE POUR LES CATÉGORIES
           ========================================== *}
    {elseif $modulenews_current_module == 'category'}
        <div class="magix-modulenews-widget py-5 mt-4">
            <h3 class="mb-4 fw-bold text-primary">{#related_news_category#|default:'Dans la même thématique'}</h3>
            {include file="news/loop/news-grid.tpl" data=$module_related_news classType="normal"}
        </div>


        {* ==========================================
           4. AFFICHAGE PAR DÉFAUT (About, etc.)
           ========================================== *}
    {else}
        <div class="magix-modulenews-widget py-5 mt-5">
            <div class="container">
                <h3 class="mb-4 fw-bold">{#related_news_default#|default:'Pour aller plus loin'}</h3>
                {include file="news/loop/news-grid.tpl" data=$module_related_news classType="normal"}
            </div>
        </div>
    {/if}

{/if}