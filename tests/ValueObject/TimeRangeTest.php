<?php
declare(strict_types=1);

namespace App\Tests\ValueObject;

use App\Exception\InvalidBookingPeriodException;
use App\ValueObject\BookingPeriod;
use App\ValueObject\TimeRange;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use App\Exception\InvalidTimeRangeException;


class TimeRangeTest extends TestCase
{
    /**
     * @throws InvalidTimeRangeException
     */
    #[TestDox('Отклоняет интервал, если начало позже окончания')]
    public function testRejectsStartAfterEnd(): void
    {
        $start = new \DateTimeImmutable('2023-01-01 10:00:00');
        $end = new \DateTimeImmutable('2023-01-01 09:00:00');
        $this->expectException(InvalidTimeRangeException::class);
        new TimeRange($start, $end);
    }

    #[TestDox('Отклоняет интервал, если начало равно окончанию')]
    public function testRejectsStartEqualToEnd(): void
    {
        $start = new \DateTimeImmutable('2023-01-01 10:00:00');
        $end = new \DateTimeImmutable('2023-01-01 10:00:00');
        $this->expectException(InvalidTimeRangeException::class);
        new TimeRange($start, $end);
    }

    #[TestDox('Частичное пересечение')]
    public function testDetectsPartialOverlap(): void
    {
        $first = new TimeRange(new \DateTimeImmutable('2023-01-01 10:00:00'), new \DateTimeImmutable('2023-01-01 11:00:00'));
        $second = new TimeRange(new \DateTimeImmutable('2023-01-01 10:30:00'), new \DateTimeImmutable('2023-01-01 11:30:00'));
        $this->assertTrue($first->overlaps($second));
        $this->assertTrue($second->overlaps($first));

    }

    #[TestDox('Полное пересечение')]
    public function testDetectsFullOverlap(): void
    {
        $first = new TimeRange(new \DateTimeImmutable('2023-01-01 10:00:00'), new \DateTimeImmutable('2023-01-01 11:00:00'));
        $second = new TimeRange(new \DateTimeImmutable('2023-01-01 10:00:00'), new \DateTimeImmutable('2023-01-01 11:00:00'));
        $this->assertTrue($first->overlaps($second));
        $this->assertTrue($second->overlaps($first));

    }

    #[TestDox('Не пересечение')]
    public function testDetectsNoOverlap(): void
    {
        $first = new TimeRange(new \DateTimeImmutable('2023-01-01 10:00:00'), new \DateTimeImmutable('2023-01-01 11:30:00'));
        $second = new TimeRange(new \DateTimeImmutable('2023-01-01 11:30:00'), new \DateTimeImmutable('2023-01-01 12:00:00'));
        $this->assertFalse($first->overlaps($second));
        $this->assertFalse($second->overlaps($first));

    }


    #[TestDox('Период длительностью ровно 8 часов')]
    public function testDetectsEightHourPeriod(): void
    {
        $start = new \DateTimeImmutable('2023-01-01 10:00:00');
        $end = new \DateTimeImmutable('2023-01-01 18:00:00');
        $timeRange = new TimeRange($start, $end);
        $period = BookingPeriod::fromRange($timeRange);
        $this->assertSame($timeRange, $period->range);

    }

    #[TestDox('Период длительностью больше 1 минуту 8 часов')]
    public function testDetectsLessThanEightHourPeriod(): void
    {
        $start = new \DateTimeImmutable('2023-01-01 10:00:00');
        $end = new \DateTimeImmutable('2023-01-01 18:01:00');
        $timeRange = new TimeRange($start, $end);
        $this->expectException(InvalidBookingPeriodException::class);
        BookingPeriod::fromRange($timeRange);
    }

}
