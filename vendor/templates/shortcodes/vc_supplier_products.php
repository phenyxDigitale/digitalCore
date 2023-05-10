<?php

$vc_main = ephenyx_manager();

extract(SmartShortCode::shortcode_atts(array(
          'id_supplier' => '1',
          'title' => '',
          'page' => '1',
          'per_page' => '12',
          'orderby' => 'position',
          'order' => 'DESC',
          'display_type' => 'grid',
        ), $atts));
        
    	$context = Context::getContext();         
    	$out_put = '';
        

        $cache_products = Supplier::getProducts($id_supplier, $context->language->id, $page, $per_page, $orderby, $order, false);
            
    	  if(is_array($cache_products) && !empty($cache_products)){
                if($display_type == 'grid'){
                    $context->smarty->assign(
                    array(
                         'new_products' => $cache_products,                                                   
                         'sds_title' => $title
	                     )
	                );
                        $template_file_name = Composer::getTPLPath('blocknewproducts.tpl');
                        $out_put = $context->smarty->fetch($template_file_name);
                }else{
                  
                    $sds_title = "";
                    $sds_title .= $vc_main->l('Products');
                    $context->smarty->assign(
                        array(
                            'productsViewedObj' => $cache_products,
                            'sds_title' => $title
                        )
                    );
                    $template_file_name = Composer::getTPLPath('blockviewed.tpl');
		 			$out_put = $context->smarty->fetch($template_file_name);
                }
        }else{
            $out_put = $vc_main->l('No products have been found.');
        }
          echo $out_put;