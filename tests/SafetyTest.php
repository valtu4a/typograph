<?php

namespace Tests;

use Emuravjev\Mdash\Typograph;

/**
 * Тесты защиты от сбоев и защищённых блоков.
 */
class SafetyTest extends TestCase
{
    /**
     * @var array Массив тестовых данных
     */
    protected $tests = [
        [
            'text' => 'Он сказал: "Читайте "Онегина" в оригинале".',
            'result' => 'Он&nbsp;сказал: &laquo;Читайте &bdquo;Онегина&ldquo; в&nbsp;оригинале&raquo;.',
        ],
        [
            'text' => '<script>var a = "x";</script><pre>"как есть"</pre>',
            'result' => '<script>var a = "x";</script><pre>"как есть"</pre>',
        ],
        [
            'text' => 'Пример <code>echo "hi";</code> и "текст"',
            'result' => 'Пример <code>echo "hi";</code> и&nbsp;&laquo;текст&raquo;',
        ],
        [
            'text' => '<?php $APPLICATION->IncludeComponent("a:b", "c", Array("A" => "Б - В")); ?> Текст "в кавычках"',
            'result' => '<?php $APPLICATION->IncludeComponent("a:b", "c", Array("A" => "Б - В")); ?> Текст &laquo;в&nbsp;кавычках&raquo;',
        ],
    ];

    /**
     * Защищённые блоки не типографируются, остальной текст — как обычно.
     *
     * @return void
     */
    final public function testSafeBlocks(): void
    {
        $this->runTypographerTests();
    }

    /**
     * Пустой текст остаётся пустым и не считается ошибкой.
     *
     * @return void
     */
    final public function testEmptyText(): void
    {
        $this->typographer->set_text('');
        $this->assertSame('', $this->typographer->apply());
        $this->assertTrue($this->typographer->ok);
    }

    /**
     * Текст длиннее порога возвращается без изменений, следующий текст обрабатывается.
     *
     * @return void
     */
    final public function testMaxLength(): void
    {
        $text = str_repeat('Текст "в кавычках" ', 10);
        $this->typographer->max_length = 50;
        $this->typographer->set_text($text);
        $this->assertSame($text, $this->typographer->apply());
        $this->assertFalse($this->typographer->ok);

        $this->typographer->max_length = 0;
        $this->typographer->set_text('Снова "работает"');
        $this->assertSame('Снова &laquo;работает&raquo;', $this->typographer->apply());
        $this->assertTrue($this->typographer->ok);
    }

    /**
     * Исключение внутри типографа не выходит наружу, возвращается исходный текст.
     *
     * @return void
     */
    final public function testExceptionReturnsSource(): void
    {
        $typographer = new class extends Typograph {
            public function init()
            {
                throw new \RuntimeException('boom');
            }
        };
        $typographer->set_text('Текст "x"');

        $this->assertSame('Текст "x"', $typographer->apply());
        $this->assertFalse($typographer->ok);
        $this->assertNotEmpty($typographer->errors);
    }
}
