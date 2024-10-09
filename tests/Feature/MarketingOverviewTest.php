<?php

namespace Tests\Feature;

use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class MarketingOverviewTest extends TestCase
{
    const URL = '/api/marketing-overview/data?';

    public static array $headers = [
        'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9hdXRoXC9yZWZyZXNoIiwiaWF0IjoxNjIxNDE4MjEyLCJleHAiOjE2MjE0MjkzMzQsIm5iZiI6MTYyMTQyMjEzNCwianRpIjoidWRrb1RHeUpNVlNVdWdQMCIsInN1YiI6MiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.ibfb2W068mNlIixwGkYxGULG-_x8WDZD1SyuduL5xp0'
    ];

    public static array $filterArray = [
        'date' => [
            '2021-04-18T21:00:00.000Z',
            '2021-05-19T20:59:59.999Z'
        ],
        'currency' => [
            'id' => 3,
            'code' => 'EUR',
            'name' => 'Euro',
        ],
        'date_granularity' => 'Daily'
    ];

    public static array $structure = [
        'data' => [
            'date_start',
            'date_end',
            'streak' => [
                'type',
                'value'
            ],
            'break_even',
            'over_period' => [
                'day' => [
                    'revenue' => [
                        'value',
                        'estimate',
                        'percentage_change',
                    ],
                    'cmam' => [
                        'value',
                        'estimate',
                        'percentage_change',
                    ],
                ],
                'week' => [
                    'revenue' => [
                        'value',
                        'estimate',
                        'percentage_change',
                    ],
                    'cmam' => [
                        'value',
                        'estimate',
                        'percentage_change',
                    ],
                ],
                'month' => [
                    'revenue' => [
                        'value',
                        'estimate',
                        'percentage_change',
                    ],
                    'cmam' => [
                        'value',
                        'estimate',
                        'percentage_change',
                    ],
                ],
            ],
            'stores' => [
                '*' => [
                    'id',
                    'name',
                    'icon_link',
                    'color',
                    'revenue' => [
                        'per_day' => [
                            '*' => [

                            ],
                        ],
                        'total',
                        'estimate',
                    ],
                    'orders' => [
                        'per_day' => [
                            '*' => [

                            ],
                        ],
                        'total',
                        'estimate',
                    ],
                    'contribution_margin' => [
                        'per_day' => [
                            '*' => [

                            ],
                        ],
                        'total',
                        'estimate',
                    ],
                    'spend' => [
                        'per_day' => [
                            '*' => [

                            ],
                        ],
                        'total',
                        'estimate',
                        'marketing_channels' => [
                            '*' => [
                                'marketing_chanel_id',
                                'marketing_chanel_name',
                                'total'
                            ]
                        ]
                    ],
                    'conversion_rate' => [
                        'per_day' => [
                            '*' => [

                            ],
                        ],
                        'total',
                        'estimate',
                        'last_30_minutes'
                    ],
                    'profit' => [
                        'per_day' => [
                            '*' => [

                            ],
                        ],
                        'total',
                        'estimate',
                    ],
                    'spend_ratio' => [
                        'per_day' => [
                            '*' => [

                            ],
                        ],
                        'total',
                        'estimate',
                    ],
                    'contribution_margin_ratio' => [
                        'per_day' => [
                            '*' => [

                            ],
                        ],
                        'total',
                        'estimate',
                    ],
                    'cmam' => [
                        'per_day' => [
                            '*' => [

                            ],
                        ],
                        'total',
                        'estimate',
                    ],
                    'packing_cost',
                    'aov',
                    'active_users',
                    'add_to_cart',
                ]
            ],
            'totals' => [
                'revenue' => [
                    'estimate',
                    'value',
                    'per_hour' => [
                        '*' => [

                        ]
                    ],
                    'per_hour_estimate_prev_week_day' => [
                        '*' => [

                        ]
                    ],
                ],

                'cmam' => [
                    'estimate',
                    'value',
                    'per_day' => [
                        '*' => [

                        ]
                    ],
                    'per_hour' => [
                        '*' => [

                        ]
                    ],
                ],
                'cm_ratio' => [
                    'value',
                    'last_30_minutes',
                    'last_hour',
                    'per_day' => [
                        '*' => [

                        ]
                    ],
                    'per_hour' => [
                        '*' => [

                        ]
                    ],
                ],
                'spend_ratio' => [
                    'value',
                    'last_30_minutes',
                    'last_hour',
                    'per_day' => [
                        '*' => [

                        ]
                    ],
                    'per_hour' => [
                        '*' => [

                        ]
                    ],
                ],
                'profit' => [
                    'estimate',
                    'value',
                ]

            ],
            'spend' => [
                'marketing_channels',
                'total',
            ],
            'conversion_indicators' => [
                "active_users",
                "add_to_cart_30min",
                "orders_30min",
                "total_cpo",
                "add_to_cart_45sek",
                "orders_45sek" => [
                    'value',
                    'change_percent'
                ]
            ],
            'warehouses' => [
                '*' => [
                    "id",
                    "name",
                    "in_packing",
                    "open",
                    "awaiting_stock",
                    "total",
                    "by_station" => [
                        '*' => [
                            'station',
                            'in_packing',
                            'open',
                            'awaiting_stock',
                            'total',
                            'by_market' => [
                                '*' => [
                                    'id',
                                    'market_name',
                                    'in_packing',
                                    'open',
                                    'awaiting_stock',
                                    'total',
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'year_to_day' => [
                'flat' => [
                    'value',
                    'estimate',
                    'change_in_percent'
                ],
                'index' => [
                    'value',
                    'estimate',
                    'change_in_percent'
                ],
                'spend' => [
                    'value',
                    'estimate',
                    'change_in_percent'
                ],
                'current_month' => [
                    'value',
                    'estimate',
                    'change_in_percent'
                ],
            ],
            'events' => [
                '*' => [
                    'date',
                    'days_before',
                    'title',
                ]
            ],

            'overview_table' => [
                '*' => [

                ]
            ],
            'indicators' => [
                '*' => [

                ]
            ]
        ]
    ];

    public function testAccess()
    {
        //$this->get(self::URL)->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testWithoutParams()
    {
        $this->get(self::URL, self::$headers)->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testWithParams()
    {
        $this->get(self::URL  . Arr::query(self::$filterArray), self::$headers)->assertOk();
    }

    public function testStructure()
    {
        $this->get(self::URL  . Arr::query(self::$filterArray), self::$headers)->assertJsonStructure(self::$structure)->assertOk();
    }
}
