<?php

namespace Modules\MarketingOverview\Services\Calculate;

class ConversationIndicatorService
{
    private array $expenseOverPeriodData;
    private array $conversationIndicatorsDaily;
    private array $conversationIndicatorsPer30Minutes;
    private array $conversationIndicatorsPer45Seconds;

    public function __construct(
        array $expenseOverPeriodData,
        array $conversationIndicatorsDaily,
        array $conversationIndicatorsPer30Minutes,
        array $conversationIndicatorsPer45Seconds
    )
    {
        $this->expenseOverPeriodData = $expenseOverPeriodData;
        $this->conversationIndicatorsDaily = $conversationIndicatorsDaily;
        $this->conversationIndicatorsPer30Minutes = $conversationIndicatorsPer30Minutes;
        $this->conversationIndicatorsPer45Seconds = $conversationIndicatorsPer45Seconds;
    }

    public function getStatistic(): array
    {
        $totalCPO = $this->formatCPO($this->expenseOverPeriodData);

        $activeUsers = $this->getActiveUsers($this->conversationIndicatorsDaily);
        $dataEvery30minutes = $this->formatConversationIndicator($this->conversationIndicatorsPer30Minutes);
        $dataEvery45seconds = $this->formatConversationIndicator($this->conversationIndicatorsPer45Seconds);
        $lastValueEvery30minutes = $this->getLastValue($dataEvery30minutes);
        $lastValueEvery45seconds = $this->getLastValue($dataEvery45seconds);

        return [
            'active_users' => round($activeUsers,2),
            'add_to_cart_30min' => round($lastValueEvery30minutes['cart'],2),
            'orders_30min' => round((float) $lastValueEvery30minutes['orders'],2),
            'total_cpo' => round($totalCPO,2),
            'add_to_cart_45sek' => round($lastValueEvery45seconds['cart'],2),
            'orders_45sek' => [
                'value' => round((float) $lastValueEvery45seconds['orders'], 2),
                'change_percent' => $this->getChangeValueInPercent($dataEvery45seconds['orders'])
            ]
        ];
    }

    private function formatCPO(array $data): float
    {
        $marketingExpense = $data;
        $dayGranularityData = $this->conversationIndicatorsDaily;

        $totalMarketingExpenseForLastDay = 0;
        if (!empty($marketingExpense)) {
            $totalMarketingExpenseForLastDay = $marketingExpense[count($marketingExpense) - 1]['marketing_expense'];
        }

        $orders = array_values(array_filter($dayGranularityData, function ($item) {
            return $item['entity'] === 'orders';
        }));

        $totalOrdersForLastDay = 0;
        if (!empty($orders)) {
            $totalOrdersForLastDay = $orders[count($orders) - 1]['value'];
        }


        $totalCPO = 0;

        if ($totalOrdersForLastDay > 0) {
            $totalCPO = $totalMarketingExpenseForLastDay / $totalOrdersForLastDay;
        }

        return $totalCPO;
    }

    private function getActiveUsers(array $dayGranularityData = []): float
    {
        $activeUsers = array_filter($dayGranularityData, function ($item) {
            return $item['entity'] === 'active_users';
        });

        return array_sum(array_column($activeUsers, 'value'));
    }

    private function formatConversationIndicator(array $data): array
    {
        $cart = array_values(array_filter($data, function ($item) {
            return $item['entity'] === 'cart_actions';
        }));

        $orders = array_values(array_filter($data, function ($item) {
            return $item['entity'] === 'orders';
        }));

        return [
            'cart' => $cart,
            'orders' => $orders
        ];
    }

    private function getLastValue(array $data): array
    {
        if (!empty($data['cart'])) {
            $cart = (int)$data['cart'][count($data['cart']) - 1]['value'];
        } else {
            $cart = 0;
        }

        if (!empty($data['orders'])) {
            $orders = (int)$data['orders'][count($data['orders']) - 1]['value'];
        } else {
            $orders = 0;
        }

        return [
            'cart' => $cart,
            'orders' => $orders
        ];
    }

    private function getChangeValueInPercent(array $data): float
    {
        if (count($data) > 1) {
            $current = $data[count($data) - 1]['value'];
            $prev = $data[count($data) - 2]['value'];

            return (($current - $prev) * ($prev / 100)) * 100;
        }

        return 0;
    }
}
