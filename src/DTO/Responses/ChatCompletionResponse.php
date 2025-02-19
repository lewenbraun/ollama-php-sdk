<?php

declare(strict_types=1);

namespace Lewenbraun\Ollama\DTO\Responses;

use Lewenbraun\Ollama\DTO\Chat\Message;

class ChatCompletionResponse
{
    /**
     * @param string $model
     * @param string $createdAt
     * @param Message $message
     * @param bool $done
     * @param string|null $doneReason
     * @param int|null $totalDuration
     * @param int|null $loadDuration
     * @param int|null $promptEvalCount
     * @param int|null $promptEvalDuration
     * @param int|null $evalCount
     * @param int|null $evalDuration
     */
    private function __construct(
        public readonly string      $model,
        public readonly string      $createdAt,
        public readonly Message   $message,
        public readonly bool        $done,
        public readonly ?string     $doneReason,
        public readonly ?int        $totalDuration,
        public readonly ?int        $loadDuration,
        public readonly ?int        $promptEvalCount,
        public readonly ?int        $promptEvalDuration,
        public readonly ?int        $evalCount,
        public readonly ?int        $evalDuration,
    ) {
    }

    /**
     * @param array $attributes
     * @return ChatCompletionResponse
     */
    public static function fromArray(array $attributes): ChatCompletionResponse
    {
        return new self(
            model: $attributes['model'],
            createdAt: $attributes['created_at'],
            message: Message::fromArray($attributes['message']),
            done: $attributes['done'],
            doneReason: $attributes['done_reason'] ?? null,
            totalDuration: $attributes['total_duration'] ?? null,
            loadDuration: $attributes['load_duration'] ?? null,
            promptEvalCount: $attributes['prompt_eval_count'] ?? null,
            promptEvalDuration: $attributes['prompt_eval_duration'] ?? null,
            evalCount: $attributes['eval_count'] ?? null,
            evalDuration: $attributes['eval_duration'] ?? null,
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'model' => $this->model,
            'created_at' => $this->createdAt,
            'message' => $this->message->toArray(),
            'done' => $this->done,
            'done_reason' => $this->doneReason,
            'total_duration' => $this->totalDuration,
            'load_duration' => $this->loadDuration,
            'prompt_eval_count' => $this->promptEvalCount,
            'prompt_eval_duration' => $this->promptEvalDuration,
            'eval_count' => $this->evalCount,
            'eval_duration' => $this->evalDuration,
        ];
    }
}
