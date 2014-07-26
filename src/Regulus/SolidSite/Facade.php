<?php namespace Regulus\SolidSite;

class Facade extends \Illuminate\Support\Facades\Facade {

	protected static function getFacadeAccessor() { return 'solidsite'; }

}