<?php

namespace Modules\MarketingOverview\Services\Widgets;

use App\Entities\Market;
use App\Entities\MarketingChannel;
use Illuminate\Support\Arr;

class OverviewTotalsTableWidget
{
    const ROW_REVENUE = 'revenue.total';
    const ROW_PACKING_COST = 'packing_cost';
    const ROW_CM = 'contribution_margin.total';
    const ROW_CMAM = 'cmam.total';

    const ROW_EST_CMAM = 'cmam.estimate';
    const ROW_EST_REVENUE = 'revenue.estimate';
    const ROW_EST_PROFIT = 'profit.estimate';

    const ROW_AOV = 'aov';
    const ROW_ORDERS = 'orders.total';
    const ROW_NEW = 'orders.estimate';
    const ROW_CM_RATION = 'spend_ratio.total';
    const ROW_SPEND_RATIO = 'contribution_margin_ratio.total';

    const ROW_MARKETING_EXPENSE = 'marketing.expense'; // virtual

    const FORMAT_PERCENT = 'percent';
    const FORMAT_INTEGER = 'integer';
    const FORMAT_DECIMAL = 'decimal';

    private array $percentageFields = [
        self::ROW_NEW,
        self::ROW_CM_RATION,
        self::ROW_SPEND_RATIO,
    ];

    private array $fields = [
        self::ROW_REVENUE,
        self::ROW_PACKING_COST,
        self::ROW_CM,

        self::ROW_CMAM,

        self::ROW_EST_CMAM,
        self::ROW_EST_REVENUE,
        self::ROW_EST_PROFIT,

        self::ROW_AOV,
        self::ROW_ORDERS,
        self::ROW_NEW,
        self::ROW_CM_RATION,
        self::ROW_SPEND_RATIO
    ];

    private array $bolderFields = [
        self::ROW_REVENUE,
        self::ROW_CM,
        self::ROW_CMAM,
        self::ROW_EST_PROFIT,
        self::ROW_EST_REVENUE,
        self::ROW_EST_CMAM
    ];

    private array $separatedFields = [
        self::ROW_REVENUE,
        self::ROW_PACKING_COST,
        self::ROW_CM,
        self::ROW_CMAM,
        self::ROW_EST_PROFIT,
    ];

    private array $header = [
        [
            'prop' => 'title',
            'title' => '',
            'image' => '',
        ],
        [
            'prop' => 'total',
            'title' => '',
            'image' => '/images/icons/netthandelsgruppen.png',
        ]
    ];

    private array $rows;
    private array $overviewByStores;

    /** @var MarketingChannel[] */
    private array $marketingChannels;

    /** @var Market[]  */
    private array $markets;

    private array $spendByMarkets;

    public function __construct(
        array $markets,
        array $marketingChannels,
        array $overviewByStores,
        array $spendByMarkets
    )
    {
        $this->markets = $markets;
        $this->marketingChannels = $marketingChannels;
        $this->overviewByStores = $this->prepareOverviewByStore($overviewByStores);
        $this->spendByMarkets = $this->prepareSpendByMarkets($spendByMarkets);

        $this->process();
    }

    /**
     * @return array|string[][]
     */
    public function getHeader(): array
    {
        return $this->header;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    private function prepareOverviewByStore(array $overviewByStore): array
    {
        $rows = [];
        foreach ($overviewByStore as $store) {
            $rows[$store['id']] = $store;
        }

        return $rows;
    }

    private function prepareSpendByMarkets(array $spendByMarkets): array
    {
        $rows = [];
        /// Fill array zeros
        foreach ($this->marketingChannels as $channelId => $channel) {
            foreach ($this->markets as $marketId => $market) {
                $rows = Arr::add($rows, $channelId . '.' . $marketId, 0);
            }
        }

        foreach ($spendByMarkets as $stat) {
            $key = $stat['marketing_chanel_id'] . '.' . $stat['market_id'];
            $rows[$stat['marketing_chanel_id']][$stat['market_id']] = Arr::get($rows, $key, 0.00) + $stat['spend'];
        }

        return $rows;
    }

    private function process()
    {
        $data = [];

        foreach ($this->markets as $market) {
            $store = Arr::get($this->overviewByStores, $market->getRemoteId(), []);
            foreach ($this->fields as $field) {
                $data[$field][] = Arr::get($store, $field, 0);
            }
            $this->header[] = [
                'prop' => 'market_' . $market->getId(),
                'title' => $market->getName(),
                'image' => $market->getIconLink(),
            ];
        }
        $rows = [];
        foreach ($data as $field => $row) {
            $rows[] = Arr::flatten([
                $this->labelByField($field),
                $this->formatNumber(array_sum($row), $field),
                array_map(fn($value) => $this->formatNumber($value, $field), $row),
                in_array($field, $this->bolderFields),
                in_array($field, $this->separatedFields)
            ]);

            // After contribution margin we insert ads marketing statistic
            if ($field === self::ROW_CM) {
                foreach ($this->marketingChannels as $channel) {
                    $rows[] = Arr::flatten([
                        $channel->getName(),
                        $this->formatNumber(array_sum($this->spendByMarkets[$channel->getId()]), self::ROW_MARKETING_EXPENSE),
                        array_map(fn($value) => $this->formatNumber($value, self::ROW_MARKETING_EXPENSE), $this->spendByMarkets[$channel->getId()]),
                        false,
                        false
                    ]);
                }
            }
        }

        $columns = array_merge(Arr::pluck($this->header, 'prop'), ['separator', 'bolder']);
        $this->rows = array_map(fn($value) => array_combine($columns, $value), $rows);
    }

    private function labelByField(string $field): string
    {
        $labels = [
            self::ROW_REVENUE => 'Revenue',
            self::ROW_PACKING_COST => 'Packing cost',
            self::ROW_CM => 'CM',
            self::ROW_CMAM => 'CMAM',

            self::ROW_EST_REVENUE => 'Est. Rev.',
            self::ROW_EST_CMAM => 'Est. CMAM',
            self::ROW_EST_PROFIT => 'Est. Profit',

            self::ROW_AOV => 'Orders',
            self::ROW_ORDERS => 'A.O.V.',
            self::ROW_NEW => '% new',
            self::ROW_CM_RATION => 'CM ratio',
            self::ROW_SPEND_RATIO => 'spend ratio',
        ];

        return $labels[$field] ?? $field;
    }

    private function formatNumber($number, string $field): string
    {
        if (in_array($field, $this->percentageFields)) {
            return number_format($number, 2) . '%';
        }

        // @todo add in future if needed formatter for decimals

        return number_format($number, 0);
    }
}
