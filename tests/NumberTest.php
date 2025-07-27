<?php

namespace Tests;

/**
 * Тесты для обработки чисел и единиц измерения типографом.
 */
class NumberTest extends TestCase
{
    /**
     * @var array Массив тестовых данных
     */
    protected $tests = [
        [
            'text' => 'Размер изделия 50х31',
            'result' => 'Размер изделия 50&times;31',
        ],
        [
            'text' => 'Размер изделия 62x21x21',
            'result' => 'Размер изделия 62&times;21&times;21',
        ],
        [
            'text' => 'Площадь квартиры 52 м2',
            'result' => 'Площадь квартиры 52&nbsp;м<sup><small>2</small></sup>',
        ],
        [
            'text' => 'Объем спичечного коробка 26 см3',
            'result' => 'Объем спичечного коробка 26&nbsp;см<sup><small>3</small></sup>',
        ],
        [
            'text' => 'Сегодня проходим § 5',
            'result' => 'Сегодня проходим &sect;&thinsp;5',
        ],
        [
            'text' => 'Сегодня проходим §&nbsp;5',
            'result' => 'Сегодня проходим &sect;&thinsp;5',
        ],
        [
            'text' => 'Направление подготовки 10.04.01_05',
            'result' => 'Направление подготовки 10.04.01_05',
        ],
        [
            'text' => 'Текст "к которому" применить - типограф.',
            'result' => 'Текст &laquo;к&nbsp;которому&raquo; применить&nbsp;&mdash; типограф.',

        ]
    ];

    /**
     * Тестирует корректное добавление верхнего индекса к единицам площади/объема,
     * замену символа 'x' на '×' и обработку символа параграфа (§).
     *
     * @return void
     */
    final public function testSuperscriptForSquareUnits(): void
    {
        $this->runTypographerTests();
    }
}