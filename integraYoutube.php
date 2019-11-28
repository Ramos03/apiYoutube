<?php
    /*
        ** Este código foi desenvolvido com base na documentação do DATA API do youtube
        ** Programador: Vinicius De Sordi Ramos 
        ** Data de criação 26/11/19
    */

    //Verifica se o autoload do composer está na pasta
    if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
        throw new \Exception('Gentileza instalar o composer na pasta "composer require google/apiclient:~2.0" in "' . __DIR__ .'"');
    }

    // importando autoload
    require_once __DIR__ . '/vendor/autoload.php';

    // Este código será executado se o usuário digitar uma consulta de pesquisa no formulário
    if (isset($_GET['q']) && isset($_GET['maxResults'])) {
        
        
        //Chave da API do youtube
        $chave_desenvolvedor = 'AIzaSyB8yLH4Sq3VP4sZQqU4Agz361sqk-LVp5k';

        
        $client = new Google_Client(); // Chama API do google
        $client->setDeveloperKey($chave_desenvolvedor); // Passa a chave de desenvolvedor para autorizar

        $youtube = new Google_Service_YouTube($client); // chama API do youtube
        
        $html = ''; // Serve para algo
        try{
            
            // Chame o método search.list para recuperar resultados correspondentes ao especificado
            $procura = $youtube->search->listSearch('id,snippet', array(
                'q' => $_GET['q'],
                'maxResults' => $_GET['maxResults'],
            ));

            
            $videos = ''; // Declaração variavel para Videos
            $canal = ''; // Declaração variavel para canal
            $playlists = ''; // Declaração variavel para playlist
            
            // Verifica se irá retornar vazio a chamada 
            if(empty($procura['items'])){
                //throw new \Exception('Não foi encontrado nenhuma informação com os parametros passados.' . __DIR__ .'"');
                echo "<h3> Não foi encontrado nenhuma informação com os parametros passados </h3>";
                echo "<button class='btn btn-primary' type='submit'><a href='index.php'> Voltar</a></button>";
            }
            else{
                // Adicione cada resultado à lista apropriada e exiba as listas de vídeos, canais e playlists 
                foreach ($procura['items'] as $result) {
                    switch ($result['id']['kind']) {
                        
                        //Em caso de vídeo irá mostrar de acordo com a quantidade
                        case 'youtube#video':
                        $videos .= sprintf('<li>%s %s %s %s  %s</li> <br>',
                            'Data de publicação: '. date('d-m-Y h:i:s', strtotime($result['snippet']['publishedAt'])) . '<br>',
                            'Id: '. $result['id']['videoId']. '<br>',
                            'Titulo: ' .$result['snippet']['title'] .'<br>',
                            'Descrição: '. $result['snippet']['description'] . '<br>',
                            'Miniatura: '. $result['snippet']['thumbnails']['default']['url'] . '<br>'
                            );
                        break;

                        //Em caso de canal irá mostrar de acordo com a quantidade
                        case 'youtube#channel':
                            $canal .= sprintf('<li>%s %s %s </li>',
                            'Data de publicação: '. date('d-m-Y h:i:s', strtotime($result['snippet']['publishedAt'])) . '<br>',
                            'Id: '. $result['id']['channelId']. '<br>',
                            'Titulo: ' .$result['snippet']['title'] .'<br>',
                            'Descrição: '. $result['snippet']['description'] . '<br>',);
                        break;

                        //Em caso de playlist irá mostrar de acordo com a quantidade
                        case 'youtube#playlist':
                            $playlists .= sprintf('<li>%s (%s)</li>',
                            'Id: '. $result['id']['playlistId'] . '<br>',
                            'Titulo: '. $result['snippet']['title'] . '<br>');
                        break;
                    }
                }
                echo "<body style='font-family: Arial'>";
                // Mostra os vídeos
                echo "<h3 >Videos</h3>";
                echo "<ul>". $videos . "</ul>";
        
                //Mostra os canais
                echo "<h3>Canais</h3>";
                echo "<ul>". $canal . "</ul>";
                
                //Mostra as playlists
                echo "<h3> Playlists</h3>";
                echo "<ul>". $playlists . "</ul>";

                echo "<button class='btn btn-primary' type='submit'><a href='index.php'> Voltar</a></button>";
                
                echo "</body>";
            }
        }
        catch (\Google_Service_Exception $e) {
            $html .= $e->getMessage();
            //echo $html . "<br/>";
            //Converte o JSON retornado da API com erro
            $converte = json_decode($html);
            
            //Verifica se a variavel $converte está diferente de null
            if(isset($converte->error)){
                
                //Verifica se é o erro 400 - Pedido ruim
                if($converte->error->code == 400){
                    echo "<h3>Ocorreu um erro!</h3>";
                    echo "<h3>O servidor não entendeu a requisição pois está com uma sintaxe inválida! </h3>";
                    echo "<button class='btn btn-primary' type='submit'><a href='index.php'> Voltar</a></button>";
                }

                //Verifica se é o erro 401 - API key invalida
                else if($converte->error->code == 401){
                    echo "<h3>Ocorreu um erro!</h3>";
                    echo "<h3>API Key inválida</h3>";
                    echo "<button class='btn btn-primary' type='submit'><a href='index.php'> Voltar</a></button>";
                }
                
                // Verifica se o erro 404 - Não localizou 
                else if($converte->error->code == 404){
                    echo "<h3>Ocorreu um erro!</h3>";
                    echo "<h3>O servidor não pode encontrar o recurso solicitado.</h3>";
                    echo "<button class='btn btn-primary' type='submit'><a href='index.php'> Voltar</a></button>";
                }

                // Verifica se o erro 500 - erro interno
                else if($converte->error->code == 500){
                    echo "<h3>Ocorreu um erro!</h3>";
                    echo "<h3>Erro interno</h3>";
                    echo "<button class='btn btn-primary' type='submit'><a href='index.php'> Voltar</a></button>";
                }

                // Verifica o erro 503 - Serviço indisponivel
                else if($converte->error->code == 503){
                    echo "<h3>Ocorreu um erro!</h3>";
                    echo "<h3>O servidor não está pronto para manipular a requisição.</h3>";
                    echo "<button class='btn btn-primary' type='submit'><a href='index.php'> Voltar</a></button>";
                }

            }
        } 
        catch (\Google_Exception $e) {
            // Recebe a mensagem de erro da exceção
            $html .= $e->getMessage();
            
            // Converte o JSON recepcionado
            $converte = json_decode($html);

            //Verifica se a variavel $converte está diferente de null
            if(isset($converte->error)){
                // Mostra a mensagem do código
                echo $converte->error->message;

                // mostra o código do erro
                echo $convete->error->code;
            }
        }
    }
?>