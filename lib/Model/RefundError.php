<?php

/**
 * The MIT License
 *
 * Copyright (c) 2017 NBCO Yandex.Money LLC
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace YandexCheckout\Model;

use YandexCheckout\Common\AbstractObject;

/**
 * RefundError - Отказ в проведении возврата платежа
 * Код ошибки | Описание
 * --- | ---
 * authorization_rejected | Отказ в проведении возврата платежа по логике платежной системы (например лимиты или отказ антифрод-аналитики)
 * 
 * @property string $code Код ошибки
 * @property string $description Дополнительное текстовое пояснение ошибки
 */
class RefundError extends AbstractObject implements RefundErrorInterface
{
    /**
     * @var string Код ошибки
     */
    private $_code;

    /**
     * @var string Дополнительное текстовое пояснение ошибки
     */
    private $_description;

    /**
     * @return string Код ошибки
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @param string $value Код ошибки
     */
    public function setCode($value)
    {
        if ($value === null || $value === '') {
            throw new \InvalidArgumentException('Invalid value');
        }
        $this->_code = (string)$value;
    }

    /**
     * @return string Дополнительное текстовое пояснение ошибки
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * @param string $value Дополнительное текстовое пояснение ошибки
     */
    public function setDescription($value)
    {
        $this->_description = (string)$value;
    }
}
