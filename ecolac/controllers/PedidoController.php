<?php

require_once 'models/Pedido.php';

class PedidoController
{
    public function realizar()
    {
        require_once 'views/pedido/registro.php';
    }

    public function gestion()
    {
        $ped = new Pedido();
        $pedidos = $ped->GetAllPedidos();
        require_once 'views/pedido/gestion.php';
    }

    public function pedidossucursal()
    {
        $ped = new Pedido();
        $ped->suc_id = $_SESSION['userconnect']->suc_id;
        $pedidos = $ped->GetAllPedidosBySuc();
        require_once 'views/pedido/gestion.php';
    }

    public function gestionpedido()
    {
        if (isset($_GET['id'])) {
            $ped = new Pedido();
            $ped->ped_id = $_GET['id'];
            $entity = $ped->GetPedidoById();
            require_once 'views/pedido/gestionpedido.php';
        }
    }

    public function despachar()
    {
        if (isset($_GET['id']) && isset($_GET['ven'])) {
            try {
                $ped = new Pedido();
                $ped->ped_id = $_GET['id'];
                $ped->usr_ven_id = $_GET['ven'];
                $ped->DespacharPedido();
                $_SESSION['pedidotoGestionMensaje'] = "Pedido #{$ped->ped_id} despachado correctamente :)";
                App::Redirect('pedido/pedidossucursal');
            } catch (\Throwable $th) {
                $_SESSION['gestionPedidoError'] = $th->getMessage();
                App::Redirect('pedido/gestionpedido&id=' . $_GET['id']);
            }
        }
    }

    public function agregar()
    {
        try {
            if (isset($_POST) && isset($_SESSION['succonnect'])) {

                APP::ValidarDireccionPedido($_POST['direccion']);
                $ped = new Pedido();
                $ped->usr_id = $_SESSION['userconnect']->usr_id;
                $ped->dir_id = $_POST['direccion'];
                $ped->ped_costo = App::EstadisticasCarrito()['total'];
                $ped->suc_id = $_SESSION['succonnect']->suc_id;
                $result = $ped->GuardarPedido();

                if (!is_null($result)) {
                    $_SESSION['pedidoMisPedidosMensaje'] = 'Su pedido se guardo correctamente!';
                    App::UnsetSessionVar('carrito');
                    App::Redirect('pedido/mispedidos');
                } else {
                    $_SESSION['carritoIndexError'] = 'Intente realizar su pedido mas tarde!';
                    App::Redirect('carrito/index');
                }
            }
        } catch (\Throwable $th) {
            $_SESSION['carritoIndexError'] = $th->getMessage();
            App::Redirect('carrito/index');
        }
    }

    public function mispedidos()
    {
        require_once 'views/pedido/mispedidos.php';
    }

    public function detallepedido()
    {
        if (isset($_GET['id'])) {
            $ped = new Pedido();
            $ped->ped_id = $_GET['id'];
            $entity = $ped->GetPedidoById();
            require_once 'views/pedido/detallepedido.php';
        }
    }
}
