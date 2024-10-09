<?php

namespace Modules\Feedbacks\ClickHouse\Services;

use DateTime;
use Exception;
use App\ClickHouse\ClickHouseException;
use Illuminate\Support\Facades\Validator;
use Modules\Feedbacks\ClickHouse\Repositories\FeedbackRepository;
use Modules\Feedbacks\Events\FeedbackAdded;
use Modules\Feedbacks\Http\Resources\FeedbackResource;

class FeedbackService
{
    /**
     * @var FeedbackRepository
     */
    private FeedbackRepository $feedbackRepository;

    /**
     * BaseFeedbackService constructor.
     * @param FeedbackRepository $feedbackRepository
     */
    public function __construct(FeedbackRepository $feedbackRepository)
    {
        $this->feedbackRepository = $feedbackRepository;
    }

    /**
     * @param array $data
     * @return array
     * @throws ClickHouseException
     * @throws Exception
     */
    public function save(array $data): array
    {
        $this->validate($data);
        return $this->insert($data);
    }

    /**
     * @param array $rowFeedback
     * @throws Exception
     */
    private function validate(array $rowFeedback): void
    {
        $validation = [
            'name' => 'string',
            'message' => 'required|string',
            'created_at' => 'required|date',
            'market_id' => 'required|exists:App\Entities\Market,remoteId',
            'source_id' => 'required|exists:App\Entities\Source,remoteId',
            'url' => 'string',
        ];

        $validator = Validator::make($rowFeedback, $validation, [
            'market_id.exists' => ':attribute is invalid `:input`',
            'source_id.exists' => ':attribute is invalid `:input`',
        ]);

        if ($validator->fails()) {
            $validator->errors()->all();
            $errorMessage = implode('; ', $validator->errors()->all());
            throw new Exception('Validation failed. ' . $errorMessage);
        }
    }

    /**
     * @param array $feedback
     * @return array
     * @throws ClickHouseException
     * @throws Exception
     */
    private function insert(array $feedback): array
    {
        $feedback = [
            'name' => $feedback['name'] ?? '',
            'message' => $feedback['message'],
            'market_id' => $feedback['market_id'],
            'source_id' => $feedback['source_id'],
            'created_at' => new DateTime($feedback['created_at']),
            'url' => $feedback['url'] ?? ''
        ];

        $uniqueId = $this->getUniqueId($feedback);
        if ($this->feedbackRepository->exist($uniqueId)) {
            throw new Exception(sprintf('Message exist: "%s" ', serialize($feedback)));
        }

        $feedback['unique_id'] = $uniqueId;

        $this->feedbackRepository->insert($feedback);

        return $feedback;
    }

    /**
     * @param array $data
     * @return string
     */
    private function getUniqueId(array $data): string
    {
        return hash('sha512', serialize($data));
    }

    /**
     * @throws ClickHouseException
     */
    public function saveAndBroadcast(array $data)
    {
        FeedbackAdded::dispatch(new FeedbackResource($this->save($data)));
    }
}
