<?php

extract(Composer::shortcode_atts(array(
            'title' => '',
            'page' => '0',
            'per_page' => '12',
            'orderby' => 'id_product',
            'order' => 'DESC',
            'display_type' => 'grid',
                ), $atts));

$context = Context::getContext();

if (!Configuration::get('NEW_PRODUCTS_NBR'))
    return;

$cache_products = Product::getNewProducts((int) Context::getContext()->language->id, $page, $per_page, false, $orderby, $order);

$context->controller->addCSS(_THEME_CSS_DIR_ . 'product.css');
$context->controller->addCSS(_THEME_CSS_DIR_ . 'product_list.css');
$context->controller->addCSS(_THEME_CSS_DIR_ . 'print.css', 'print');
$context->controller->addJqueryPlugin(array('fancybox', 'idTabs', 'scrollTo', 'serialScroll', 'bxslider'));
$context->controller->addJS(array(
    _THEME_JS_DIR_ . 'tools.js', 
));




$context->smarty->assign(
        array(
            'vc_products' => $cache_products,
            'vc_title' => $title,
            'elementprefix' => 'supplier',
        )
);
if ($display_type == 'sidebar')
    $output = $context->smarty->fetch(Composer::getTPLPath('blockviewed.tpl'));
else
    $output = $context->smarty->fetch(Composer::getTPLPath('blocknewproducts.tpl'));

echo $output;