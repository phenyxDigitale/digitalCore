<?php
$vc_manager = ephenyx_manager();

$output = $title = $interval = $el_class = '';
extract( Composer::shortcode_atts( array(
	'title' => '',
	'interval' => 0,
	'el_class' => ''
), $atts ) );

Context::getContext()->controller->addJqueryUI('ui.effect');
Context::getContext()->controller->addJqueryUI('effects.core');
Context::getContext()->controller->addJqueryUI('ui.tabs');


$el_class = $this->getExtraClass( $el_class );

$element = 'wpb_tabs';
if ( 'vc_tour' == $this->shortcode ) $element = 'wpb_tour';

preg_match_all( '/vc_tab([^\]]+)/i', $content, $matches, PREG_OFFSET_CAPTURE );
$tab_titles = array();
if ( isset( $matches[1] ) ) {
	$tab_titles = $matches[1];
}
$tabs_nav = '';
$tabs_nav .= '<ul class="wpb_tabs_nav ui-tabs-nav vc_clearfix">';
foreach ( $tab_titles as $tab ) {
	$tab_atts = Composer::shortcode_parse_atts($tab[0]);
	if(isset($tab_atts['title'])) {
		$tabs_nav .= '<li><a href="#tab-' . ( isset( $tab_atts['tab_id'] ) ? $tab_atts['tab_id'] : Tools::safeOutput( $tab_atts['title'] ) ) . '">' . $tab_atts['title'] . '</a></li>';
	}
}
$tabs_nav .= '</ul>' . "\n";

$css_class =  trim( $element . ' wpb_content_element ' . $el_class );

$output .= "\n\t" . '<div class="' . $css_class . '" data-interval="' . $interval . '">';
$output .= "\n\t\t" . '<div class="wpb_wrapper wpb_tour_tabs_wrapper ui-tabs vc_clearfix">';
$output .= widget_title( array( 'title' => $title, 'extraclass' => $element . '_heading' ) );
$output .= "\n\t\t\t" . $tabs_nav;
$output .= "\n\t\t\t" . js_remove_wpautop( $content );
if ( 'vc_tour' == $this->shortcode ) {
	$output .= "\n\t\t\t" . '<div class="wpb_tour_next_prev_nav vc_clearfix"> <span class="wpb_prev_slide"><a href="#prev" title="' . $vc_manager->l('Previous tab') . '">' . $vc_manager->l('Previous tab') . '</a></span> <span class="wpb_next_slide"><a href="#next" title="' . $vc_manager->l('Next tab') . '">' . $vc_manager->l('Next tab') . '</a></span></div>';
}
$output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.wpb_wrapper' );
$output .= "\n\t" . '</div> ' . $this->endBlockComment( $element );

echo $output;