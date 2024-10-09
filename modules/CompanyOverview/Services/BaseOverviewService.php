<?php


namespace Modules\CompanyOverview\Services;


class BaseOverviewService {

    public function modifyTotals(array $data): array
    {
        $result = [];

        foreach ($data as $item) {
            $result[$item['interval']] = $item;
        }

        return $result;
    }
}
