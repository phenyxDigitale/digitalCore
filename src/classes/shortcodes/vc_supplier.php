<?php

extract(Composer::shortcode_atts(
    array(
        'speed'=>'600',
        'maxslide'=>'6',
        'img_size'=>'',
        'slider_type' => 'bxslider'
    ),$atts
));
$context = Context::getContext(); 
$suppliers = Supplier::getSuppliers(false,$context->language->id, true);


$context->smarty->assign(
    array(
        'suppliers' => $suppliers,
        'speed' => $speed,
        'maxslide' => $maxslide,
        'man_img_size' => $img_size,
        'slider_type' => $slider_type
    )
);
if($slider_type == 'flexslider'){
    if(Configuration::get('vc_load_flex_css') != 'no'){
            $context->controller->addCSS(_EPH_ADMIN_THEME_DIR_. '/composer/flexslider/flexslider.css' );
    }
    if(Configuration::get('vc_load_flex_js') != 'no'){
            $context->controller->addJS(_EPH_ADMIN_THEME_DIR_. '/composer/flexslider/jquery.flexslider-min.js' );
    }    
}else{
    $context->controller->addJqueryPlugin(array('bxslider'));
}

$output = $context->smarty->fetch(Composer::getTPLPath('smartsuppliers.tpl'));

echo $output;