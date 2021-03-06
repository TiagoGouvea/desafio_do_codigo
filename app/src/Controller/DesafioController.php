<?php

namespace App\Controller;

use App\Model\TrilhaDAO;
use App\Model\Desafio;
use App\Model\DesafioDAO;
use ReflectionClass;
use Slim\Http\Request;
use Slim\Http\Response;

class DesafioController extends AdminController
{
    protected $slug = "desafio";

    public function indexAction(Request $request, Response $response, $args)
    {
        // Obter DAO de desafios
        /* @var $dao DesafioDAO */
        $dao = $this->getDAO();

        // Obter desafios
        $desafios = $dao->getAll();

        // Renderizar lista
        $content = $this->fetchView("admin/desafio/lista.twig", ['registros' => $desafios]);

        return $this->renderLayout($response, $content);
    }

    /**
     * Renderiza o formulário para inclusão
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     */
    public function addAction(Request $request, Response $response, $args)
    {
        $daoTrilha = TrilhaDAO::getInstance($this->container->db);
        $trilha = $daoTrilha->findById($args['id']);
        $content = $this->fetchView("admin/desafio/form.twig", ['trilha' => $trilha]);
        return $this->renderLayout($response, $content);
    }

    /**
     * Salva o novo registro incluido
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     */
    public function addPostAction(Request $request, Response $response, $args)
    {
        // O id da trilha, está na URL.. exemplo: /admin/trilha/1/desafio/incluir/
        $idTrilha = $args['id'];
        $vars = $request->getParsedBody();
        $vars['id_trilha'] = $idTrilha;
        try {
            /* @var $dao DesafioDAO */
            $dao = $this->getDao();
            $dao->insertFromArray($vars);
            return $this->redirect($response, "trilhaListDesafios", ['id' => $idTrilha]);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $content = $this->fetchView("admin/desafio/form.twig", ["error" => $error, "registro" => $vars]);
            return $this->renderLayout($response, $content);
        }
    }


    /**
     * Renderiza o formulário para edição
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     */
    public function editAction(Request $request, Response $response, $args)
    {
        /* @var $registrto Desafio */
        $registro = $this->getDAO()->findById($args['id']);
        $trilha = $registro->getTrilha();
        $content = $this->fetchView("admin/desafio/form.twig", ["trilha"=>$trilha, "registro" => $registro]);
        return $this->renderLayout($response, $content);
    }


    /**
     * Salva o registro alterado
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     */
    public function editPostAction(Request $request, Response $response, $args)
    {
        $vars = $request->getParsedBody();
        try {
            /* @var $dao DesafioDAO */
            $dao = $this->getDao();
            $desafio = $dao->updateFromArray($args['id'], $vars, true);
            return $this->redirect($response, "trilhaListDesafios", ['id' => $desafio->id_trilha]);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $content = $this->fetchView("admin/desafio/form.twig", ["error" => $error, "registro" => $vars]);
            return $this->renderLayout($response, $content);
        }
    }


    /**
     * Exclui (tenta ao menos) um registro
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return mixed
     */
    public function deleteAction(Request $request, Response $response, $args)
    {
        $registro = $this->getDAO()->deleteById($args['id']);
        return $this->redirect($response, "desafioIndex");
    }

    /**
     * Retorna o DAO referente a este controller
     * Implementado apenas para forçar o tipo do retorno
     * Pode ser melhorado. :)
     * @return DesafioDAO
     */
    protected function getDAO($conexao = null)
    {
        return parent::getDAO();
    }


}
