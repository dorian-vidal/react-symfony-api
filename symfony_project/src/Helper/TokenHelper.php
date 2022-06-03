<?php

namespace App\Helper;

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use \Firebase\JWT\ExpiredException;
use App\Entity\User;

use App\Controller\CookiesController;


class TokenHelper {

  public static function generateToken(User $user, String $appSecret) {
    return JWT::encode([
        'exp' => time() + 10,
        'userId' => $user->getId(),
        'username' => $user->getEmail()
    ], $appSecret, 'HS256');
  }

  public static function checkToken(String $token, String $appSecret) {
    try {
      $oldToken= JWT::decode($token, new Key($appSecret, 'HS256'));
      dd($oldToken);
    } catch (ExpiredException $th) {
      $oldJwt = json_decode(base64_decode(explode('.', $token)[1]));
      // dd($oldJwt->username);
      $user = new User();

        
        $user->setEmail($oldJwt->username);
        $user->setId($oldJwt->id);
        $user->setPassword(
          password_hash($password, PASSWORD_BCRYPT)
            // $this->passwordEncoder->hashPassword($user, $password)
        );
      

        // generer token
        $token = $this->generateToken($user, $this->getParameter('app.appSecret'));

        // set cookies  
        CookiesController::setCookies($request, $token, $user->getEmail());
        
      dd($token);
    }
    
  }

}
