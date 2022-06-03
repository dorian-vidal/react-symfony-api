<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Helper\TokenHelper;
use App\Controller\CookiesController;


class TestController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    
    #[Route('api/login', name: 'app_login', methods: 'POST')]
    public function login(Request $request) : JsonResponse
    {

        $inputs = json_decode(file_get_contents('php://input'));
      
        $email = $inputs->email;
        $password = $inputs->password;

        if($email == "" && $password == "") {
          $data = [
            'message' => 'les champs email et password sont obligatoires',
            "status" => "error"
          ];
          return $this->json($data); 
        }

        $user = $this->userRepository->findOneBy([
            'email' => $email,
        ]);

        if (is_null($user)) {
            $data = [
              'message' => 'User not exists',
              "status" => "error"
            ];
            return $this->json($data); 
        }

        // generer token
        $token = TokenHelper::generateToken($user, $this->getParameter('app.appSecret'));

        // set cookies  
        CookiesController::setCookies($request, $token, $email);

        $data = [
          'status' => 'success',
          'email' => $user->getEmail(),
          'token' => $token
        ];

        return $this->json($data); 
    }

    #[Route('api/register', name: 'app_register', methods: 'POST')]
    public function register(Request $request)
    {

      $inputs = json_decode(file_get_contents('php://input'));
        $email = $inputs->email;
        $password = $inputs->password;

        if($email == "" && $password == "") {
          $data = [
              'message' => 'les champs email et password sont obligatoires',
              'status' => 'error'
          ];
          return $this->json($data);
        }

        $user = $this->userRepository->findOneBy([
            'email' => $email,
        ]);

        if (!is_null($user)) {
            $data = [
              'message' => 'User already exists',
              'status' => 'error'
            ];
            return $this->json($data);
        }
        

        $user = new User();

        
        $user->setEmail($email);
        $user->setRoles(array(
          "ROLE_USER"
        ));
        $user->setPassword(
          password_hash($password, PASSWORD_BCRYPT)
            // $this->passwordEncoder->hashPassword($user, $password)
        );
        

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // generer token
        $token = TokenHelper::generateToken($user, $this->getParameter('app.appSecret'));

        // set cookies  
        CookiesController::setCookies($request, $token, $user->getEmail());
        
        $newUser = [
          "id" => $user->getId(),
          'status' => 'success',
          'username' => $user->getEmail(),
          'token' => $token
        ];
        return $this->json($newUser);
    }

    #[Route('api/refresh-token', name: 'app_refresh_token', methods: 'POST')]
    public function refreshToken(Request $request)
    {
      $token = $request->cookies->get('token');
      // verifie si valid
      TokenHelper::checkToken($token, $this->getParameter('app.appSecret'));

        // generer token
        $token = TokenHelper::generateToken($user, $this->getParameter('app.appSecret'));

        // set cookies  
        CookiesController::setCookies($request, $token, $user->getEmail());
        
        $newUser = [
          "id" => $user->getId(),
          'status' => 'success',
          'username' => $user->getEmail(),
          'token' => $token
        ];
        return $this->json($newUser);
    }
}