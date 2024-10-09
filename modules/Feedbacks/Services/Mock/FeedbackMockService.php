<?php

namespace Modules\Feedbacks\Services\Mock;

use App\ClickHouse\ClickHouseException;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Modules\Feedbacks\ClickHouse\Services\FeedbackService;

/**
 * Class FeedbackMockService
 * @package Modules\Feedbacks\Services\Mock
 */
class FeedbackMockService
{
    /**
     * @var string[]
     */
    private static array $names = [
        'Siya Butler',
        'Liyah Swift',
        'Augustus Cox',
        'Malcolm Parks',
        'Leonardo Short',
        'Huda Mustafa',
        'Mildred Moss',
        'Lilith Macdonald',
        'Huw Hibbert',
        'Charlton Wilcox',
        'John Doe'
    ];

    /**
     * @var string[]
     */
    private static array $messages = [
        "Monster Lavkarbo Zero Drops - Banana - FEMPAKNING",
        "Monster Lavkarbo Protein Chips BBQ 5x50g - FEMPAKNING",
        "Monster Lavkarbo Protein Chips Sea Salt & Pepper 5x50g - FEMPAKNING",
        "Monster Lavkarbo Protein Chips Vinegar & Salt 5x50g - FEMPAKNING",
        "Now Foods L-Arginin 100x500mg",
        "Now Foods Hyaluronic Acid 50mg + MSM 60 caps",
        "Now Foods 5-HTP 120x100mg",
        "Blender Bottle ProStak Black 650ml",
        "NJIE ProPud Milkshake - Mint Chocolate 8x330ml Limited Edition!",
        "Monster Lavkarbo Protein Chips Vinegar & Salt 50g",
        "Monster Lavkarbo Protein Chips Sea Salt & Pepper 50g",
        "Monster Lavkarbo Protein Chips BBQ 50g",
        "Monster TagaSØTT 500g",
        "Monster Keto Protein Cookie 12x33g",
        "Monster Keto Protein Cookie 33g",
        "Monster Canned Chicken 5x155g - FEMPAKNING",
        "Gavepose",
        "Monster Lavkarbo Zero Drops 30ml - Hazelnut - NY FORBEDRET OPPSKRIFT!",
        "Monster Zero Calorie Syrup - Chocolate & Caramel 434g",
        "Monster Zero Calorie Syrup - Sweet Peanut Butter 434g",
        "Monster Lavkarbo Zero Drops - Cookie - NY FORBEDRET OPPSKRIFT!",
        "Monster Lavkarbo Zero Drops - Hazelnut Chocolate - NY FORBEDRET OPPSKRIFT!",
        "Monster Lavkarbo Zero Drops - White Chocolate - NY FORBEDRET OPPSKRIFT!",
        "Monster Lavkarbo Zero Drops - Apple - NY FORBEDRET OPPSKRIFT!",
        "Monster Lavkarbo Zero Drops - Peach - NY FORBEDRET OPPSKRIFT!",
        "Monster Lavkarbo Zero Drops - Coconut - NY FORBEDRET OPPSKRIFT!",
        "Monster Lavkarbo Zero Drops - Strawberry - NY FORBEDRET OPPSKRIFT!",
        "Monster Lavkarbo Zero Drops - Caramel - NY FORBEDRET OPPSKRIFT!",
        "Monster Lavkarbo Zero Drops - Raspberry - NY FORBEDRET OPPSKRIFT!",
        "Monster Lavkarbo Zero Drops - Maple Syrup - NY FORBEDRET OPPSKRIFT!",
        "Monster Lavkarbo Zero Drops - Banana - NY FORBEDRET OPPSKRIFT!",
        "SunnRask Ingefær Ginger Shot",
        "SunnRask Gurkemeie Shot",
        "Better Bodies Highbridge leggings Black Camo",
        "Swanson - Thyroid Essentials - 90 caps",
        "Swanson - Garcinia Cambogia - 5:1 (80mg) - 60 caps",
        "Swanson - Juniper berries - 520mg - 100 caps",
        "Swanson Albion Chelated Zinc - 30mg - 90caps",
        "EFX Lean Fix - 120 caps",
        "EFX Glutazorb - 120 caps",
        "NJIE ProPud Milkshake - Mint Chocolate 330ml Limited Edition!",
        "Monster Lavkarbo Croissant 50g",
        "SunnRask Tørkede Svisker 500g",
        "SunnRask Tørket Fiken 500g",
        "SunnRask Tørkede Dadler 500g",
        "TITAN LIFE Suspension Trainer Pro - Black",
        "TITAN LIFE Training Mat 180x60x1.5cm - Grey",
    ];

    /**
     * @throws ClickHouseException
     */
    public function execute()
    {
        /** @var FeedbackService $feedbackService */
        $feedbackService = resolve(FeedbackService::class);
        $feedbackService->saveAndBroadcast($this->takeRandomData());
    }

    /**
     * @return array
     */
    protected function takeRandomData(): array
    {
        return [
            'name' => Arr::random(self::$names),
            'message' => Arr::random(self::$messages),
            'source_id' => rand(1, 4),
            'market_id' => rand(1, 4),
            'created_at' => Carbon::now()->toDateTimeLocalString(),
            'url' => rand(1, 2) === 1 ? 'https://google.com/' : ''
        ];
    }
}
