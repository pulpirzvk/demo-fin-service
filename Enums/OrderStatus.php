<?php


namespace App\Enums;

use Konekt\Enum\Enum;

class OrderStatus extends Enum
{
    public const __DEFAULT = self::NEED_SEND;

    public const NEED_BUY_ACTIVE = 10; // Сделка в базе, необходимо купить актив
    public const BUYING_ACTIVE = 20; // Идет покупка актива
    public const NEED_SEND = 30; // Надо отправить все ТФП в биржу
    public const SENDING = 40; // Сделка отправляется в биржу
    public const ON_MARKET = 50; // Сделка размещена на бирже
    public const NEED_UPDATE = 60; // Сделка на бирже, нужда в обновление
    public const UPDATING = 70; // Сделка на бирже, обновляется
    public const NEED_CANCEL = 80; // Сделка на бирже, надо отменить все ТФП без финального статуса
    public const CANCELING = 90; // Сделка на бирже, в процессе отмены
    public const CANCELLED = 100; // Сделка отменена (финальный статус)
    public const CLOSED_SUCCESS = 110; // Сделка выполнена и закрыта с закрытием всех ТФП (финальный статус)
    public const CLOSED_STOP_LOSS = 120; // Сделка закрыта по стоп-лоссу (финальный статус)
    public const CLOSED_ERROR = 130; // Сделка закрыта из-за ошибки (финальный статус)

    public static $labels = [
        self::NEED_BUY_ACTIVE => 'Ожидается покупка актива',
        self::BUYING_ACTIVE => 'Покупается актив',
        self::NEED_SEND => 'Ожидается отправка заявок',
        self::SENDING => 'Отправляются заявки',
        self::ON_MARKET => 'Размещена',
        self::NEED_UPDATE => 'Ожидается обновление',
        self::UPDATING => 'Обновляется',
        self::NEED_CANCEL => 'Ожидается отмена',
        self::CANCELING => 'Отменяется',
        self::CANCELLED => 'Отменена',
        self::CLOSED_SUCCESS => 'Выполнена',
        self::CLOSED_STOP_LOSS => 'Закрыта по стоп-лоссу',
        self::CLOSED_ERROR => 'Закрыта из-за ошибки',
    ];

    /**
     * Проверка является ли статус финальным
     *
     * @return bool
     */
    public function isFinal(): bool
    {
        $finalStatuses = [
            self::CLOSED_ERROR,
            self::CLOSED_SUCCESS,
            self::CLOSED_STOP_LOSS,
            self::CANCELLED,
            self::NEED_CANCEL,
            self::CANCELING,
        ];

        return in_array($this->value(), $finalStatuses, true);
    }

    /**
     * Проверка является ли статус не финальным
     *
     * @return bool
     */
    public function isNotFinal(): bool
    {
        return !$this->isFinal();
    }

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __(parent::label());
    }

}
