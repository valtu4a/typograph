<?php

namespace Tests;

/**
 * Тесты расстановки кавычек.
 */
class QuoteTest extends TestCase
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
            'text' => 'Он сказал: <q>Читайте "Онегина" в оригинале</q>.',
            'result' => 'Он&nbsp;сказал: <q>Читайте &bdquo;Онегина&ldquo; в&nbsp;оригинале</q>.',
        ],
        [
            'text' => '<q cite="http://x">Фонд "Открытое общество" работает</q>',
            'result' => '<q cite="http://x">Фонд &bdquo;Открытое общество&ldquo; работает</q>',
        ],
        [
            'text' => 'Цитата <q>без кавычек</q> и "текст" потом',
            'result' => 'Цитата <q>без&nbsp;кавычек</q> и&nbsp;&laquo;текст&raquo; потом',
        ],
        [
            'text' => '<p>Абзац <q>с "кавычками"</q></p><p>И "ещё"</p>',
            'result' => '<p>Абзац <q>с&nbsp;&bdquo;кавычками&ldquo;</q></p><p>И &laquo;ещё&raquo;</p>',
        ],
        [
            'text' => 'Труба 3" и <q>"цитата"</q>',
            'result' => 'Труба 3&Prime; и <q>&bdquo;цитата&ldquo;</q>',
        ],
    ];

    /**
     * Внутри <q> внешние кавычки рисует CSS, поэтому в тексте ставятся лапки.
     *
     * @return void
     */
    final public function testQuotes(): void
    {
        $this->runTypographerTests();
    }
}
