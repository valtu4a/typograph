<?php

namespace Emuravjev\Mdash;

/**
 * Основной класс типографа Евгения Муравьёва
 * реализует основные методы запуска и работы типографа
 *
 */
class TypographBase
{
	private $_text = "";
	private $initialized = false;

	/**
	 * Список Трейтов, которые надо применить к типографированию
	 *
	 * @var array
	 */
	protected $traits = array() ;
	protected $traits_index = array() ;
	protected $trait_objects = array() ;

	public $ok             = false;
	public $debug_enabled  = false;
	public $logging        = false;
	public $logs           = array();
	public $errors         = array();
	public $debug_info     = array();

	private $use_layout = false;
	private $class_layout_prefix = false;
	private $use_layout_set = false;
	public $disable_notg_replace = false;
	public $remove_notg = false;

	public $settings = array();

	final protected function log($str, $data = null)
	{
		if(!$this->logging) return;
		$this->logs[] = array('class' => '', 'info' => $str, 'data' => $data);
	}

	protected function trait_log($trait, $str, $data = null)
	{
		$this->logs[] = array('class' => $trait, 'info' => $str, 'data' => $data);
	}

	protected function error($info, $data = null)
	{
		$this->errors[] = array('class' => '', 'info' => $info, 'data' => $data);
		$this->log("ERROR $info", $data );
	}

	protected function trait_error($trait, $info, $data = null)
	{
		$this->errors[] = array('class' => $trait, 'info' => $info, 'data' => $data);
	}

	protected function debug($class, $place, &$after_text, $after_text_raw = "")
	{
		if(!$this->debug_enabled) return;
		$this->debug_info[] = array(
				'trait'  => $class == $this ? false: true,
				'class' => is_object($class)? get_class($class) : $class,
				'place' => $place,
				'text'  => $after_text,
				'text_raw'  => $after_text_raw,
			);
	}



	protected $_safe_blocks = array();


	/**
	 * Включить режим отладки, чтобы посмотреть последовательность вызовов
	 * третов и правил после
	 *
	 */
	public function debug_on()
	{
		$this->debug_enabled = true;
	}

	/**
	 * Включить режим отладки, чтобы посмотреть последовательность вызовов
	 * третов и правил после
	 *
	 */
	public function log_on()
	{
		$this->logging = true;
	}

	/**
     * Добавление защищенного блока
     *
     * <code>
     *  Jare_Typograph_Tool::addCustomBlocks('<span>', '</span>');
     *  Jare_Typograph_Tool::addCustomBlocks('\<nobr\>', '\<\/span\>', true);
     * </code>
     *
     * @param 	string $id идентификатор
     * @param 	string $open начало блока
     * @param 	string $close конец защищенного блока
     * @param 	string $tag тэг
     * @return  void
     */
    private function _add_safe_block($id, $open, $close, $tag)
    {
    	$this->_safe_blocks[] = array(
    			'id' => $id,
    			'tag' => $tag,
    			'open' =>  $open,
    			'close' =>  $close,
    		);
    }

    /**
     * Список защищенных блоков
     *
     * @return 	array
     */
    public function get_all_safe_blocks()
    {
    	return $this->_safe_blocks;
    }

    /**
     * Удаленного блока по его номеру ключа
     *
     * @param 	string $id идентифиактор защищённого блока
     * @return  void
     */
    public function remove_safe_block($id)
    {
    	foreach($this->_safe_blocks as $k => $block) {
    		if($block['id']==$id) unset($this->_safe_blocks[$k]);
    	}
    }


    /**
     * Добавление защищенного блока
     *
     * @param 	string $tag тэг, который должен быть защищён
     * @return  void
     */
    public function add_safe_tag($tag)
    {
    	$open = preg_quote("<", '/'). $tag."[^>]*?" .  preg_quote(">", '/');
    	$close = preg_quote("</$tag>", '/');
    	$this->_add_safe_block($tag, $open, $close, $tag);
    	return true;
    }


    /**
     * Добавление защищенного блока
     *
     * @param 	string $open начало блока
     * @param 	string $close конец защищенного блока
     * @param 	bool $quoted специальные символы в начале и конце блока экранированы
     * @return  void
     */
    public function add_safe_block($id, $open, $close, $quoted = false)
    {
    	$open = trim($open);
    	$close = trim($close);

    	if (empty($open) || empty($close))
    	{
    		return false;
    	}

    	if (false === $quoted)
    	{
    		$open = preg_quote($open, '/');
            $close = preg_quote($close, '/');
    	}

    	$this->_add_safe_block($id, $open, $close, "");
    	return true;
    }


    /**
     * Сохранение содержимого защищенных блоков
     *
     * @param   string $text
     * @param   bool $safe если true, то содержимое блоков будет сохранено, иначе - раскодировано.
     * @return  string
     */
    public function safe_blocks($text, $way, $show = true)
    {
    	if (count($this->_safe_blocks))
    	{
    		$safeType = true === $way ? "Lib::encrypt_tag(\$m[2])" : "stripslashes(Lib::decrypt_tag(\$m[2]))";
    		$safeBlocks = true === $way ? $this->_safe_blocks : array_reverse($this->_safe_blocks);
       		foreach ($safeBlocks as $block)
       		{
                $text = preg_replace_callback(
                    "/({$block['open']})(.+?)({$block['close']})/s",
                    function($m) { return $m[1].'.$safeType . '.$m[3]; },
                    $text
                );
        	}
    	}

    	return $text;
    }


     /**
     * Декодирование блоков, которые были скрыты в момент типографирования
     *
     * @param   string $text
     * @return  string
     */
    public function decode_internal_blocks($text)
    {
		return Lib::decode_internal_blocks($text);
    }


	private function create_object($trait)
	{
		$trait = "Emuravjev\\Mdash\\Traits\\" . $trait;

		if(!class_exists($trait))
		{
			$this->error("Класс $trait не найден. Пожалуйста, подргузите нужный файл.");
			return null;
		}

		$obj = new $trait();
		$obj->EMT     = $this;
		$obj->logging = $this->logging;
		return $obj;
	}

	private function get_short_trait($traitname)
	{
        if(preg_match("/^EMT_Traits_([a-zA-Z0-9_]+)$/",$traitname, $m))
		{
			return $m[1];
		}
		return $traitname;
	}

	private function _init()
	{
		foreach($this->traits as $trait)
		{
			if(isset($this->trait_objects[$trait])) continue;
			$obj = $this->create_object($trait);
			if($obj == null) continue;
			$this->trait_objects[$trait] = $obj;
		}

		if(!$this->initialized)
		{
			$this->add_safe_tag('pre');
			$this->add_safe_tag('script');
			$this->add_safe_tag('style');
			$this->add_safe_tag('notg');
			$this->add_safe_block('span-notg', '<span class="_notg_start"></span>', '<span class="_notg_end"></span>');
		}
		$this->initialized = true;
	}





	/**
	 * Инициализация класса, используется чтобы задать список трейтов или
	 * список защищённых блоков, которые можно использовать.
	 * Также здесь можно отменить защищённые блоки по умлочнаию
	 *
	 */
	public function init()
	{

	}

	/**
	 * Добавить трейт
	 *
	 * @param mixed $class - имя класса трейта, или сам объект
	 * @param string $altname - альтернативное имя, если мы хотим иметь два одинаковых трейта в обработке
 	 * @return mixed
	 */
	public function add_trait($class, $altname = false)
	{
		if(is_object($class))
		{
			if(!is_a($class, "Traits\Base"))
			{
				$this->error("You are adding Trait that doesn't inherit base class Traits\Base", get_class($class));
				return false;
			}

			$class->EMT     = $this;
			$class->logging = $this->logging;
			$this->trait_objects[($altname ?: get_class($class))] = $class;
			$this->traits[] = ($altname ?: get_class($class));
			return true;
		}
		if(is_string($class))
		{
			$obj = $this->create_object($class);
			if($obj === null)
				return false;
			$this->trait_objects[($altname ?: $class)] = $obj;
			$this->traits[] = ($altname ?: $class);
			return true;
		}
		$this->error("Чтобы добавить трейт необходимо передать имя или объект");
		return false;
	}

	/**
	 * Получаем трейт по идентификатору, т.е. названию класса
	 *
	 * @param mixed $name
	 */
	public function get_trait($name)
	{
		if(isset($this->trait_objects[$name])) return $this->trait_objects[$name];
		foreach($this->traits as $trait)
		{
			if($trait == $name)
			{
				$this->_init();
				return $this->trait_objects[$name];
			}
			if($this->get_short_trait($trait) == $name)
			{
				$this->_init();
				return $this->trait_objects[$trait];
			}
		}
		$this->error("Трейт с идентификатором $name не найден");
		return false;
	}

	/**
	 * Задаём текст для применения типографа
	 *
	 * @param string $text
	 */
	final public function set_text(string $text)
	{
		$this->_text = $text;
	}



	/**
	 * Запустить типограф на выполнение
	 *
	 */
	public function apply($traits = null)
	{
		$this->ok = false;

		$this->init();
		$this->_init();

		$atraits = $this->traits;
		if(is_string($traits)) $atraits = array($traits);
		elseif(is_array($traits)) $atraits = $traits;

		$this->debug($this, 'init', $this->_text);

		$this->_text = $this->safe_blocks($this->_text, true);
		$this->debug($this, 'safe_blocks', $this->_text);

		$this->_text = Lib::safe_tag_chars($this->_text, true);
		$this->debug($this, 'safe_tag_chars', $this->_text);

		$this->_text = Lib::clear_special_chars($this->_text);
		$this->debug($this, 'clear_special_chars', $this->_text);

		foreach ($atraits as $trait)
		{
			// если установлен режим разметки тэгов то выставим его
			if($this->use_layout_set)
				$this->trait_objects[$trait]->set_tag_layout_ifnotset($this->use_layout);

			if($this->class_layout_prefix)
				$this->trait_objects[$trait]->set_class_layout_prefix($this->class_layout_prefix);

			// влючаем, если нужно
			if($this->debug_enabled) $this->trait_objects[$trait]->debug_on();
			if($this->logging) $this->trait_objects[$trait]->logging = true;

			// применяем трэт
			//$this->trait_objects[$trait]->set_text(&$this->_text);
			$this->trait_objects[$trait]->set_text($this->_text);
			$this->trait_objects[$trait]->apply();

			// соберём ошибки если таковые есть
			if(count($this->trait_objects[$trait]->errors)>0)
				foreach($this->trait_objects[$trait]->errors as $err )
					$this->trait_error($trait, $err['info'], $err['data']);

			// логгирование
			if($this->logging)
				if(count($this->trait_objects[$trait]->logs)>0)
					foreach($this->trait_objects[$trait]->logs as $log )
						$this->trait_log($trait, $log['info'], $log['data']);

			// отладка
			if($this->debug_enabled) {
				foreach($this->trait_objects[$trait]->debug_info as $di)
				{
					$unsafetext = $di['text'];
					$unsafetext = Lib::safe_tag_chars($unsafetext, false);
					$unsafetext = $this->safe_blocks($unsafetext, false);
					$this->debug($trait, $di['place'], $unsafetext, $di['text']);
				}
			}
		}


		$this->_text = $this->decode_internal_blocks($this->_text);
		$this->debug($this, 'decode_internal_blocks', $this->_text);

		if($this->is_on('dounicode'))
		{
			Lib::convert_html_entities_to_unicode($this->_text);
		}

		$this->_text = Lib::safe_tag_chars($this->_text, false);
		$this->debug($this, 'unsafe_tag_chars', $this->_text);

		$this->_text = $this->safe_blocks($this->_text, false);
		$this->debug($this, 'unsafe_blocks', $this->_text);

		if(!$this->disable_notg_replace)
		{
			$repl = array('<span class="_notg_start"></span>', '<span class="_notg_end"></span>');
			if($this->remove_notg) $repl = "";
			$this->_text = str_replace( array('<notg>','</notg>'), $repl , $this->_text);
		}
		$this->_text = trim($this->_text);
		$this->ok = (count($this->errors)==0);
		return $this->_text;
	}

	/**
	 * Получить содержимое <style></style> при использовании классов
	 *
	 * @param bool $list false - вернуть в виде строки для style или как массив
	 * @param bool $compact не выводить пустые классы
	 * @return string|array
	 */
	public function get_style($list = false, $compact = false)
	{
		$this->_init();

		$res = array();
		foreach ($this->traits as $trait)
		{
			$arr =$this->trait_objects[$trait]->classes;
			if(!is_array($arr)) continue;
			foreach($arr as $classname => $str)
			{
				if(($compact) && (!$str)) continue;
				$clsname = ($this->class_layout_prefix ? $this->class_layout_prefix : "" ).(isset($this->trait_objects[$trait]->class_names[$classname]) ? $this->trait_objects[$trait]->class_names[$classname] :$classname);
				$res[$clsname] = $str;
			}
		}
		if($list) return $res;
		$str = "";
		foreach($res as $k => $v)
		{
			$str .= ".$k { $v }\n";
		}
		return $str;
	}





	/**
	 * Установить режим разметки,
	 *   Lib::LAYOUT_STYLE - с помощью стилей
	 *   Lib::LAYOUT_CLASS - с помощью классов
	 *   Lib::LAYOUT_STYLE|Lib::LAYOUT_CLASS - оба метода
	 *
	 * @param int $layout
	 */
	public function set_tag_layout($layout = Lib::LAYOUT_STYLE)
	{
		$this->use_layout = $layout;
		$this->use_layout_set = true;
	}

	/**
	 * Установить префикс для классов
	 *
	 * @param string|bool $prefix если true то префикс 'emt_', иначе то, что передали
	 */
	public function set_class_layout_prefix($prefix )
	{
		$this->class_layout_prefix = $prefix === true ? "emt_" : $prefix;
	}

	/**
	 * Включить/отключить правила, согласно карте
	 * Формат карты:
	 *    'Название трейта 1' => array ( 'правило1', 'правило2' , ...  )
	 *    'Название трейта 2' => array ( 'правило1', 'правило2' , ...  )
	 *
	 * @param array $map
	 * @param bool $disable если ложно, то $map соответствует тем правилам, которые надо включить
	 *                         иначе это список правил, которые надо выключить
	 * @param bool $strict строго, т.е. те которые не в списке будут тоже обработаны
	 */
	public function set_enable_map($map, $disable = false, $strict = true)
	{
		if(!is_array($map)) return;
		$traits = array();
		foreach($map as $trait => $list)
		{
			$traitx = $this->get_trait($trait);
			if(!$traitx)
			{
				$this->log("Трейт $trait не найден при применении карты включаемых правил");
				continue;
			}
			$traits[] = $traitx;

			if($list === true) // все
			{
				$traitx->activate(array(), !$disable ,  true);
			} elseif(is_string($list)) {
				$traitx->activate(array($list), $disable ,  $strict);
			} elseif(is_array($list)) {
				$traitx->activate($list, $disable ,  $strict);
			}
		}
		if($strict)
		{
			foreach($this->traits as $trait)
			{
				if(in_array($this->trait_objects[$trait], $traits)) continue;
				$this->trait_objects[$trait]->activate(array(), $disable ,  true);
			}
		}

	}


	/**
	 * Установлена ли настройка
	 *
	 * @param string $key
	 */
	public function is_on($key)
	{
		if(!isset($this->settings[$key])) return false;
		$kk = $this->settings[$key];
		return ((strtolower($kk)=="on") || ($kk === "1") || ($kk === true) || ($kk === 1));
	}


	/**
	 * Установить настройку
	 *
	 * @param mixed $selector
	 * @param string $setting
	 * @param mixed $value
	 */
	protected function doset($selector, $key, $value)
	{
		$trait_pattern = false;
		$rule_pattern = false;
		//if(($selector === false) || ($selector === null) || ($selector === false) || ($selector === "*")) $type = 0;
		if(is_string($selector))
		{
			if(strpos($selector,".")===false)
			{
				$trait_pattern = $selector;
			} else {
				$pa = explode(".", $selector);
				$trait_pattern = $pa[0];
				array_shift($pa);
				$rule_pattern = implode(".", $pa);
			}
		}
		Lib::_process_selector_pattern($trait_pattern);
		Lib::_process_selector_pattern($rule_pattern);
		if($selector == "*") $this->settings[$key] = $value;

		foreach ($this->traits as $trait)
		{
			$t1 = $this->get_short_trait($trait);
			if(!Lib::_test_pattern($trait_pattern, $t1))	if(!Lib::_test_pattern($trait_pattern, $trait)) continue;
			$trait_obj = $this->get_trait($trait);
			if($key == "active")
			{
				foreach($trait_obj->rules as $rulename => $v)
				{
					if(!Lib::_test_pattern($rule_pattern, $rulename)) continue;
					if((strtolower($value) === "on") || ($value===1) || ($value === true) || ($value=="1")) $trait_obj->enable_rule($rulename);
					if((strtolower($value) === "off") || ($value===0) || ($value === false) || ($value=="0")) $trait_obj->disable_rule($rulename);
				}
			} else {
				if($rule_pattern===false)
				{
					$trait_obj->set($key, $value);
				} else {
					foreach($trait_obj->rules as $rulename => $v)
					{
						if(!Lib::_test_pattern($rule_pattern, $rulename)) continue;
						$trait_obj->set_rule($rulename, $key, $value);
					}
				}
			}
		}
	}


	/**
	 * Установить настройки для трейтов и правил
	 * 	1. если селектор является массивом, то тогда установка правил будет выполнена для каждого
	 *     элемента этого массива, как отдельного селектора.
	 *  2. Если $key не является массивом, то эта настройка будет проставлена согласно селектору
	 *  3. Если $key массив - то будет задана группа настроек
	 *       - если $value массив , то настройки определяются по ключам из массива $key, а значения из $value
	 *       - иначе, $key содержит ключ-значение как массив
	 *  4. $exact_match - если true тогда array selector будет соответствовать array $key, а не произведению массивов
	 *
	 * @param mixed $selector
	 * @param mixed $key
	 * @param mixed $value
	 * @param mixed $exact_match
	 */
	public function set($selector, $key , $value = false, $exact_match = false)
	{
		if($exact_match && is_array($selector) && is_array($key) && count($selector)==count($key)) {
			$idx = 0;
			foreach($key as $x => $y){
				if(is_array($value))
				{
					$kk = $y;
					$vv = $value[$x];
				} else {
					$kk = ( $value ? $y : $x );
					$vv = ( $value ? $value : $y );
				}
				$this->set($selector[$idx], $kk , $vv);
				$idx++;
			}
			return ;
		}
		if(is_array($selector))
		{
			foreach($selector as $val) $this->set($val, $key, $value);
			return;
		}
		if(is_array($key))
		{
			foreach($key as $x => $y)
			{
				if(is_array($value))
				{
					$kk = $y;
					$vv = $value[$x];
				} else {
					$kk = ( $value ? $y : $x );
					$vv = ( $value ? $value : $y );
				}
				$this->set($selector, $kk, $vv);
			}
			return ;
		}
		$this->doset($selector, $key, $value);
	}


	/**
	 * Возвращает список текущих третов, которые установлены
	 *
	 */
	public function get_traits_list()
	{
		return $this->traits;
	}

	/**
	 * Установка одной метанастройки
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function do_setup($name, $value)
	{

	}


	/**
	 * Установить настройки
	 *
	 * @param array $setupMap - массив с настройками
     * @return void
	 */
    final public function setup(array $setupMap): void
    {
        if (isset($setupMap['map']) || isset($setupMap['maps'])) {

            // Преобразование одиночной карты в maps[]
            if (isset($setupMap['map'])) {
                $ret = [
                    'map' => $setupMap['map'],
                    'disable' => $setupMap['map_disable'] ?? false,
                    'strict' => $setupMap['map_strict'] ?? true,
                ];
                $setupMap['maps'] = [$ret];
                unset($setupMap['map'], $setupMap['map_disable'], $setupMap['map_strict']);
            }

            // Применение всех карт
            if (is_array($setupMap['maps'])) {
                foreach ($setupMap['maps'] as $map) {
                    $this->set_enable_map(
                        $map['map'],
                        $map['disable'] ?? false,
                        $map['strict'] ?? false
                    );
                }
            }

            unset($setupMap['maps']);
        }

        // Применение оставшихся настроек
        foreach ($setupMap as $key => $value) {
            $this->do_setup($key, $value);
        }
    }

}