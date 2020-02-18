<?php

namespace YandexCheckout\Model;

/**
 * Interface TransferInterface
 *
 * @package YandexCheckout\Model
 *
 * @property AmountInterface $amount
 * @property string $accountId
 */
interface TransferInterface
{
    /**
     * Устаналивает id магазина-получателя средств
     *
     * @param string $value
     *
     * @return void
     */
    public function setAccountId($value);

    /**
     * Возвращает id магазина-получателя средств
     *
     * @return string|null
     */
    public function getAccountId();

    /**
     * Возвращает сумму оплаты
     *
     * @return AmountInterface Сумма оплаты
     */
    public function getAmount();

    /**
     * Проверяет была ли установлена сумма оплаты
     *
     * @return bool True если сумма оплаты была установлена, false если нет
     */
    public function hasAmount();

    /**
     * Устанавливает сумму оплаты
     * @param AmountInterface $value Сумма оплаты
     */
    public function setAmount(AmountInterface $value);
}
