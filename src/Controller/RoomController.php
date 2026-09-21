<?php
declare(strict_types=1);
namespace App\Controller;

use App\Dto\CreateRoomRequest;
use App\Repository\RoomRepository;
use App\Service\RoomService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

final class RoomController extends AbstractController
{
    function __construct(
        private RoomService $roomService,
        private RoomRepository $roomRepository)
    {
    }

    #[Route('/api/rooms', name: 'app_rooms_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $rooms = $this->roomRepository->findAll();
        return $this->json($rooms);
    }

    #[Route('/api/room', name: 'app_rooms_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateRoomRequest $request): JsonResponse
    {
      $room =$this->roomService->create($request);
      return $this->json($room,Response::HTTP_CREATED);
    }

}
