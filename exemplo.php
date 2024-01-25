<?php // Rota para calcular a rota usando a API da OpenRouteService
$app->post('/calcular-rota', function () use ($app) {
    $data = $app->request->post();
    $apiKey = 'SUA_CHAVE_DE_API';

    if ($data && isset($data['coordinates']) && isset($data['profile'])) {
        $coordinates = $data['coordinates'];
        $profile = $data['profile'];

        // Montar a URL da solicitação
        $url = "https://api.openrouteservice.org/v2/directions/$profile?api_key=$apiKey";

        // Configurar opções para a solicitação cURL
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['coordinates' => $coordinates]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);

        // Enviar a solicitação e obter a resposta
        $apiResponse = curl_exec($ch);
        curl_close($ch);

        // Enviar resposta JSON ao cliente
        $app->response->headers->set('Content-Type', 'application/json');
        $app->response->setBody($apiResponse);
    } else {
        // Se os dados não forem válidos, retornar erro
        $app->response->setStatus(400);
        $app->response->setBody(json_encode(['error' => 'Dados inválidos']));
    }
});

?>