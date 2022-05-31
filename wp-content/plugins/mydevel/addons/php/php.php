<?php
class MDPhpAddon extends MDAddOn {
  private static $_instance = null;
  public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new MDPhpAddon();
		}
		return self::$_instance;
	}

  public function init() {
		parent::init();

	}
}
