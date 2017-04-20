<?php
/**
 * Setup the JWT token route.
 * @author Destin Moulton
 * Modified From: 
 *    https://github.com/tuupola/slim-api-skeleton/blob/master/routes/token.php
 */
use Firebase\JWT\JWT;
use Tuupola\Base62;

$app->post("/token", function ($request, $response, $arguments) {
    
    $json = $request->getBody();
    $data = json_decode($json, true);
    
    if(!isset($data['auth_id'])){
        return $response->withStatus(401);
    }

    $settings = $this->get('settings');
    
    if($data['auth_id'] !== $settings['jwt']['auth_id']){
        return $response->withStatus(401);
    }
    $now = new DateTime();
    $future = new DateTime("now +2 years");
    $php_auth_user = $request->getServerParam("PHP_AUTH_USER", "unknown");
    $jti = (new Base62)->encode(random_bytes(16));
    $payload = [
        "iat" => $now->getTimeStamp(),
        "exp" => $future->getTimeStamp(),
        "jti" => $jti,
        "sub" => $php_auth_user
    ];
    $secret = $settings['jwt']['secret'];
    $token = JWT::encode($payload, $secret, "HS256");
    $data["token"] = $token;
    $data["expires"] = $future->getTimeStamp();
    return $response->withStatus(201)
        ->withHeader("Content-Type", "application/json")
        ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
});