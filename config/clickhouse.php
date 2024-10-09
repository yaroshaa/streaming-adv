<?php

use App\ClickHouse\Converter;
use App\ClickHouse\DataTransformers\OrderTransformer;
use App\ClickHouse\FieldEngines;
use App\ClickHouse\FieldsTypes;
use App\ClickHouse\Layout;
use App\ClickHouse\Models\Address;
use App\ClickHouse\Models\Customer;
use App\ClickHouse\Models\Feedback;
use App\ClickHouse\Models\FifteenMinTotals;
use App\ClickHouse\Models\Market;
use App\ClickHouse\Models\Order;
use App\ClickHouse\Models\OrderProduct;
use App\ClickHouse\Models\OrderStatus;
use App\ClickHouse\Models\OrderStatusHistory;
use App\ClickHouse\Models\Source;
use App\ClickHouse\Models\ActiveUser;
use App\ClickHouse\Models\CartAction;
use App\ClickHouse\Models\MarketingExpense;
use App\ClickHouse\Models\WarehouseStatistic;
use App\ClickHouse\Models\UserHistoryStatistic;
use App\ClickHouse\Models\AnalyticsSites;
use App\ClickHouse\Models\AnalyticsEvents;
use App\ClickHouse\Models\AnalyticsEventProperties;
use App\ClickHouse\Repositories\BaseFeedbackRepository;
use App\ClickHouse\Repositories\BaseFifteenMinTotalsRepository;
use App\ClickHouse\Repositories\BaseOrderProductRepository;
use App\ClickHouse\Repositories\BaseOrderRepository;
use App\ClickHouse\Repositories\BaseActiveUserRepository;
use App\ClickHouse\Repositories\BaseCartActionRepository;
use App\ClickHouse\Repositories\BaseMarketingExpenseRepository;
use App\ClickHouse\Repositories\BaseUserHistoryStatisticRepository;
use App\ClickHouse\Repositories\BaseWarehouseStatisticRepository;
use Illuminate\Support\Arr;

return Arr::mergeConfigFromModules('clickhouse', [
    'host' => env('CLICKHOUSE_HOST', '127.0.0.1'),
    'port' => env('CLICKHOUSE_PORT', '8123'),
    'username' => env('CLICKHOUSE_USERNAME'),
    'password' => env('CLICKHOUSE_PASSWORD'),
    'dbname' => env('CLICKHOUSE_DBNAME', 'default'),
    'migrations_path' => base_path(env('CLICKHOUSE_MIGRATIONS_PATH')),
    'dictionaries' => [
        'orders' => [
            'name' => 'orders',
            'fields' => [
                'remote_id' => FieldsTypes::STRING,
                'address_id' => FieldsTypes::UINT64,
                'created_at' => FieldsTypes::DATETIME,
                'order_status_id' => FieldsTypes::UINT64,
                'market_id' => FieldsTypes::UINT64,
                'packing_cost' => FieldsTypes::FLOAT32
            ],
            'pk' => 'remote_id',
            'layout' => Layout::COMPLEX_HASHED,
            'lifetime' => 2,
            'source' => [
                'mysql' => [
                    'port' => env('DB_PORT'),
                    'host' => env('DB_HOST'),
                    'user' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'db' => env('DB_DATABASE'),
                    'table' => 'orders',
                    'invalidate_query' => 'SQL_QUERY'
                ]
            ]
        ],
        'order_statuses' => [
            'name' => 'order_statuses',
            'fields' => [
                'remote_id' => FieldsTypes::UINT64,
                'name' => FieldsTypes::STRING,
                'color' => FieldsTypes::STRING,
            ],
            'pk' => 'remote_id',
            'layout' => Layout::HASHED,
            'lifetime' => 300,
            'source' => [
                'mysql' => [
                    'port' => env('DB_PORT'),
                    'host' => env('DB_HOST'),
                    'user' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'db' => env('DB_DATABASE'),
                    'table' => 'order_statuses',
                    'invalidate_query' => 'SQL_QUERY'
                ]
            ],
        ],
        'product_variants' => [
            'name' => 'product_variants',
            'fields' => [
                'remote_id' => FieldsTypes::STRING,
                'weight' => FieldsTypes::FLOAT32,
                'name' => FieldsTypes::STRING,
                'link' => FieldsTypes::STRING,
                'image_link' => FieldsTypes::STRING
            ],
            'pk' => 'remote_id',
            'layout' => Layout::COMPLEX_DIRECT,
            'source' => [
                'mysql' => [
                    'port' => env('DB_PORT'),
                    'host' => env('DB_HOST'),
                    'user' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'db' => env('DB_DATABASE'),
                    'table' => 'product_variants',
                    'invalidate_query' => 'SQL_QUERY'
                ]
            ]
        ],
        'markets' => [
            'name' => 'markets',
            'fields' => [
                'remote_id' => FieldsTypes::UINT64,
                'name' => FieldsTypes::STRING,
                'icon_link' => FieldsTypes::STRING,
                'color' => FieldsTypes::STRING,
            ],
            'pk' => 'remote_id',
            'layout' => Layout::HASHED,
            'lifetime' => 300,
            'source' => [
                'mysql' => [
                    'port' => env('DB_PORT'),
                    'host' => env('DB_HOST'),
                    'user' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'db' => env('DB_DATABASE'),
                    'table' => 'markets',
                    'invalidate_query' => 'SQL_QUERY'
                ]
            ]
        ],
        'sources' => [
            'name' => 'sources',
            'fields' => [
                'remote_id' => FieldsTypes::UINT64,
                'name' => FieldsTypes::STRING,
                'icon_link' => FieldsTypes::STRING,
            ],
            'pk' => 'remote_id',
            'layout' => Layout::HASHED,
            'lifetime' => 300,
            'source' => [
                'mysql' => [
                    'port' => env('DB_PORT'),
                    'host' => env('DB_HOST'),
                    'user' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'db' => env('DB_DATABASE'),
                    'table' => 'sources',
                    'invalidate_query' => 'SQL_QUERY'
                ]
            ]
        ],
        'customers' => [
            'name' => 'customers',
            'fields' => [
                'remote_id' => FieldsTypes::UINT64,
                'name' => FieldsTypes::STRING,
                'created_at' => FieldsTypes::DATETIME,
            ],
            'pk' => 'remote_id',
            'layout' => Layout::HASHED,
            'lifetime' => 300,
            'source' => [
                'mysql' => [
                    'port' => env('DB_PORT'),
                    'host' => env('DB_HOST'),
                    'user' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'db' => env('DB_DATABASE'),
                    'table' => 'customers',
                    'invalidate_query' => 'SQL_QUERY'
                ]
            ],
        ],
        'currencies' => [
            'name' => 'currencies',
            'fields' => [
                'id' => FieldsTypes::UINT64,
                'code' => FieldsTypes::STRING,
                'name' => FieldsTypes::STRING,
            ],
            'pk' => 'id',
            'layout' => Layout::HASHED,
            'lifetime' => 300,
            'source' => [
                'mysql' => [
                    'port' => env('DB_PORT'),
                    'host' => env('DB_HOST'),
                    'user' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'db' => env('DB_DATABASE'),
                    'table' => 'currencies',
                    'invalidate_query' => 'SQL_QUERY'
                ]
            ],
        ],
        'exchange_rates' => [
            'name' => 'exchange_rates',
            'fields' => [
                'from_id' => FieldsTypes::UINT64,
                'to_id' => FieldsTypes::UINT64,
                'created_at' => FieldsTypes::DATE,
                'rate' => FieldsTypes::FLOAT32,
            ],
            'pk' => 'from_id, to_id, created_at',
            'layout' => Layout::COMPLEX_HASHED,
            'lifetime' => 300,
            'source' => [
                'mysql' => [
                    'port' => env('DB_PORT'),
                    'host' => env('DB_HOST'),
                    'user' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'db' => env('DB_DATABASE'),
                    'table' => 'exchange_rates',
                    'invalidate_query' => 'SQL_QUERY'
                ]
            ],
        ],
        'addresses' => [
            'name' => 'addresses',
            'fields' => [
                'id' => FieldsTypes::UINT64,
                'address' => FieldsTypes::STRING,
                'lat' => FieldsTypes::FLOAT32,
                'lng' => FieldsTypes::FLOAT32,
            ],
            'pk' => 'id',
            'layout' => Layout::HASHED,
            'lifetime' => 2,
            'source' => [
                'mysql' => [
                    'port' => env('DB_PORT'),
                    'host' => env('DB_HOST'),
                    'user' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'db' => env('DB_DATABASE'),
                    'table' => 'addresses',
                    'invalidate_query' => 'SQL_QUERY'
                ]
            ]
        ],
        'marketing_channels' => [
            'name' => 'marketing_channels',
            'fields' => [
                'id' => FieldsTypes::UINT64,
                'name' => FieldsTypes::STRING,
                'icon_link' => FieldsTypes::STRING
            ],
            'pk' => 'id',
            'layout' => Layout::HASHED,
            'lifetime' => 2,
            'source' => [
                'mysql' => [
                    'port' => env('DB_PORT'),
                    'host' => env('DB_HOST'),
                    'user' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'db' => env('DB_DATABASE'),
                    'table' => 'marketing_channels',
                    'invalidate_query' => 'SQL_QUERY'
                ]
            ]
        ],
        'warehouses' => [
            'name' => 'warehouses',
            'fields' => [
                'id' => FieldsTypes::UINT64,
                'name' => FieldsTypes::STRING
            ],
            'pk' => 'id',
            'layout' => Layout::HASHED,
            'lifetime' => 2,
            'source' => [
                'mysql' => [
                    'port' => env('DB_PORT'),
                    'host' => env('DB_HOST'),
                    'user' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'db' => env('DB_DATABASE'),
                    'table' => 'warehouses',
                    'invalidate_query' => 'SQL_QUERY'
                ]
            ]
        ],
        'holiday_events' => [
            'name' => 'holiday_events',
            'fields' => [
                'id' => FieldsTypes::UINT64,
                'title' => FieldsTypes::STRING,
                'date' => FieldsTypes::DATE
            ],
            'pk' => 'id',
            'layout' => Layout::HASHED,
            'lifetime' => 2,
            'source' => [
                'mysql' => [
                    'port' => env('DB_PORT'),
                    'host' => env('DB_HOST'),
                    'user' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'db' => env('DB_DATABASE'),
                    'table' => 'holiday_events',
                    'invalidate_query' => 'SQL_QUERY'
                ]
            ]
        ],
        'analytics_sites' => [
            'name' => 'analytics_sites',
            'fields' => [
                'id' => FieldsTypes::UINT64,
                'name' => FieldsTypes::STRING,
                'key' => FieldsTypes::STRING
            ],
            'pk' => 'id',
            'layout' => Layout::HASHED,
            'lifetime' => 2,
            'source' => [
                'mysql' => [
                    'port' => env('DB_PORT'),
                    'host' => env('DB_HOST'),
                    'user' => env('DB_USERNAME'),
                    'password' => env('DB_PASSWORD'),
                    'db' => env('DB_DATABASE'),
                    'table' => 'analytics_sites',
                    'invalidate_query' => 'SQL_QUERY'
                ]
            ]
        ]
    ],
    'mapping' => [
        Order::class => [
            'virtual' => true,
            'repository' => BaseOrderRepository::class,
            'transformer' => OrderTransformer::class,
            'fields' => [
                'id' => [
                    'column' => 'order_id',
                    'type' => FieldsTypes::STRING,
                ],
                'customer' => [
                    'nested' => Customer::class,
                ],
                'market' => [
                    'nested' => Market::class
                ],
                'status' => [
                    'nested' => OrderStatus::class
                ],
                'currency' => [
                    'column' => 'currency_id',
                    'type' => FieldsTypes::UINT64,
                    'fillable' => true,
                ],
                'address' => [
                    'nested' => Address::class,
                ],
                'updatedAt' => [
                    'column' => 'updated_at',
                    'type' => FieldsTypes::DATETIME,
                    'fillable' => false,
                    'converter' => Converter::TO_DATE,
                ],
            ]
        ],
        OrderProduct::class => [
            'table' => 'orders_products',
            'engine' => FieldEngines::MERGE_TREE,
            'repository' => BaseOrderProductRepository::class,
            'order_by' => ['updated_at'],
            'fields' => [
                'id' => [
                    'column' => 'product_variant_id',
                    'type' => FieldsTypes::STRING,
                ],
                'orderId' => [
                    'column' => 'order_id',
                    'type' => FieldsTypes::STRING,
                    'fillable' => false
                ],
                'customer' => [
                    'nested' => Customer::class,
                    'fillable' => false,
                ],
                'market' => [
                    'nested' => Market::class,
                    'fillable' => false,
                ],
                'currency' => [
                    'column' => 'currency_id',
                    'type' => FieldsTypes::UINT64,
                    'fillable' => false,
                ],
                'name' => [
                    'column' => 'product_name',
                    'manual' => true,
                ],
                'link' => [
                    'column' => 'product_link',
                    'manual' => true,
                ],
                'imageLink' => [
                    'column' => 'product_image_link',
                    'manual' => true,
                ],
                'price' => [
                    'column' => 'product_price',
                    'type' => FieldsTypes::FLOAT32,
                ],
                'profit' => [
                    'column' => 'product_profit',
                    'type' => FieldsTypes::FLOAT32,
                ],
                'discount' => [
                    'column' => 'product_discount',
                    'type' => FieldsTypes::FLOAT32,
                ],
                'qty' => [
                    'column' => 'product_qty',
                    'type' => FieldsTypes::UINT16,
                ],
                'weight' => [
                    'column' => 'product_weight',
                    'type' => FieldsTypes::FLOAT32,
                ],
                'updatedAt' => [
                    'column' => 'updated_at',
                    'type' => FieldsTypes::DATETIME,
                    'fillable' => false,
                    'converter' => Converter::TO_DATE,
                ],
            ]
        ],
        Feedback::class => [
            'table' => 'feedbacks',
            'repository' => BaseFeedbackRepository::class,
            'engine' => FieldEngines::MERGE_TREE,
            'order_by' => ['created_at'],
            'fields' => [
                'uniqueId' => [
                    'column' => 'unique_id',
                    'type' => FieldsTypes::STRING,
                    'fillable' => false,
                ],
                'source' => [
                    'nested' => Source::class,
                    'fillable' => false,
                ],
                'market' => [
                    'nested' => Market::class,
                    'fillable' => false,
                ],
                'name' => [
                    'column' => 'name',
                    'type' => FieldsTypes::STRING,
                    'fillable' => false,
                ],
                'message' => [
                    'column' => 'message',
                    'type' => FieldsTypes::STRING,
                    'fillable' => false,
                ],
                'url' => [
                    'column' => 'url',
                    'type' => FieldsTypes::STRING,
                    'fillable' => false,
                ],
                'createdAt' => [
                    'column' => 'created_at',
                    'type' => FieldsTypes::DATETIME,
                    'fillable' => false,
                    'converter' => Converter::TO_DATE,
                ],
            ]
        ],
        Source::class => [
            'virtual' => true,
            'fields' => [
                'id' => [
                    'column' => 'source_id',
                    'type' => FieldsTypes::UINT64,
                ],
                'name' => [
                    'column' => 'source_name',
                    'manual' => true
                ],
                'iconLink' => [
                    'column' => 'source_icon_link',
                    'manual' => true
                ]
            ]
        ],
        OrderStatusHistory::class => [
            'table' => 'order_status_history',
            'engine' => FieldEngines::MERGE_TREE,
            'order_by' => ['updated_at'],
            'fields' => [
                'orderId' => [
                    'column' => 'order_id',
                    'type' => FieldsTypes::STRING,
                ],
                'statusBefore' => [
                    'column' => 'status_before',
                    'type' => FieldsTypes::UINT8,
                ],
                'statusAfter' => [
                    'column' => 'status_after',
                    'type' => FieldsTypes::UINT8,
                ],
                'updatedAt' => [
                    'column' => 'updated_at',
                    'type' => FieldsTypes::DATETIME,
                ]
            ],
        ],
        Market::class => [
            'virtual' => true,
            'fields' => [
                'id' => [
                    'column' => 'market_id',
                    'type' => FieldsTypes::UINT64,
                ],
                'name' => [
                    'column' => 'market_name',
                    'manual' => true
                ],
                'iconLink' => [
                    'column' => 'market_icon_link',
                    'manual' => true
                ]
            ]
        ],
        Customer::class => [
            'virtual' => true,
            'fields' => [
                'id' => [
                    'column' => 'customer_id',
                    'type' => FieldsTypes::UINT64,
                ],
                'name' => [
                    'column' => 'customer_name',
                    'manual' => true
                ],
                'createdAt' => [
                    'column' => 'customer_created_at',
                    'manual' => true,
                    'converter' => Converter::TO_DATE,
                ]
            ]
        ],
        OrderStatus::class => [
            'parent' => Order::class,
            'virtual' => true,
            'fields' => [
                'id' => [
                    'column' => 'status_id',
                    'type' => FieldsTypes::UINT64,
                ],
                'name' => [
                    'column' => 'status_name',
                    'manual' => true
                ],
                'color' => [
                    'column' => 'status_color',
                    'manual' => true
                ],
            ]
        ],
        Address::class => [
            'virtual' => true,
            'fields' => [
                'id' => [
                    'column' => 'address_id',
                    'type' => FieldsTypes::UINT64,
                ],
                'address' => [
                    'column' => 'address',
                    'manual' => true
                ],
                'lat' => [
                    'column' => 'address_lat',
                    'manual' => true
                ],
                'lng' => [
                    'column' => 'address_lng',
                    'manual' => true
                ],
            ]
        ],
        FifteenMinTotals::class => [
            'table' => 'order_stat',
            'repository' => BaseFifteenMinTotalsRepository::class,
            'virtual' => true,
            'fields' => [
                'profit' => [
                    'column' => 'profit',
                ],
                'total' => [
                    'column' => 'total',
                ],
                'ordersCnt' => [
                    'column' => 'orders_count',
                ],
                'date' => [
                    'column' => 'date',
                    'converter' => Converter::TO_NULLABLE_DATE,
                    'nullable' => true
                ]
            ]
        ],
        ActiveUser::class => [
            'table' => 'active_users',
            'virtual' => true,
            'repository' => BaseActiveUserRepository::class,
            'fields' => [
                'ip' => [
                    'column' => 'ip',
                    'type' => FieldsTypes::STRING,
                ],
                'market' => [
                    'nested' => Market::class
                ],
                'status' => [
                    'column' => 'status',
                    'type' => FieldsTypes::UINT8,
                ],
                'createdAt' => [
                    'column' => 'created_at',
                    'type' => FieldsTypes::DATETIME,
                    'fillable' => false,
                    'converter' => Converter::TO_DATE,
                ],
            ]
        ],
        UserHistoryStatistic::class => [
            'table' => 'user_history_statistics',
            'virtual' => true,
            'repository' => BaseUserHistoryStatisticRepository::class,
            'fields' => [
                'ip' => [
                    'column' => 'ip',
                    'type' => FieldsTypes::STRING,
                ],
                'market' => [
                    'nested' => Market::class
                ],
                'status' => [
                    'column' => 'status',
                    'type' => FieldsTypes::UINT8,
                ],
                'createdAt' => [
                    'column' => 'created_at',
                    'type' => FieldsTypes::DATETIME,
                    'fillable' => false,
                    'converter' => Converter::TO_DATE,
                ],
            ]
        ],
        CartAction::class => [
            'table' => 'cart_actions',
            'virtual' => true,
            'repository' => BaseCartActionRepository::class,
            'fields' => [
                'ip' => [
                    'column' => 'ip',
                    'type' => FieldsTypes::STRING,
                ],
                'market' => [
                    'nested' => Market::class
                ],
                'status' => [
                    'column' => 'status',
                    'type' => FieldsTypes::UINT8,
                ],
                'createdAt' => [
                    'column' => 'created_at',
                    'type' => FieldsTypes::DATETIME,
                    'fillable' => false,
                    'converter' => Converter::TO_DATE,
                ],
            ]
        ],
        WarehouseStatistic::class => [
            'table' => 'warehouse_statistic',
            'virtual' => true,
            'repository' => BaseWarehouseStatisticRepository::class,
            'fields' => [
                'warehouse_id' => [
                    'column' => 'warehouse_id',
                    'type' => FieldsTypes::UINT64,
                ],
                'inPacking' => [
                    'column' => 'in_packing',
                    'type' => FieldsTypes::UINT16,
                ],
                'open' => [
                    'column' => 'open',
                    'type' => FieldsTypes::UINT16,
                ],
                'awaitingStock' => [
                    'column' => 'awaiting_stock',
                    'type' => FieldsTypes::UINT16,
                ],
                'station' => [
                    'column' => 'station',
                    'type' => FieldsTypes::UINT16,
                ],
                'market_id' => [
                    'column' => 'market_id',
                    'type' => FieldsTypes::UINT16,
                ],
            ]
        ],
        MarketingExpense::class => [
            'table' => 'marketing_expense',
            'virtual' => true,
            'repository' => BaseMarketingExpenseRepository::class,
            'fields' => [
                'marketing_channel_id' => [
                    'column' => 'marketing_channel_id',
                    'type' => FieldsTypes::UINT64,
                ],
                'market' => [
                    'nested' => Market::class
                ],
                'currency' => [
                    'column' => 'currency_id',
                    'type' => FieldsTypes::UINT64,
                    'fillable' => true,
                ],
                'value' => [
                    'column' => 'value',
                    'type' => FieldsTypes::FLOAT32,
                ],
                'createdAt' => [
                    'column' => 'created_at',
                    'type' => FieldsTypes::DATETIME,
                    'fillable' => false,
                    'converter' => Converter::TO_DATE,
                ],
            ]
        ],
        AnalyticsSites::class => [
            'virtual' => true,
            'fields' => [
                'id' => [
                    'column' => 'site_id',
                    'type' => FieldsTypes::UINT64,
                ],
                'name' => [
                    'column' => 'name',
                    'manual' => true
                ],
                'key' => [
                    'column' => 'key',
                    'manual' => true
                ]
            ]
        ],
        AnalyticsEvents::class => [
            'table' => 'analytics_events',
            'fields' => [
                'eventId' => [
                    'column' => 'event_id',
                    'type' => FieldsTypes::STRING,
                ],
                'siteId' => [
                    'column' => 'site_id',
                    'type' => FieldsTypes::UINT64,
                ],
                'name' => [
                    'column' => 'name',
                    'manual' => true
                ],
                'createdAt' => [
                    'column' => 'created_at',
                    'type' => FieldsTypes::DATETIME,
                    'fillable' => false,
                    'converter' => Converter::TO_DATE,
                ],
            ]
        ],
        AnalyticsEventProperties::class => [
            'table' => 'analytics_event_properties',
            'fields' => [
                'propertyId' => [
                    'column' => 'property_id',
                    'type' => FieldsTypes::STRING,
                ],
                'eventId' => [
                    'column' => 'event_id',
                    'type' => FieldsTypes::STRING,
                ],
                'name' => [
                    'column' => 'name',
                    'manual' => true
                ],
                'value' => [
                    'column' => 'value',
                    'manual' => true
                ],
            ]
        ]
    ]
]);
