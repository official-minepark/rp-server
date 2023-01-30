<?php

namespace minepark\formsapi\forms;

use Generator;
use minepark\formsapi\responses\interfaces\IResponse;
use pocketmine\form\Form;
use pocketmine\player\Player;
use pocketmine\promise\Promise;
use SOFe\AwaitGenerator\Await;

/**
 * @template TResponse
 */
abstract class BaseForm implements Form
{
    private mixed $resolve = null;

    public function __construct(
        private string $title
    )
    {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    abstract protected function getFormType(): string;

    abstract protected function serializeFormData(): array;

    final public function jsonSerialize(): array
    {
        return array_merge([
            "type" => $this->getFormType(),
            "title" => $this->getTitle()
        ], $this->serializeFormData());
    }

    /**
     * @param Player $player
     * @return Generator<mixed, mixed, mixed, TResponse>
     */
    final public function sendToPlayerAsync(Player $player): Generator
    {
        if (!is_null($this->resolve)) {
            throw new \RuntimeException("Same form object can't be sent more than once");
        }

        $player->sendForm($this);

        return yield from Await::promise(function($resolve, $reject) {
            $this->resolve = $resolve;
        });
    }

    final protected function submitResponse(IResponse $response)
    {
        if (is_null($this->resolve)) {
            throw new \RuntimeException("Submitting response to form that is not yet sent is unexpected behaviour");
        }

        ($this->resolve)($response);
    }
}