<?php

namespace Concrete\Package\Ht7C5WsTictactoe\Messages;

use \Concrete\Package\Ht7C5WsTictactoe\Messages\AbstractGameMessage;

/**
 * Game move message.
 */
class GameSettings extends AbstractGameMessage
{
    /**
     * @var string              The name attribute of the related setting.
     */
    protected $item;
    protected $value;

    public function __construct(array $msg)
    {
        $msg['action'] = 'game_settings';

        $this->item = empty($msg['item']) ? '' : $msg['item'];
        $this->value = empty($msg['value']) ? 0 : $msg['value'];

        parent::__construct($msg);
    }
    /**
     *
     * @return string
     */
    public function getItem()
    {
        return $this->item;
    }
    public function getValue()
    {
        return $this->value;
    }
    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'item' => $this->getItem(),
            'value' => $this->getValue(),
            ] + parent::jsonSerialize();
    }
}
