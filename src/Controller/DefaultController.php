<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    /**
     * @Route("/",  name="home")
     */
    public function index()
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/GetTodo")
     */
    public function GetTodo()
    {
        $response = new Response();

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        // BDD Config
        $servername = 'localhost';
        $username = 'root';
        $password = '';

        $dataToClient = array(
            "id" => array(),
            "memo" => array(),
            "dateAjout" => array(),
            "priority" => array(),
        );

        // On établit la connexion
        $mysqli = mysqli_connect($servername, $username, $password, "node");

        // On vérifie la connexion
        if (!$mysqli) {
            die('Erreur : ' . mysqli_connect_error());
        }

        $res = mysqli_query($mysqli, "SELECT * FROM `todolist` ORDER BY priority ASC");
        $row = mysqli_fetch_all($res);

        for ($i = 0; $i < count($row); $i++) {
            $dataToClient["id"][] = $row[$i][0];
            $dataToClient["memo"][] = $row[$i][1];
            $dataToClient["dateAjout"][] = $row[$i][2];
            $dataToClient["priority"][] = $row[$i][3];
        }

        mysqli_close($mysqli);

        $response->setContent(json_encode($dataToClient));
        return $response;
    }

    /**
     * @Route("/InsertTodo")
     */
    public function InsertTodo()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $memo = json_decode($_POST['data'], true);

        // BDD Config
        $servername = 'localhost';
        $username = 'root';
        $password = '';

        // On établit la connexion
        $mysqli = mysqli_connect($servername, $username, $password, "node");

        // // On vérifie la connexion
        if (!$mysqli) {
            die('Erreur : ' . mysqli_connect_error());
        }

        $res = mysqli_query($mysqli, "SELECT COUNT(*) as isEmpty FROM `todolist`");
        $row = mysqli_fetch_row($res);

        $isEmpty = $row[0];

        if ($isEmpty != 0) {
            $res = mysqli_query($mysqli, "SELECT priority FROM `todolist` ORDER BY priority DESC LIMIT 1");
            $row = mysqli_fetch_row($res);
            $priority = 1 + $row[0];
            $res = mysqli_query($mysqli, "INSERT INTO `todolist` (`id`, `memo`, `dateAjout`, `priority`) VALUES (NULL, '" . $memo . "', NOW(), '" . $priority . "');");
        } else {
            $res = mysqli_query($mysqli, "INSERT INTO `todolist` (`id`, `memo`, `dateAjout`, `priority`) VALUES (NULL, '" . $memo . "', NOW(), '1');");
        }

        mysqli_close($mysqli);

        $response->setContent(json_encode(""));
        return $response;
    }

    /**
     * @Route("/DeleteTodo")
     */
    public function DeleteTodo()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $id = json_decode($_POST['data'], true);
        
        // BDD Config
        $servername = 'localhost';
        $username = 'root';
        $password = '';

        // On établit la connexion
        $mysqli = mysqli_connect($servername, $username, $password, "node");

        // // On vérifie la connexion
        if (!$mysqli) {
            die('Erreur : ' . mysqli_connect_error());
        }

        // Get priority of memo to delete
        $res = mysqli_query($mysqli, "SELECT priority FROM `todolist` WHERE `todolist`.`id` = " . $id . ";");
        $row = mysqli_fetch_row($res);
        $priority = $row[0];
        $res = mysqli_query($mysqli, "DELETE FROM `todolist` WHERE `todolist`.`id` = " . $id . ";");

        // Get id of all memo whose priority is higther than priority of memo to delete
        $res = mysqli_query($mysqli, "SELECT * FROM `todolist` WHERE `todolist`.`priority` > " . $priority . ";");
        $row = mysqli_fetch_all($res);

        for ($i = 0; $i < count($row); $i++) {
            $res = mysqli_query($mysqli, "SELECT priority FROM `todolist` WHERE `todolist`.`id` = " . $row[$i][0] . ";");
            $result = mysqli_fetch_row($res);
            $priority = $result[0] - 1;
            echo "UPDATE `todolist` SET priority = '" . $priority . "' WHERE id = '" . $row[$i][0] . "';";
            $res = mysqli_query($mysqli, "UPDATE `todolist` SET priority = '" . $priority . "' WHERE id = '" . $row[$i][0] . "';");
        }

        mysqli_close($mysqli);

        $response->setContent(json_encode(""));
        return $response;
    }

    /**
     * @Route("/ChangePriorityTodo")
     */
    public function ChangePriorityTodo()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $data = json_decode($_POST['data'], true);

        $id = $data["id"];
        $priorityActual = $data["priorityActual"];
        $newPriority = $data["newPriority"];

        // BDD Config
        $servername = 'localhost';
        $username = 'root';
        $password = '';

        // On établit la connexion
        $mysqli = mysqli_connect($servername, $username, $password, "node");

        // // On vérifie la connexion
        if (!$mysqli) {
            die('Erreur : ' . mysqli_connect_error());
        }

        $res = mysqli_query($mysqli, "UPDATE `todolist` SET priority = '" . $priorityActual . "' WHERE priority = '" . $newPriority . "';");
        $res = mysqli_query($mysqli, "UPDATE `todolist` SET priority = '" . $newPriority . "' WHERE priority = '" . $priorityActual . "' and id = '" . $id . "';");

        mysqli_close($mysqli);

        $response->setContent(json_encode(""));
        return $response;
    }

}
