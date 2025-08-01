<?php

namespace Emuravjev\Mdash\Traits;

/**
 * @see Base
 */

class Etc extends Base
{

	public $classes = array(
			'nowrap'           => 'word-spacing:nowrap;',
		);

	/**
	 * Базовые параметры типографа
	 *
	 * @var array
	 */
	public $title = "Прочее";
	public $rules = array(
		'acute_accent' => array(
				'description'	=> 'Акцент',
				'pattern' 		=> '/(у|е|ы|а|о|э|я|и|ю|ё)\`(\w)/i',
				'replacement' 	=> '\1&#769;\2'
			),

		'word_sup' => array(
				'description'	=> 'Надстрочный текст после символа ^',
				'pattern' 		=> '/((\s|\&nbsp\;|^)+)\^([a-zа-яё0-9\.\:\,\-]+)(\s|\&nbsp\;|$|\.$)/ieu',
				'replacement' 	=> '"" . $this->tag($this->tag($m[3],"small"),"sup") . $m[4]'
			),
		'century_period' => array(
				'description'	=> 'Тире между диапозоном веков',
				'pattern' 		=> '/(\040|\t|\&nbsp\;|^)([XIV]{1,5})(-|\&mdash\;)([XIV]{1,5})(( |\&nbsp\;)?(в\.в\.|вв\.|вв|в\.|в))/eu',
				'replacement' 	=> '$m[1] .$this->tag($m[2]."&mdash;".$m[4]." вв.","span", array("class"=>"nowrap"))'
			),
		'time_interval' => array(
				'description'	=> 'Тире и отмена переноса между диапозоном времени',
				'pattern' 		=> '/([^\d\>]|^)([\d]{1,2}\:[\d]{2})(-|\&mdash\;|\&minus\;)([\d]{1,2}\:[\d]{2})([^\d\<]|$)/eui',
				'replacement' 	=> '$m[1] . $this->tag($m[2]."&mdash;".$m[4],"span", array("class"=>"nowrap")).$m[5]'
			),
		'split_number_to_triads' => array(
				'description'	=> 'Разбиение числа на триады',
				'pattern' 		=> '/([^a-zA-Z0-9<\)]|^)([0-9]{5,})([^a-zA-Z>\(]|$)/eu',
				'replacement' 	=> '$m[1].str_replace(" ","&thinsp;",Emuravjev\Mdash\Lib::split_number($m[2])).$m[3] '
				//'function'	    => 'split_number'
			),
		'expand_no_nbsp_in_nobr' => array(
				'description'	=> 'Удаление nbsp в nobr/nowrap тегах',
				'function'	=> 'remove_nbsp'
			),
		'nobr_to_nbsp' => array(
				'description'	=> 'Преобразование nobr в nbsp',
				'disabled'		=> true,
				'function'	=> 'nobr_to_nbsp'
			),
		);

	final protected function remove_nbsp()
	{
		$tag = $this->tag("###", 'span', array('class' => "nowrap"));
		$arr = explode("###", $tag);
		$b = preg_quote($arr[0], '/');
		$e = preg_quote($arr[1], '/');

		$match = '/(^|[^a-zа-яё])([a-zа-яё]+)&nbsp;('.$b.')/iu';
		do {
			$this->_text = preg_replace($match, '\1\3\2 ', $this->_text);
		} while(preg_match($match, $this->_text));

		$match = '/('.$e.')&nbsp;([a-zа-яё]+)($|[^a-zа-яё])/iu';
		do {
			$this->_text = preg_replace($match, ' \2\1\3', $this->_text);
		} while(preg_match($match, $this->_text));

		$this->_text = $this->preg_replace_e('/'.$b.'.*?'.$e.'/iue', 'str_replace("&nbsp;"," ",$m[0]);' , $this->_text );
	}

	final protected function nobr_to_nbsp()
	{
		$tag = $this->tag("###", 'span', array('class' => "nowrap"));
		$arr = explode("###", $tag);
		$b = preg_quote($arr[0], '/');
		$e = preg_quote($arr[1], '/');
		$this->_text = $this->preg_replace_e('/'.$b.'(.*?)'.$e.'/iue', 'str_replace(" ","&nbsp;",$m[1]);' , $this->_text );
	}
}