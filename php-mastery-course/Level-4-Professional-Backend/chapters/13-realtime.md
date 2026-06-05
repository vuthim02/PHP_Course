# Chapter 13: Real-time Features

## Learning Objectives

- Implement WebSockets with Ratchet
- Build Server-Sent Events (SSE)
- Create real-time notifications
- Handle concurrent connections

---

```mermaid
sequenceDiagram
    participant ClientA as Browser A
    participant ClientB as Browser B
    participant WS as WebSocket Server
    participant PHP as PHP Backend
    participant DB as Database

    ClientA->>WS: 1. HTTP Upgrade Request
    WS->>ClientA: 101 Switching Protocols
    Note over ClientA,WS: WebSocket connection established

    ClientB->>WS: 2. HTTP Upgrade Request
    WS->>ClientB: 101 Switching Protocols

    ClientA->>WS: 3. Send message: "Hello everyone!"
    WS->>WS: Parse message JSON
    WS->>DB: Store message in chat_log
    WS->>ClientA: Broadcast: "Hello everyone!" (from A)
    WS->>ClientB: Broadcast: "Hello everyone!" (from A)

    ClientB->>WS: 4. Typing indicator
    WS->>ClientA: User B is typing...

    ClientA->>WS: 5. Disconnect
    WS->>ClientB: User A left the chat
```

```mermaid
flowchart TD
    subgraph SSE vs WebSocket
        SSE[Server-Sent Events] -->|One-way, text-only| C[Simple notifications]
        WS2[WebSocket] -->|Bidirectional, binary + text| D[Real-time chat, games]
    end

    subgraph Ratchet Stack
        E[WebSocket Client] --> F[WsServer]
        F --> G[HttpServer]
        G --> H[IoServer]
        H --> I[Event Loop]
        I --> J[MessageComponentInterface]
        J -->|onOpen| K[Client connected]
        J -->|onMessage| L[Message received]
        J -->|onClose| M[Client disconnected]
    end
```

## 13.1 WebSocket Server

```php
<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class ChatServer implements MessageComponentInterface
{
    protected \SplObjectStorage $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
        echo "Chat server started\n";
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        $this->clients->attach($conn);
        echo "New connection: {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        $data = json_decode($msg, true);
        
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                $client->send(json_encode([
                    'type' => 'message',
                    'user' => $data['user'],
                    'message' => $data['message'],
                    'timestamp' => time(),
                ]));
            }
        }
    }

    public function onClose(ConnectionInterface $conn): void
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

// Start server: php server.php
// $server = IoServer::factory(
//     new HttpServer(
//         new WsServer(
//             new ChatServer()
//         )
//     ),
//     8080
// );
// $server->run();
```

---

## 13.2 Server-Sent Events (SSE)

```php
<?php
class SSEController
{
    public function stream(): never
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        $lastId = 0;

        while (true) {
            // Check for new events
            $events = $this->getNewEvents($lastId);
            
            foreach ($events as $event) {
                echo "id: {$event['id']}\n";
                echo "event: {$event['type']}\n";
                echo "data: " . json_encode($event['data']) . "\n\n";
                $lastId = $event['id'];
            }

            // Keep alive
            echo ": heartbeat\n\n";
            ob_flush();
            flush();

            // Check if client disconnected
            if (connection_aborted()) {
                break;
            }

            sleep(1);
        }
    }

    private function getNewEvents(int $lastId): array
    {
        // Fetch events from database/queue
        return [];
    }
}

// Client-side JavaScript:
// const evtSource = new EventSource('/api/events/stream');
// evtSource.addEventListener('notification', (e) => {
//     const data = JSON.parse(e.data);
//     showNotification(data);
// });
```

---

## 13.3 Exercises

1. Build a WebSocket-based real-time notification system
2. Implement SSE for live event streaming
3. Create a real-time dashboard that updates via WebSockets
4. Handle reconnection and message queuing for offline clients

---

## Further Reading

- **Doc:** [Ratchet WebSockets](http://socketo.me/)
- **Doc:** [MDN Server-Sent Events](https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events)
