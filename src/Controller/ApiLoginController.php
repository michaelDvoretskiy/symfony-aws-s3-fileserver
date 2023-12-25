<?php

namespace App\Controller;

use App\Dto\UserRegisterDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Service\JsonResponsesService;
use App\Service\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApiLoginController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserService $userService,
        private JsonResponsesService $jsonRespService,
        private ContainerBagInterface $params
    ) {
    }

    #[Route('/api/register', methods: ['POST'], name: 'app_api_register')]
    public function register(UserRegisterDto $userData): Response
    {
        if ($this->params->get('user.allow_registration') != 1) {
            return $this->jsonRespService->generalError();
        }
        $this->userService->createUser($userData);       

        return $this->jsonRespService->success(['message' => 'Registered Successfully']);
    }

    #[Route('/api/login', methods: ['POST'], name: 'app_api_login')]
    public function index(#[CurrentUser] ?User $user): JsonResponse
    {
        if (null === $user) {
            return $this->jsonRespService->unauthorized();
        }

        $token = $this->userService->refreshUserToken($user);
            
        return $this->json([
            'user'  => $user->getUserIdentifier(),
            'token' => $token,
        ]);
    }
}
