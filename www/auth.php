<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

const JWT_KEY = "vwmlEiJMlJmC1j2WgRRCzvs3Br9zJUvQczOpkZee5bMsRBr7ajHms7skfxm-6fdN3dwzNxb3zbX8zZxNrml9d_yxSLwhPW_XFpSPPd6hg1wVyUmx_zlGPytSI0sGapHctdNnyPeTibGOexrmAMPTiC7S6lAAig165lgYqNNznD45YvqrcX_9HZ3WFeT45eUY0HM8dNJ6jzDL0ET6GHsUfD33Gc6PnrhfqDKtT0fZnPDdjIUnT02v1D6DaDHhuTWJ1PxYfmfUbuF0GELB_nNUPRKzQHom6_3G4NHOiAst3qrFADFxD5Cmhgil4wfrF2xdUfWiswGnHH9KCpDXVorLww";
const JWT_COOKIE_NAME = "jwt_token";

foreach (glob("src/*") as $filename)
{
    include $filename;
}

function create_token(string $id): string {

    $now = new DateTime('now', new DateTimeZone('Asia/Seoul'));
    $exp_time = $now->add(new DateInterval('PT1H'));

    // issued at -> 토큰 발급 시간(타임스탬프)
    $iat = time();

    // expired at -> 토큰 만료 시간(타임스탬프)
    $exp = $exp_time->getTimestamp();

    $token = [
        "id" => $id,
        "iat" => $iat, 
        "exp" => $exp,
    ];

    JWT::$leeway = 5;
    $jwt = JWT::encode($token, JWT_KEY, 'HS256');

    return $jwt;
}

function decode_token(string $token): array {

    $decoded = (array) JWT::decode($token, new Key(JWT_KEY, 'HS256'));

    return $decoded;
}

function logout() {
    setcookie(JWT_COOKIE_NAME, "", time()-3600, "/", "localhost");
}

function login(PDO $conn, string $name, string $password): bool {

    $sql = "SELECT * FROM student WHERE name LIKE :name;";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->execute();

    $result = $stmt->fetch();

    if($result) {
        if(password_verify($password, $result['password'])) {
            $jwt = create_token($result['id']);
            setcookie(JWT_COOKIE_NAME, $jwt, 0, "/", "localhost");
            return true;
        } else {
            logout();
            return false;
        }
    } else {
        return false;
    }

}

function get_user_info(PDO $conn): array|null {

    $jwt = $_COOKIE[JWT_COOKIE_NAME] ?? null;

    if(!$jwt) return null;

    try {
        $decoded = decode_token($jwt);

        $sql = "SELECT * FROM student WHERE id LIKE :id;";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $decoded['id']);
        $stmt->execute();

        $result = $stmt->fetch();

        return $result;

    } catch (\Exception | \Throwable | \Error) {
        return null;
    }

}