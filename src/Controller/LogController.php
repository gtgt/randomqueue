<?php
namespace RandomQueue\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LogController extends AbstractController {
    protected const CONTROLLER_ACTIONS = [
        '/log' => 'logList',
        '/log/{id}' => 'logItem',
    ];

    /**
     * @var \PDO
     */
    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function logListAction() {
        $stmt = $this->pdo->query('SELECT id, time, level, message, context FROM log ORDER BY id DESC');
        return new Response(
            json_encode($stmt->fetchAll(), JSON_PARTIAL_OUTPUT_ON_ERROR)
        );
    }

    public function logItemAction(Request $request) {
        $stmt = $this->pdo->prepare('SELECT id, time, level, message, context FROM log WHERE id = :id ORDER BY id DESC');
        $stmt->execute([':id' => $request->attributes->getInt('id')]);
        return new Response(
            json_encode($stmt->fetchAll(), JSON_PARTIAL_OUTPUT_ON_ERROR)
        );
    }
}
