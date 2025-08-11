<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Repositories\PlantRepository;
use App\Repositories\RiegoRepository;
use App\Services\RiegoService;

class RiegoController {
    private PlantRepository $plantRepo;
    private RiegoRepository $riegoRepo;
    private RiegoService $riegoService;

    public function __construct(PlantRepository $p, RiegoRepository $r, RiegoService $s) {
        $this->plantRepo = $p;
        $this->riegoRepo = $r;
        $this->riegoService = $s;
    }

    public function registrar(Request $req, Response $res, $args): Response {
        $id = (int)$args['id_planta'];
        $plant = $this->plantRepo->find($id);
        if(!$plant) {
            $res->getBody()->write(json_encode(['error'=>'Recurso no encontrado','mensaje'=>'No se encontró una planta con el ID proporcionado']));
            return $res->withStatus(404)->withHeader('Content-Type','application/json');
        }

        $body = json_decode((string)$req->getBody(), true);
        if(empty($body['fecha_riego'])) {
            $res->getBody()->write(json_encode(['error'=>'Datos inválidos','detalles'=>['fecha_riego'=>'Este campo es obligatorio']]));
            return $res->withStatus(400)->withHeader('Content-Type','application/json');
        }

        $fechaRiego = $body['fecha_riego'];
        $d = \DateTime::createFromFormat('Y-m-d',$fechaRiego);
        if(!$d) {
            $res->getBody()->write(json_encode(['error'=>'Datos inválidos','detalles'=>['fecha_riego'=>'Formato no válido. Use YYYY-MM-DD']]));
            return $res->withStatus(400)->withHeader('Content-Type','application/json');
        }

        $this->riegoRepo->create($id, $fechaRiego);

        $nuevoProximo = $this->riegoService->calcularProximoDesde($plant['categoria'], $fechaRiego);
        $this->plantRepo->updateNextRiego($id, $nuevoProximo);

        $res->getBody()->write(json_encode([
            'mensaje'=>'Riego registrado correctamente',
            'id_planta'=>$id,
            'proximo_riego_actualizado'=>$nuevoProximo
        ]));
        return $res->withHeader('Content-Type','application/json');
    }

    public function proximos(Request $req, Response $res): Response {
        $rows = $this->plantRepo->nextToWater();
        $res->getBody()->write(json_encode($rows));
        return $res->withHeader('Content-Type','application/json');
    }

    public function detallePlantaConHistorial(Request $req, Response $res, $args): Response {
        $id = (int)$args['id'];
        $plant = $this->plantRepo->find($id);
        if(!$plant) {
            $res->getBody()->write(json_encode(['error'=>'Recurso no encontrado','mensaje'=>'No se encontró una planta con el ID proporcionado']));
            return $res->withStatus(404)->withHeader('Content-Type','application/json');
        }
        $hist = $this->riegoRepo->historyByPlant($id);
        $plant['historial_riego'] = $hist;
        $res->getBody()->write(json_encode($plant));
        return $res->withHeader('Content-Type','application/json');
    }
}
