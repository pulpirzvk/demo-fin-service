<?php


namespace App\Enums;

use Konekt\Enum\Enum;

class OrderStageStatus extends Enum
{
    public const __DEFAULT = self::WAIT;

    public const WAIT = 10; // Заявка в базе, ожидаем события позволяющего отправить ее на биржу

    public const NEED_SEND = 20; // Заявка в базе, надо отправить в биржу
    public const SENDING = 30; // Заявка отправляется в биржу

    public const ON_MARKET = 40; // Размещена на бирже

    public const NEED_UPDATE = 50; // Заявка на бирже, нужда в обновление
    public const UPDATING = 60; // Заявка на бирже, обновляется

    public const NEED_CANCEL = 70; // Заявка на бирже, надо отменить
    public const CANCELING = 80; // Заявка на бирже, в процессе отмены
    public const CANCELLED = 90; // Заявка отменена (финальный статус)

    public const CLOSED = 100; // Заявка выполнена
    public const ERROR = 110; // Заявка закрыта из-за ошибки

    public static $labels = [
        self::WAIT => 'В ожидание',
        self::NEED_SEND => 'Ожидается отправка',
        self::SENDING => 'Отправляется',
        self::ON_MARKET => 'Размещена',
        self::NEED_UPDATE => 'Ожидается обновление',
        self::UPDATING => 'Обновляется',
        self::NEED_CANCEL => 'Ожидается отмена',
        self::CANCELING => 'Отменяется',
        self::CANCELLED => 'Отменена',
        self::CLOSED => 'Выполнена',
        self::ERROR => 'Закрыта из-за ошибки',
    ];

    /**
     * @inheritDoc
     */
    public function label()
    {
        return __(parent::label());
    }
}
