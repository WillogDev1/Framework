<?php

namespace Engine\Router;


use Engine\Http\Http;
use Engine\Router\Routes;
use Engine\Render\Render\Render;
use Engine\Access\Access;


class Router
{
    private ?array  $GET_ROUTE = null;
    private array   $GET_CONTROLLER_AND_ACTION;
    private array   $CONTROLLER_NAME;
    private string  $CONTROLLER_ACTION;
    private string  $METHOD;
    private string  $URI;
    private array   $QUERY;
    private array   $PROTECTED_ROUTES;
    private array   $FREE_ROUTES;
    private array   $PROTECTED_FREE_ROUTES;

    public function __construct()
    {
        $HTTP   = new Http();
        $ROUTES = new Routes();


        $this->METHOD =         $HTTP->getRequestMethod();
        $this->URI    =         $HTTP->getRequestUri();
        $this->QUERY  =         $HTTP->getRequestQueryString();

        $this->PROTECTED_ROUTES             =       $ROUTES->getProtectedRoutes();
        $this->PROTECTED_FREE_ROUTES        =       $ROUTES->getProtectedFreeRoutes();
        $this->FREE_ROUTES                  =       $ROUTES->getFreeRoutes();

        $this->GET_ROUTE = self::VERIFY_IF_ROUTE_EXIST();

        if ($this->GET_ROUTE === NULL) {
            self::ERROR_PAGE();
            return;
        }
        
        $this->GET_CONTROLLER_AND_ACTION = self::RETURN_CONTROLLER_AND_ACTION($this->GET_ROUTE);

        if (is_array($this->GET_CONTROLLER_AND_ACTION['Controller']['Component'])) {

            $this->CONTROLLER_NAME = $this->GET_CONTROLLER_AND_ACTION['Controller']['Component'];
        } else {

            $this->CONTROLLER_NAME = [$this->GET_CONTROLLER_AND_ACTION['Controller']['Component']];
        }

        $this->CONTROLLER_ACTION = $this->GET_CONTROLLER_AND_ACTION['Controller']['Action'];


        self::VERIFY_IF_ROUTE_NEEDS_LOGGIN();
    }

    private function VERIFY_IF_ROUTE_EXIST(): ?array
    {
        $URI_EXIST_IN_ROUTE = self::VERIFY_IF_URI_EXIST_IN_ROUTE($this->PROTECTED_ROUTES);
        $METHOD_EXIST_IN_ROUTE = self::VERIFY_IF_METHOD_EXIST_IN_ROUTE($this->PROTECTED_ROUTES);

        if ($URI_EXIST_IN_ROUTE === TRUE || $METHOD_EXIST_IN_ROUTE === TRUE) {
            return $this->PROTECTED_ROUTES[$this->URI][$this->METHOD];
        } else {
            return NULL;
        }
    }


    private function VERIFY_IF_URI_EXIST_IN_ROUTE($ROUTES): bool
    {
        return array_key_exists($this->URI, $ROUTES);
    }

    private function VERIFY_IF_METHOD_EXIST_IN_ROUTE($ROUTES): bool
    {
        return isset($ROUTES[$this->URI]) && array_key_exists($this->METHOD, $ROUTES[$this->URI]);
    }

    private function RETURN_CONTROLLER_AND_ACTION(): array
    {
        $ROUTE_IN_PARTS = [];

        foreach ($this->GET_ROUTE as $KEY => $VALUE) {
            $lastAtIndex = strrpos($VALUE, '@');
            $COMPONENT = substr($VALUE, 0, $lastAtIndex);
            $ACTION = substr($VALUE, $lastAtIndex + 1);

            if (strpos($COMPONENT, '/') !== false) {
                $COMPONENT = explode('/', $COMPONENT);
            }

            $ROUTE_IN_PARTS[$KEY] = [
                'Component' => $COMPONENT,
                'Action'    => $ACTION
            ];
        }

        return $ROUTE_IN_PARTS;
    }


    private function VERIFY_IF_ROUTE_NEEDS_LOGGIN(): void
    {
        $RENDER = new Render();

        $RENDER->setController($this->CONTROLLER_NAME);
        $RENDER->setModel($this->CONTROLLER_NAME);
        $RENDER->setAction($this->CONTROLLER_ACTION);
        $RENDER->setView($this->CONTROLLER_NAME);
        $RENDER->setMethod($this->METHOD);
        $RENDER->setQuery($this->QUERY);

        if (array_key_exists($this->URI, $this->FREE_ROUTES)) {
            $RENDER->RENDER();
        } else {
            //$_SESSION['SESSION_ID'] = 1; //Para testes
            if (Access::ACCESS()) {
                if (array_key_exists($this->URI, $this->PROTECTED_FREE_ROUTES)) {
                    $RENDER->RENDER();
                } else {
                    if (in_array($this->CONTROLLER_ACTION, $_SESSION['user_permissions'])) {
                        $RENDER->RENDER();
                    } else {
                        echo json_encode(["message" => "Sem permissão"]);
                    }
                }
            } else {
                header("Location: /login");
                exit();
            }
        }
    }
    
    public static function ERROR_PAGE()
    {
        header("Location: /page-not-found");
        exit();
    }
}
