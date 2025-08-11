<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Repositories\PlantRepository;
use App\Services\RiegoService;
use App\Repositories\RiegoRepository;

class PlantController {
    private PlantRepository $repo;
    private RiegoService $riegoService;
    private RiegoRepository $riegoRepo;

    public function __construct(PlantRepository $repo, RiegoService $rs, RiegoRepository $rr) {
        $this->repo = $repo;
        $this->riegoService = $rs;
        $this->riegoRepo = $rr;
    }

    public function create(Request $req, Response $res): Response {
        $body = json_decode((string)$req->getBody(), true);
        $errors = [];

        if(empty($body['nombre'])) $errors['nombre'] = 'Este campo es obligatorio';
        if(empty($body['categoria']) || !$this->riegoService->validarCategoria($body['categoria'])) {
            $errors['categoria'] = 'Valor no permitido. Opciones válidas: cactus, ornamental, frutal';
        }
        if(empty($body['familia'])) $errors['familia'] = 'Este campo es obligatorio';

        if(!empty($errors)) {
            $res->getBody()->write(json_encode(['error'=>'Datos inválidos','detalles'=>$errors]));
            return $res->withStatus(400)->withHeader('Content-Type','application/json');
        }

        $proximo = $this->riegoService->calcularProximoToday($body['categoria']);
        $plant = $this->repo->create($body['nombre'],$body['categoria'],$body['familia'],$proximo);
        $res->getBody()->write(json_encode($plant));
        return $res->withHeader('Content-Type','application/json');
    }

    public function all(Request $req, Response $res): Response {
        $rows = $this->repo->all();
        $res->getBody()->write(json_encode($rows));
        return $res->withHeader('Content-Type','application/json');
    }

    public function byCategory(Request $req, Response $res, $args): Response {
        $cat = $args['categoria'];
        if(!$this->riegoService->validarCategoria($cat)) {
            $res->getBody()->write(json_encode(['error'=>'Datos inválidos','detalles'=>['categoria'=>'Valor no permitido. Opciones válidas: cactus, ornamental, frutal']]));
            return $res->withStatus(400)->withHeader('Content-Type','application/json');
        }
        $rows = $this->repo->byCategory($cat);
        $res->getBody()->write(json_encode($rows));
        return $res->withHeader('Content-Type','application/json');
    }

    public function detalle(Request $req, Response $res, $args): Response {
        $id = (int)$args['id'];
        $plant = $this->repo->find($id);
        if(!$plant) {
            $res->getBody()->write(json_encode(['error'=>'Recurso no encontrado','mensaje'=>'No se encontró una planta con el ID proporcionado']));
            return $res->withStatus(404)->withHeader('Content-Type','application/json');
        }
        // Añadir historial
        $hist = $this->riegoRepo->historyByPlant($id);
        $plant['historial_riego'] = $hist;
        $res->getBody()->write(json_encode($plant));
        return $res->withHeader('Content-Type','application/json');
    }
}
