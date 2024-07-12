<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\AccountEventProcessDispatcher;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api', methods: ['POST'])]
    public function index(
        Request $request,
        AccountEventProcessDispatcher $accountEventProcessDispatcher
    ): JsonResponse
    {
        $accountId = $request->get('account_id');
        $eventId = $request->get('event_id');

        if ( $accountId === null || $eventId === null )
        {
            return $this->json([
                'error' => 'Some of required parameters are missing. Required params: account_id, event_id',
            ], 400);
        }

        try
        {
            $accountEventProcessDispatcher->dispatchCommand($accountId, $eventId);

            return $this->json([
                'message' => 'Message has been dispatched to queue.',
            ]);
        }
        catch ( ExceptionInterface )
        {
            return $this->json([
                'error' => 'Error occurred while dispatching message.',
            ], 500);
        }
    }
}
