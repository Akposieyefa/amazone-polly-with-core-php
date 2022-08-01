<form method="post" enctype="multipart/form-data">
    <p><input type="file" name="file" /></p>
    <button type="submit" name="submit">Submit</button>
</form>

<?php
    require __DIR__ . '/vendor/autoload.php';
  
    use Aws\Polly\PollyClient;
    use Orutu\Polly\DotEnv;

    (new DotEnv(__DIR__ . '/.env'))->load();

    if ( isset($_POST['submit']) ) {

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
                    echo "Error: {$e->getMessage()}";
                }
                if (!empty($result['AudioStream'])) {
                    $audio = $result['AudioStream'];
                    file_put_contents('./audio/kimberly' . 'standard' . 'Php.mp3', $audio);
                }
            }
    }
?>