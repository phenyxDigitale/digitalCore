<?php
$output = $el_class = $el_id = '';
extract(Composer::shortcode_atts(array(
    'el_class' => '',
    'el_id' => '',
    'css_animation' => ''
), $atts));

$el_class = $this->getExtraClass($el_class);
$el_class .= ($this->settings['base']=='vc_raw_code') ? ' wpb_content_element wpb_raw_code' : ' wpb_raw_js';

$css_class =  'wpb_raw_code' . $el_class;
$css_class .= $this->getCSSAnimation( $css_animation );
$code_id = '';
if ( ! empty( $el_id ) ) {
	$code_id = 'id="ace_' . Tools::htmlentitiesUTF8( $el_id ) . '"';
}

$output .= "\n\t".'<div class="'.$css_class.'">';
    $output .= "\n\t\t".'<div class="wpb_wrapper ace-editor" '.$code_id.'>';
        $output .= "\n\t\t\t".$content;
        $output .= "\n\t\t".'</div> '.$this->endBlockComment('.wpb_wrapper');
    $output .= "\n\t".'</div> '.$this->endBlockComment('.wpb_raw_code');
  $output .= '<script>
		  (function () {
				function initAce() {
					if (typeof ace === "undefined") {
						setTimeout(initAce, 100);
						return;
					}
					var editor = ace.edit("ace_' . Tools::htmlentitiesUTF8( $el_id ) . '");
					editor.setTheme("ace/theme/twilight");
					editor.getSession().setMode("ace/mode/php");
					editor.setOptions({
						fontSize: 14,
						minLines: 16,
						maxLines: 30,
						showPrintMargin: false,
						enableBasicAutocompletion: false,
						enableSnippets: false,
						enableLiveAutocompletion: false
					});
					editor.setReadOnly(true)
				}
    			initAce();
	       })();
    </script>';
$output .= ' <script type="text/javascript" src="https://cdn.ephenyx.io/ace/ace.js"></script>';

echo $output;