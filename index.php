<?php
    header('Content-type: application/json');
    require __DIR__ . '/vendor/autoload.php';
  
    use Aws\Polly\PollyClient;
    use Orutu\Polly\DotEnv;

    (new DotEnv(__DIR__ . '/.env'))->load();

    if (isset($_FILES['file'])) {
        $config = [
            'version' => 'latest',
            'region' =>  getenv('YOUR_AWS_REGION'),
            'credentials' => [
                'key' =>  getenv('ACCESS_KEY_ID'),
                'secret' => getenv('SECRET_ACCESS_KEY'),
                ]
            ];
            
        $client = new PollyClient($config);
            
        $engines = ['standard','neural'];

        foreach($engines as $engine) {
            try {
                $arrtemp = [
                    'Text' => file_get_contents($_FILES['file']['tmp_name']) ,
                    'OutputFormat' => 'mp3',
                    'VoiceId' => 'Kimberly',
                    'Engine' => 'standard',
                ]; 
            
                $result = $client->synthesizeSpeech($arrtemp);
            
            } catch (\Aws\Exception\AwsException $e) {
                echo json_encode([
                    'message' => "Sorry there was an error from aws",
                    'error' => $e->getMessage(),
                    'success' => false,
                    'status_code' => http_response_code(400)
                ]);
            }
            if (!empty($result['AudioStream'])) {
                $audio = $result['AudioStream'];
                file_put_contents('./audio/kimberly' . 'standard' . 'Php.mp3', $audio);
                echo json_encode([
                    'message' => "Txt file converted to audio successfully",
                    'success' => true,
                    'status_code' => http_response_code(200)
                ]);
            }
        }
    } else {
        echo json_encode([
            'message' => "No file selected",
            'success' => false,
            'status_code' => http_response_code(422)
        ]);
    }
?>