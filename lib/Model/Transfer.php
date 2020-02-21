<?php

namespace YandexCheckout\Model;

use YandexCheckout\Common\AbstractObject;

/**
 * Класс объекта распределения денег в магазин
 */
class Transfer extends AbstractObject implements TransferInterface
{
    /**
     * @var string
     */
    protected $_account_id;

    /**
     * @var AmountInterface
     */
    protected $_amount;

    /**
     * Transfer constructor.
     *
     * @param string $accountId
     * @param AmountInterface $amount
     */
    public function __construct($accountId = null, AmountInterface $amount = null)
    {
        $this->_account_id = $accountId;
        $this->_amount = $amount;
    }

    /**
     * @inheritDoc
     */
    public function setAccountId($value)
    {
        $this->_account_id = $value;
    }

    /**
     * @inheritDoc
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
     * @inheritDoc
     */
    public function hasAmount()
    {
        return !empty($this->_amount);
    }

    /**
     * @inheritDoc
     */
    public function setAmount(AmountInterface $value)
    {
        $this->_amount = $value;
    }

    /**
     * @inheritDoc
     */
    public function getAccountId()
    {
        return $this->_account_id;
    }
}
