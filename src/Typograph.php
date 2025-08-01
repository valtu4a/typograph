<?php

namespace Emuravjev\Mdash;


class Typograph extends TypographBase
{
	public $traits = array('Quote', 'Dash', 'Symbol', 'Punctuation', 'Number',  'Space', 'Abbr',  'Nobr', 'Date', 'OptAlign', 'Etc', 'Text');

	protected $group_list  = array(
		'Quote'     => true,
		'Dash'      => true,
		'Nobr'      => true,
		'Symbol'    => true,
		'Punctuation' => true,
		'Number'    => true,
		'Date'      => true,
		'Space'     => true,
		'Abbr'      => true,
		'OptAlign'  => true,
		'Text'      => true,
		'Etc'       => true,
	);
	protected $all_options = array(

		'Quote.quotes' => array( 'description' => 'Расстановка «кавычек-елочек» первого уровня', 'selector' => "Quote.*quote" ),
		'Quote.quotation' => array( 'description' => 'Внутренние кавычки-лапки', 'selector' => "Quote", 'setting' => 'no_bdquotes', 'reversed' => true ),

		'Dash.to_libo_nibud' => 'direct',
		'Dash.iz_za_pod' => 'direct',
		'Dash.ka_de_kas' => 'direct',

        'Nobr.super_nbsp' => 'direct',
        'Nobr.nbsp_in_the_end' => 'direct',
        'Nobr.phone_builder' => 'direct',
        'Nobr.phone_builder_v2' => 'direct',
        'Nobr.ip_address' => 'direct',
        'Nobr.spaces_nobr_in_surname_abbr' => 'direct',
        'Nobr.dots_for_surname_abbr' => 'direct',
        'Nobr.nbsp_celsius' => 'direct',
        'Nobr.hyphen_nowrap_in_small_words' => 'direct',
        'Nobr.hyphen_nowrap' => 'direct',
        'Nobr.nowrap' => array(
            'description' => 'Nobr (по умолчанию) & nowrap',
            'disabled' => true,
            'selector' => '*',
            'setting' => 'nowrap'
        ),
        'Nobr.twosym_abbr' => array(
            'description' => 'Неразрывный пробел перед двухсимвольной аббревиатурой',
            'selector' => "Nobr.nobr_twosym_abbr"
        ),

		'Symbol.tm_replace'     => 'direct',
		'Symbol.r_sign_replace' => 'direct',
		'Symbol.copy_replace' => 'direct',
		'Symbol.apostrophe' => 'direct',
		'Symbol.degree_f' => 'direct',
		'Symbol.arrows_symbols' => 'direct',
		'Symbol.no_inches' => array( 'description' => 'Расстановка дюйма после числа', 'selector' => "Quote", 'setting' => 'no_inches', 'reversed' => true ),

		'Punctuation.auto_comma' => 'direct',
		'Punctuation.hellip' => 'direct',
		'Punctuation.fix_double_marks' => 'direct',
		'Punctuation.fix_excl_quest_marks' => 'direct',
		'Punctuation.dot_on_end' => 'direct',

		'Number.minus_between_nums' => 'direct',
		'Number.minus_in_numbers_range' => 'direct',
		'Number.auto_times_x' => 'direct',
		'Number.simple_fraction' => 'direct',
		'Number.math_chars' => 'direct',
        'Number.numeric_sub' => 'direct',
        'Number.numeric_sup' => 'direct',
        'Number.dimensions_sup' => 'direct',
		'Number.thinsp_between_number_triads' => 'direct',
		'Number.thinsp_between_no_and_number' => 'direct',
		'Number.thinsp_between_sect_and_number' => 'direct',

        // Date-related rules
        'Date.years' => 'direct', // Matches years
        'Date.mdash_month_interval' => 'direct', // Matches mdash_month_interval
        'Date.nbsp_and_dash_month_interval' => 'direct', // Matches nbsp_and_dash_month_interval
        'Date.nobr_year_in_date' => 'direct', // Matches nobr_year_in_date
        'Date.nbsp_before_month' => array(
            'description' => 'Неразрывный пробел в датах перед числом и месяцем',
            'selector' => "Date.nbsp_before_month"
        ),
        'Date.space_after_year' => array(
            'description' => 'Пробел после года',
            'selector' => "Date.space_after_year"
        ),
        'Date.nbsp_after_year_abbr' => array(
            'description' => 'Неразрывный пробел после года с сокращением «г.»',
            'selector' => "Date.nbsp_after_year_abbr"
        ),

		'Space.many_spaces_to_one' => 'direct',
		'Space.clear_percent' => 'direct',
		'Space.clear_before_after_punct' => array( 'description' => 'Удаление пробелов перед и после знаков препинания в предложении', 'selector' => 'Space.remove_space_before_punctuationmarks'),
		'Space.autospace_after' => array( 'description' => 'Расстановка пробелов после знаков препинания', 'selector' => 'Space.autospace_after_*'),
		'Space.bracket_fix' => array( 'description' => 'Удаление пробелов внутри скобок, а также расстановка пробела перед скобками',
				'selector' => array('Space.nbsp_before_open_quote', 'Punctuation.fix_brackets')),

        // Abbreviation-related rules
        'Abbr.nbsp_money_abbr' => array(
            'description' => 'Форматирование денежных сокращений (расстановка пробелов и привязка названия валюты к числу)',
            'selector' => array('Abbr.nbsp_money_abbr', 'Abbr.nbsp_money_abbr_rev')
        ),
        'Abbr.nobr_itd_itp' => 'direct',
        'Abbr.nobr_sm_im' => 'direct',
        'Abbr.nobr_acronym' => 'direct',
        'Abbr.nobr_locations' => 'direct',
        'Abbr.nobr_abbreviation' => 'direct',
        'Abbr.ps_pps' => 'direct',
        'Abbr.nbsp_org_abbr' => 'direct',
        'Abbr.nobr_gost' => 'direct',
        'Abbr.nobr_before_unit_volt' => 'direct',
        'Abbr.nbsp_before_unit' => 'direct',
        'Abbr.nbsp_te' => array(
            'description' => 'Обработка сокращения «т.е.»',
            'selector' => "Abbr.nbsp_te"
        ),

		'OptAlign.all' => array( 'description' => 'Все настройки оптического выравнивания', 'hide' => true, 'selector' => 'OptAlign.*'),
		'OptAlign.oa_oquote' => 'direct',
		'OptAlign.oa_obracket_coma' => 'direct',
		'OptAlign.oa_oquote_extra' => 'direct',
		'OptAlign.layout' => array( 'description' => 'Inline стили или CSS' ),

		'Text.paragraphs' => 'direct',
		'Text.auto_links' => 'direct',
		'Text.email' => 'direct',
		'Text.breakline' => 'direct',
		'Text.no_repeat_words' => 'direct',


		//'Etc.no_nbsp_in_nobr' => 'direct',
		'Etc.unicode_convert' => array('description' => 'Преобразовывать html-сущности в юникод', 'selector' => array('*', 'Etc.nobr_to_nbsp'), 'setting' => array('dounicode','active'), 'exact_selector' => true ,'disabled' => true),
		'Etc.nobr_to_nbsp' => 'direct',
		'Etc.split_number_to_triads' => 'direct',

	);

	/**
	 * Получить список имеющихся опций
	 *
	 * @return array
	 *     all    - полный список
	 *     group  - собранный по группам
	 */
	final public function get_options_list()
	{
		$arr['all'] = array();
		$by_group = array();
		foreach($this->all_options as $opt => $op)
		{
			$arr['all'][$opt] = $this->get_option_info($opt);
			$x = explode(".",$opt);
			$by_group[$x[0]][] = $opt;
		}
		$arr['group'] = array();
		foreach($this->group_list as $group => $ginfo)
		{
			if($ginfo === true)
			{
				$trait = $this->get_trait($group);
				if($trait) $info['title'] = $trait->title; else $info['title'] = "Не определено";
			} else {
				$info = $ginfo;
			}
			$info['name'] = $group;
			$info['options'] = array();
			if(is_array($by_group[$group])) foreach($by_group[$group] as $opt) $info['options'][] = $opt;
			$arr['group'][] = $info;
		}
		return $arr;
	}


	/**
	 * Получить информацию о настройке
	 *
	 * @param string $key
	 * @return array|false
	 */
	final protected function get_option_info($key)
	{
		if(!isset($this->all_options[$key])) return false;
		if(is_array($this->all_options[$key])) return $this->all_options[$key];

		if(($this->all_options[$key] == "direct") || ($this->all_options[$key] == "reverse"))
		{
			$pa = explode(".", $key);
			$trait_pattern = $pa[0];
			$trait = $this->get_trait($trait_pattern);
			if(!$trait) return false;
			if(!isset($trait->rules[$pa[1]])) return false;
			$array = $trait->rules[$pa[1]];
			$array['way'] = $this->all_options[$key];
			return $array;
		}
		return false;
	}


	/**
	 * Установка одной метанастройки
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	final public function do_setup($name, $value)
	{
		if(!isset($this->all_options[$name])) return;

		// эта настрока связана с правилом ядра
		if(is_string($this->all_options[$name]))
		{
			$this->set($name, "active", $value );
			return ;
		}
		if(is_array($this->all_options[$name]))
		{
			if(isset($this->all_options[$name]['selector']))
			{
				$setting_name = "active";
				if(isset($this->all_options[$name]['setting'])) $setting_name = $this->all_options[$name]['setting'];
				$this->set($this->all_options[$name]['selector'], $setting_name, $value, isset($this->all_options[$name]['exact_selector']));
			}
		}

		if($name == "OptAlign.layout")
		{
			if($value == "style") $this->set_tag_layout(Lib::LAYOUT_STYLE);
			if($value == "class") $this->set_tag_layout(Lib::LAYOUT_CLASS);
		}

	}

	/**
	 * Запустить типограф со стандартными параметрами
	 *
	 * @param string $text
	 * @param array $options
	 * @return string
	 */
	public static function fast_apply(string $text, $options = null)
	{
		$obj = new self();
		if(is_array($options)) {
            $obj->setup($options);
        }
		$obj->set_text($text);
		return $obj->apply();
	}
}