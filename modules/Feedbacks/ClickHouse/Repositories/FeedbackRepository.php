<?php

namespace Modules\Feedbacks\ClickHouse\Repositories;

use Modules\Feedbacks\ClickHouse\QuickQueries\SearchQuery;
use App\ClickHouse\Repositories\BaseFeedbackRepository;
use Carbon\Carbon;

class FeedbackRepository extends BaseFeedbackRepository
{
    /**
     * @param int[] $markets
     * @param int[] $sources
     * @param string[] $words
     * @param Carbon $from
     * @return array
     */
    public function search(array $markets, array $sources, array $words, Carbon $from): array
    {
        return $this->quickQuery(new SearchQuery($markets, $sources, $words, $from));
    }
}
