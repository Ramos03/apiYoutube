<?php

    if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
        throw new \Exception('Gentileza instalar o composer na pasta "composer require google/apiclient:~2.0" in "' . __DIR__ .'"');
    }

    require_once __DIR__ . '/vendor/autoload.php';

    $htmlBody = <<<END
    <form method="GET">
    <div>
        Search Term: <input type="search" id="q" name="q" placeholder="Enter Search Term">
    </div>
    <div>
        Max Results: <input type="number" id="maxResults" name="maxResults" min="1" max="10" step="1" value="10">
    </div>
    <input type="submit" value="Procurar">
    </form>
    END;


    // Este código será executado se o usuário digitar uma consulta de pesquisa no formulário
    // Caso contrário, a página exibirá o formulário acima.
    if (isset($_GET['q']) && isset($_GET['maxResults'])) {
        /*
        * Set $chave_desenvolvedor to the "API key" value from the "Access" tab of the
        * Google API Console <https://console.developers.google.com/>
        * Please ensure that you have enabled the YouTube Data API for your project.
        */
        $chave_desenvolvedor = 'AIzaSyB8yLH4Sq3VP4sZQqU4Agz361sqk-LVp5k';

        $client = new Google_Client();
        $client->setDeveloperKey($chave_desenvolvedor);

        // Define an object that will be used to make all API requests.
        $youtube = new Google_Service_YouTube($client);

        $htmlBody = '';
        try {

            // Call the search.list method to retrieve results matching the specified
            // query term.
            $searchResponse = $youtube->search->listSearch('id,snippet', array(
            'q' => $_GET['q'],
            'maxResults' => $_GET['maxResults'],
            ));

            $videos = '';
            $channels = '';
            $playlists = '';

            // Add each result to the appropriate list, and then display the lists of
            // matching videos, channels, and playlists.
            foreach ($searchResponse['items'] as $searchResult) {
                switch ($searchResult['id']['kind']) {

                    case 'youtube#video':
                    $videos .= sprintf('<li>%s %s %s %s  %s</li> <br>',
                        'Data de publicação'. $searchResult['snippet']['publishedAt'] . "<br>",
                        'Id: '. $searchResult['id']['videoId']. "<br>",
                        'Titulo: ' .$searchResult['snippet']['title'] ."<br>",
                        'Descrição: '. $searchResult['snippet']['description'] . "<br>",
                        'Link: '. $searchResult['snippet']['thumbnails']['key']['url'] . "<br>"
                        );
                    break;

                    case 'youtube#channel':
                    $channels .= sprintf('<li>%s (%s)</li>',
                        $searchResult['snippet']['title'], $searchResult['id']['channelId']);
                    break;
                    case 'youtube#playlist':
                    $playlists .= sprintf('<li>%s (%s)</li>',
                        $searchResult['snippet']['title'], $searchResult['id']['playlistId']);
                    break;
                }
            }

            $htmlBody .= <<<END
            <h3>Videos</h3>
            <ul>$videos</ul>
            <h3>Channels</h3>
            <ul>$channels</ul>
            <h3>Playlists</h3>
            <ul>$playlists</ul>
        END;
        } catch (Google_Service_Exception $e) {
            $htmlBody .= sprintf('<p>Ocorreu um erro de serviço: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
        } catch (Google_Exception $e) {
            $htmlBody .= sprintf('<p>Ocorreu um erro de serviço: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
        }
        }
        ?>

    <!doctype html>
    <html>
    <head>
        <title>Pesquisa do youtube</title>
    </head>
    <body>
        <?=$htmlBody?>
    </body>
    </html>