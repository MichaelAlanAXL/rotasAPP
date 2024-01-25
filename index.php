<?php 

require_once("vendor/autoload.php");

use \Slim\Slim;
use \App\Page;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function(){

	$page = new Page();

	$page->setTpl("index");

});

$app->post('/', function () use ($app) {
	
	$profile = 'driving-car';  // ou outro perfil de roteamento que desejar
    $data = $app->request()->post();  
    $apiKey = 'Your_Key_Here';

    if ($data && isset($data['origem']) && isset($data['destino'])) {

        $origem = explode(',', $data['origem']);
        $destino = explode(',', $data['destino']);

        // Montar a URL da solicitação
        $url = "https://api.openrouteservice.org/v2/directions/$profile?api_key=$apiKey";

        // Configurar opções para a solicitação cURL
        $ch = curl_init();

        $postData = [
        	'coordinates' => [$origem, $destino],
        	'format' => 'geojson',
        	//'options' => ['radiuses' => [350.0, 350.0]] // Ajuste de raio
        ];

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);

        // Enviar a solicitação e obter a resposta
        $apiResponse = curl_exec($ch);

        if (curl_errno($ch)) {
 		   echo 'Erro na solicitação cURL: ' . curl_error($ch);
 		}

        curl_close($ch);

        // Enviar resposta JSON ao cliente
        $app->response()->header('Content-Type', 'application/json');
        $app->response()->body($apiResponse);

    } else {

        $app->response()->status(400);
        $app->response()->body(json_encode(['error' => 'Dados inválidos']));
    }    

});

$app->run();



 ?>