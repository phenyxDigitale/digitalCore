<?php

extract(Composer::shortcode_atts(array(
            'title' => '',
            'page' => '1',
            'per_page' => '12',
            'random' => false,
            'orderby' => 'position',
            'order' => 'DESC',
            'display_type' => 'grid',
                ), $atts));

$context = Context::getContext();
$output = '';

$random = $random == 'yes' ? true : false;

$category = new Category($context->company->getCategory(), (int) $context->language->id);
$cache_products = $category->getProducts((int) $context->language->id, $page, $per_page, $orderby, $order, false, true, (bool) $random, $per_page);

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
            'elementprefix' => 'featured',
        )
);
if ($display_type == 'sidebar')
    $output = $context->smarty->fetch(Composer::getTPLPath('blockviewed.tpl'));
else
    $output = $context->smarty->fetch(Composer::getTPLPath('blocknewproducts.tpl'));

echo $output;