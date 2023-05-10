<?php
$output = $title = $tab_id = '';
extract(Composer::shortcode_atts($this->predefined_atts, $atts));

Context::getContext()->controller->addJS(_EPH_JS_DIR_ . 'composer/jquery-ui-tabs-rotate/jquery-ui-tabs-rotate.js' );


$css_class =  'wpb_tab ui-tabs-panel wpb_ui-tabs-hide vc_clearfix';
$output .= "\n\t\t\t" . '<div id="tab-'. (empty($tab_id) ? sanitize_title( $title ) : $tab_id) .'" class="'.$css_class.'">';
$output .= ($content=='' || $content==' ') ? ephenyx_manager()->l("Empty tab. Edit page to add content here.") : "\n\t\t\t\t" . js_remove_wpautop($content);
$output .= "\n\t\t\t" . '</div> ' . $this->endBlockComment('.wpb_tab');

echo $output;