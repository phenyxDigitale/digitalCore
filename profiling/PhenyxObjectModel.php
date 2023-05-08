<?php

abstract class PhenyxObjectModel extends PhenyxObjectModelCore {

    public static $debug_list = [];

    public function __construct($id = null, $idLang = null, $id_company = null) {

        parent::__construct($id, $idLang, $id_company);

        $classname = get_class($this);

        if (!isset(self::$debug_list[$classname])) {
            self::$debug_list[$classname] = [];
        }

        $class_list = ['PhenyxObjectModel', 'PhenyxObjectModelCore', $classname, $classname . 'Core'];
        $backtrace = debug_backtrace();

        foreach ($backtrace as $trace_id => $row) {

            if (!isset($backtrace[$trace_id]['class']) || !in_array($backtrace[$trace_id]['class'], $class_list)) {
                break;
            }

        }

        $trace_id--;

        self::$debug_list[$classname][] = [
            'file' => @$backtrace[$trace_id]['file'],
            'line' => @$backtrace[$trace_id]['line'],
        ];
    }

}
