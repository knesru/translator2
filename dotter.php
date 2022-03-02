<?php

class dotter
{
    public static function dot(array $array, $prepend = '')
    {
        $results = [];
        foreach ($array as $key => $value) {
            if (\is_array($value) && !empty($value)) {
                $results = array_merge($results, self::dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }

    public static function dot2(array $array, $prepend = '', array &$results = [])
    {
        foreach ($array as $key => $value) {
            if (\is_array($value) && !empty($value)) {
                self::dot2($value,  $prepend . $key . '.', $results);
            } else {
                $results[$prepend . $key] = $value;
            }
        }
        return $results;
    }

    public static function unpa(){
        print_r(func_get_args());
    }
}


$a = ['x' => [
        'y' => 'abc',
        ],
        'z' => [
            'sss'=>'000',
            'nnn'=>[],
            'more_levels'=>$a = ['x' => [
                'y' => 'abc',
            ],
                'z' => [
                    'sss'=>'000',
                    'nnn'=>[]
                ],
                'a'=>'b',
                'c' => [
                    'd'=>'e',
                    'f'=>[
                        'g'=>'h',
                        'more_levels'=>['x' => [
                            'y' => 'abc',
                        ],
                            'z' => [
                                'sss'=>'000',
                                'nnn'=>[]
                            ],
                            'a'=>'b',
                            'c' => [
                                'd'=>'e',
                                'f'=>[
                                    'g'=>'h'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ],
    'a'=>'b',
    'c' => [
        'd'=>'e',
        'f'=>[
            'g'=>'h',
            'more_levels'=>['x' => [
                'y' => 'abc',
            ],
                'z' => [
                    'sss'=>'000',
                    'nnn'=>[]
                ],
                'a'=>'b',
                'c' => [
                    'd'=>'e',
                    'f'=>[
                        'g'=>'h'
                    ]
                ]
            ]
        ]
    ]
    ];


$t = microtime(true);
for($i=0; $i<1000000; $i++){
    $d = dotter::dot($a, 'pref');
}
print round(microtime(true) - $t,2)."\n";

$t = microtime(true);
for($i=0; $i<1000000; $i++){
$d1 = dotter::dot2($a,'pref');
}
print round(microtime(true) - $t, 2)."\n";
