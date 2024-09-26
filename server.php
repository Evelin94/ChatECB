<?php
require __DIR__ . '/vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    protected $clients;
    protected $clientIdCounter;
    protected $clientMap;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->clientIdCounter = 0;
        $this->clientMap = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clientIdCounter++;
        $clientId = $this->clientIdCounter;
        $this->clientMap[$clientId] = $conn; 
        $this->clients->attach($conn);
        echo "Nueva conexión: ({$clientId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $clientId = array_search($conn, $this->clientMap, true);
        unset($this->clientMap[$clientId]); 
        $this->clients->detach($conn);
        echo "Conexión ({$clientId}) cerrada\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8080
);

echo "Servidor WebSocket iniciado en el puerto 8080\n";

$server->run();
?>
