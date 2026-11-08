<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Room;
use App\Enum\BookingStatus;
use App\ValueObject\TimeRange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Types\UuidType;
/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

//    /**
//     * @return Booking[] Returns an array of Booking objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Booking
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function existsBlockingOverlap(Room $room, TimeRange $range): bool
    {
        $count = $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->andWhere('b.room = :room')
            ->andWhere('b.status IN (:blockingStatuses)')
            ->andWhere('b.period.range.startAt < :end')
            ->andWhere('b.period.range.endAt > :start')
            ->setParameter('room', $room->getId(), UuidType::NAME)
            ->setParameter('blockingStatuses', BookingStatus::blockingStatuses())
            ->setParameter('start', $range->startAt)
            ->setParameter('end', $range->endAt)
            ->getQuery()
            ->getSingleScalarResult();

        return $count > 0;
    }
}
