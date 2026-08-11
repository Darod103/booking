<?php

namespace App\Controller;

use App\Dto\CreateBookingRequest;
use App\Exception\BookingConflictException;
use App\Exception\CapacityExceededException;
use App\Exception\InvalidBookingPeriodException;
use App\Exception\RoomNotFoundException;
use App\Service\BookingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class BookingController extends AbstractController
{
    public function __construct(
        private readonly BookingService $bookingService
    )
    {

    }

    /**
     * @throws BookingConflictException
     * @throws CapacityExceededException
     * @throws InvalidBookingPeriodException
     * @throws RoomNotFoundException
     */
    #[Route('api/bookings', name: 'app_booking_create',methods: ['POST'])]
    public function create( #[MapRequestPayload] CreateBookingRequest $bookingCreateRequest): JsonResponse
    {
        $booking = $this->bookingService->createBooking($bookingCreateRequest);
        return $this->json($booking,Response::HTTP_CREATED);
    }
}
