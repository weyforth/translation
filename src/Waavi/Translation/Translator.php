<?php namespace Waavi\Translation;

use Illuminate\Translation\Translator as IlluminateTranslator;

class Translator extends IlluminateTranslator {

	public function trans($id, array $parameters = array(), $domain = 'messages', $locale = null)
	{
		$value = parent::trans($id, $parameters, $domain, $locale);

		if(!config('translation.debug')) return $value;

		$prepend = is_array($value) || $id == $value ? '' : '{{trans:'.md5($id).'}}';
		return $prepend ? $prepend . $value : $value;
	}

	public function getAllHashed() {
		$toJoin = [
			'validation',
			'copy'
		];

		$output = [];
		$trans = [];

		foreach ($toJoin as $file) {
			$trans[$file] = $this->trans($file);
		}

		$trans = array_dot($trans);

		foreach ($trans as $key => $value) {
			$output[md5($key)] = $key;
		}


		return json_encode($output);
	}

}