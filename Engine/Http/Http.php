<?php

namespace Engine\Http;

/**
 * Classe Http
 * Responsável por capturar e armazenar informações da requisição HTTP.
 */
class Http
{
    // Armazena o método HTTP da requisição (GET, POST, etc.)
    private string $REQUEST_METHOD;

    // Armazena a URI da requisição (exemplo: "/home")
    private string $REQUEST_URI;

    // Armazena os parâmetros da query string (exemplo: "?id=10&name=John")
    private array  $QUERY_STRING;

    /**
     * Construtor da classe.
     * Inicializa os atributos capturando informações da superglobal $_SERVER.
     */
    public function __construct()
    {
        $this->REQUEST_METHOD = self::RETURN_METHOD();  // Obtém o método da requisição
        $this->REQUEST_URI    = self::RETURN_URI();     // Obtém a URI da requisição
        $this->QUERY_STRING   = self::RETURN_QUERY();   // Obtém os parâmetros da query string
    }

    /**
     * Retorna o método HTTP da requisição (GET, POST, etc.).
     *
     * @return string
     */
    private function RETURN_METHOD(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Retorna a URI da requisição, sem os parâmetros da query string.
     * Exemplo: Se a URL for "https://exemplo.com/home?id=10", retorna "/home".
     *
     * @return string
     */
    private function RETURN_URI(): string
    {
        $RETURNED_URI = $_SERVER['REQUEST_URI']; // Obtém a URI completa
        $PARSE_URI = parse_url($RETURNED_URI);   // Separa a URI da query string

        return $PARSE_URI['path'] ?? ''; // Retorna apenas o caminho da URI
    }

    /**
     * Retorna um array associativo contendo os parâmetros da query string.
     * Exemplo: Se a URL for "https://exemplo.com/home?id=10&name=John",
     * retorna ['id' => '10', 'name' => 'John'].
     *
     * @return array
     */
    private function RETURN_QUERY(): array
    {
        if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
            $QUERY_STRING = $_SERVER['QUERY_STRING']; // Obtém a query string
            $PARAMS = [];

            parse_str($QUERY_STRING, $PARAMS); // Converte a query string para um array associativo

            unset($PARAMS['url']); // Remove a chave 'url', caso esteja presente

            return $PARAMS; // Retorna os parâmetros da query string
        }
        return []; // Retorna um array vazio se não houver query string
    }

    // ==============================
    // Getters (Métodos de acesso)
    // ==============================

    /**
     * Retorna o método da requisição HTTP.
     *
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->REQUEST_METHOD;
    }

    /**
     * Retorna a URI da requisição.
     *
     * @return string
     */
    public function getRequestUri(): string
    {
        return $this->REQUEST_URI;
    }

    /**
     * Retorna os parâmetros da query string como um array associativo.
     *
     * @return array
     */
    public function getRequestQueryString(): array
    {
        return $this->QUERY_STRING;
    }
}
