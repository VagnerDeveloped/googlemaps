<?php

/**
 * GoogleMaps [ HELPER ]
 * Classe para obeter a latitude e longitude de um determinado endereço.
 *
 * @copyright (c) 2016, Vagner Cardoso - FAMÍLIA UPINSIDE
 */
class GoogleMaps{

    private $GoogleApi;
    private $GoogleKey;
    private $Local;
    private $Latitude;
    private $Longitude;
    private $Result;

    /**
     * <b>GoogleMaps:</b> responsável por gerenciar toda verificação para retorna
     * a Latitude e Longitude já com os valores corretos!
     *
     * @param $GoogleKey = Key do seu console do GOOGLE.
     * @param $Local = Endereço de onde quer pegar a LATITUDE e Longitude
     */
    public function __construct($GoogleKey, $Local){
        $this->GoogleKey = (string) $GoogleKey;
        $this->Local = (string) $Local;

        if(empty($GoogleKey) || empty($Local)):
            //Retornando um erro explicando como criar a KEY e falando que os campos são obrigatório
            Erro("<b>ERROR:</b> Para funcionar corretamente siga esses passos a baixo.<br/> 
            <b>Parametros:</b> <mark>GoogleKey e Local</mark> são obrigatorio instanciar ele na classe. ( EX: Variável = new GoogleMaps(GoogleKey, Local) )
            <br />
            <br />
            
            <mark>GoogleKey:</mark>
            <div style='padding-left: 20px;'>
                - <a href='https://code.google.com/apis/console/' target='_blank'>Entre no site de Console de APIs</a><br />
                - Clique no lado esquerdo em Serviços.<br />
                - Ative o serviço API do Google Maps v3.<br />
                - No menu esquerdo clique no link acesso á api, a chave de acesso estará disponível nesta página na sessão Acesso Simples a API.
            </div>
            <br />
            
            <mark>Local:</mark>
            <div style='padding-left: 20px;'>
                - Endereço que vai ser montado o MAPA.<br />
                - Exemplo: Rua, Numero, Bairro, Cidade, Estado, Brasil
            </div>", E_USER_ERROR);
        else:
            //Url da API do google
            $this->GoogleApi = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($this->Local) . "&key={$this->GoogleKey}";

            //Pegando resultado da consulta
            $this->GMaps();

            //Verificando e retornando os valores já convertidos!
            if($this->Result):
                //Se o "status" for "OK" pegamos a latitude e longitude para jogarmos em nosso mapinha :)
                if($this->Result['status'] == "OK"):
                    //Valor da Latitude do endereço
                    $this->Latitude = $this->Result['results'][0]['geometry']['location']['lat'];
                    //Valor da Longitude do endereço
                    $this->Longitude = $this->Result['results'][0]['geometry']['location']['lng'];
                else:
                    //Se o "status" for diferente de "OK" acesse o LINK para ver oque do significado
                    Erro("<b>{$this->Result['status']}</b>: Veja o significado deste erro para corrigir: <a target='_blank' href='https://developers.google.com/maps/documentation/geocoding/intro#StatusCodes'>Status do Código</a>", E_USER_WARNING);
                endif;
            else:
                /*
                 * Aqui seria a verificação de falha ao tentar pega os dados.
                 * Porém se não existir a função CURL ativada no PHP ele vai pega o retorno e transformar em json
                 * e por isso aqui não precisa de uma verificação ao meu ponto de vista, porque só vai dar erro se
                 * o endereço estiver errado ou não existir ou você não definir as duas variável para instanciar ela na classe.
                 */
            endif;

            //Já está tudo OK e seu mapa está aparecendo então vamos resetar os campos da memoria pois já está OK! :)
            $this->GoogleKey = null;
            $this->Local = null;
            $this->Result = null;
            $this->GoogleApi = null;
        endif;
    }

    /**
     * <b>getLatitude()</b>: Ela retorna o número já completo da latitude.
     *
     * @return mixed
     */
    public function getLatitude(){
        return $this->Latitude;
    }

    /**
     * <b>getLongitude()</b>: Ela retorna o número já completo da longitude.
     *
     * @return mixed
     */
    public function getLongitude(){
        return $this->Longitude;
    }

    /**
     * Envia uma solicitação para a API do Google Maps
     * para retornar um json com os dados.
     *
     * Os dados jSON são tranformado em um ARRAY normal.
     *
     * @return $this->Result
     */
    private function GMaps(){
        if(function_exists('curl_init')):
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->GoogleApi);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $this->Result = curl_exec($ch);
            curl_close($ch);

            $this->Result = json_decode($this->Result, true);
        else:
            $this->Result = json_decode(file_get_contents($this->GoogleApi), true);
        endif;
    }
}