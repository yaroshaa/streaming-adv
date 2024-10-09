<?php


namespace App\ClickHouse;


class FieldEngines
{
    public const MERGE_TREE = 'MergeTree';
    public const REPLACING_MERGE_TREE = 'ReplacingMergeTree';
    public const SUMMING_MERGE_TREE = 'SummingMergeTree';
    public const AGGREGATING_MERGE_TREE = 'AggregatingMergeTree';
    public const COLLAPSING_MERGE_TREE = 'CollapsingMergeTree';
    public const VERSIONED_MERGE_TREE = 'VersionedCollapsingMergeTree';
    public const GRAPHITE_MERGE_TREE = 'GraphiteMergeTree';
}
