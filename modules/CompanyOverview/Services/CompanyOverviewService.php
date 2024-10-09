<?php


namespace Modules\CompanyOverview\Services;


class CompanyOverviewService extends BaseOverviewService
{
    public function modifyTotalsByMarket(array $data, array $fields): array
    {
        $result = [];
        foreach ($data as $row) {
            $result[$row['interval']] = $row;
        }

        if (! array_key_exists('previous', $result)) {
            $result['previous'] = $result['previous'] = array_combine(array_keys($result['current']), array_fill(0, count($result['current']), 0));
        }

        $datum = [];
        foreach ($fields as $field) {
            $sign = $result['current'][$field] > $result['previous'][$field]
                ? 1
                : ($result['current'][$field] < $result['previous'][$field] ? -1 : 0);
            $diff = abs($result['current'][$field] - $result['previous'][$field]);

            $diffPercentage = 1 - (
                $sign > 0
                    ? $result['previous'][$field] / $result['current'][$field]
                    : ($sign < 0 ? $result['current'][$field] / $result['previous'][$field] : 0)
                );

            $previous = $result['previous'][$field];
            $current = $result['current'][$field];
            $datum[$field] = [
                'sign' => $sign,
                'diff' => $diff,
                'diff_percentage' => $diffPercentage,
                'previous' => $previous,
                'current' => $current,
            ];
        }

        return $datum;
    }
}
