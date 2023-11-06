<?php

$vc = ephenyx_manager();

extract( Composer::shortcode_atts( array(
	'id' => '',
	'attach_pdf' => '',
	'btnDownloadPdf' => false,
	'btnPrint'     => false,
	'btnShare'     => false
), $atts ) );
$context = Context::getContext();
$context->controller->addJS(_EPH_JS_DIR_ . 'pdfWorker/flipbook.min.js');
$context->controller->addCSS(_EPH_CSS_DIR_ . 'flipbook.style.css');
$path = ComposerMedia::getPathMediaById($id);
$data = $context->smarty->createTemplate(_EPH_ALL_THEMES_DIR_. 'script.tpl');

$data->assign([
	'pdf_dir'           => $path, 
	'btnDownloadPdf' => $btnDownloadPdf,
	'btnPrint' => $btnPrint,
	'btnShare'             => $btnShare,
]);

echo $data->fetch();
