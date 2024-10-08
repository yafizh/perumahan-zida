<?php

namespace App\Charts;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;

class PenjualanRumahChart extends Chart
{
    public function __construct()
    {
        parent::__construct();
        $this->options([
            "animation" => [
                "duration" => 0
            ],
            'responsive' => true,
            'scales' => [
                'xAxes' => [
                    [
                        "scaleLabel" => [
                            "display" => true,
                            "labelString" => 'Bulan',
                            "fontColor" => '#000'
                        ],
                        "gridLines" => [
                            "display" => false
                        ]
                    ]
                ],
                'yAxes' => [
                    [
                        "scaleLabel" => [
                            "display" => true,
                            "labelString" => 'Jumlah',
                            "fontColor" => '#000'
                        ],
                        'ticks' => [
                            'beginAtZero' => true,
                        ],
                        "gridLines" => [
                            'display' => false
                        ]
                    ],
                ],
            ],
        ]);
        $this->displayLegend(false);
    }

    public function setStepSize($max_of_data, $divide)
    {
        $stepSize = $max_of_data ? ceil($max_of_data / $divide) : 1;
        $this->options([
            'scales' => [
                'yAxes' => [
                    [
                        'ticks' => [
                            'stepSize' => $stepSize
                        ],
                    ],
                ],
            ],
        ]);
    }
}
