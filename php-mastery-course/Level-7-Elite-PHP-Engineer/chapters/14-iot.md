# Chapter 14: PHP in IoT

## Learning Objectives

- Build IoT applications with PHP for device communication
- Interface with sensors and hardware on Raspberry Pi
- Process IoT data streams with MQTT protocol
- Implement device command and control
- Build dashboards for real-time sensor data

---

## 14.1 IoT Communication with MQTT

MQTT is a lightweight publish-subscribe protocol ideal for IoT devices. PHP can act as both a publisher and subscriber.

```php
<?php
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class IoTService
{
    private MqttClient $mqtt;

    public function __construct()
    {
        $this->mqtt = new MqttClient(
            $_ENV['MQTT_BROKER'] ?? 'localhost',
            (int)($_ENV['MQTT_PORT'] ?? 1883),
            'php-iot-' . uniqid()
        );

        $settings = (new ConnectionSettings())
            ->setUsername($_ENV['MQTT_USER'] ?? '')
            ->setPassword($_ENV['MQTT_PASS'] ?? '')
            ->setKeepAliveInterval(60)
            ->setConnectTimeout(5);

        $this->mqtt->connect($settings, true);
    }

    public function publishSensorData(string $deviceId, array $data): void
    {
        $this->mqtt->publish(
            "sensors/{$deviceId}/data",
            json_encode([
                'device_id' => $deviceId,
                'timestamp' => time(),
                'temperature' => $data['temperature'],
                'humidity' => $data['humidity'],
                'pressure' => $data['pressure'] ?? null,
            ]),
            MqttClient::QOS_AT_LEAST_ONCE
        );
    }

    public function subscribeToDevice(string $deviceId, callable $callback): void
    {
        $this->mqtt->subscribe("sensors/{$deviceId}/command", function ($topic, $message) use ($callback) {
            $command = json_decode($message, true);
            $callback($command);
        }, MqttClient::QOS_AT_LEAST_ONCE);

        $this->mqtt->loop(true);
    }

    public function __destruct()
    {
        $this->mqtt->disconnect();
    }
}
```

---

## 14.2 Hardware Interfacing (Raspberry Pi)

PHP can interact with GPIO pins and 1-Wire sensors by reading/writing to the Linux sysfs interface.

```php
<?php
class GPIOManager
{
    private array $exportedPins = [];

    public function exportPin(int $pin): void
    {
        if (!file_exists("/sys/class/gpio/gpio{$pin}")) {
            file_put_contents('/sys/class/gpio/export', (string)$pin);
            usleep(100000); // Wait for sysfs to create files
        }
        $this->exportedPins[$pin] = true;
    }

    public function setDirection(int $pin, string $direction): void
    {
        $this->ensureExported($pin);
        file_put_contents("/sys/class/gpio/gpio{$pin}/direction", $direction);
    }

    public function writePin(int $pin, bool $value): void
    {
        $this->ensureExported($pin);
        file_put_contents("/sys/class/gpio/gpio{$pin}/value", $value ? '1' : '0');
    }

    public function readPin(int $pin): bool
    {
        $this->ensureExported($pin);
        return trim(file_get_contents("/sys/class/gpio/gpio{$pin}/value")) === '1';
    }

    public function readTemperature(): ?float
    {
        // Read DS18B20 temperature sensor via 1-Wire
        $basePath = '/sys/bus/w1/devices';
        $devices = glob("{$basePath}/28-*");

        if (empty($devices)) {
            return null;
        }

        $raw = file_get_contents($devices[0] . '/w1_slave');
        if (!str_contains($raw, 'YES')) {
            return null; // CRC check failed
        }

        preg_match('/t=(-?\d+)/', $raw, $matches);
        return isset($matches[1]) ? (int)$matches[1] / 1000.0 : null;
    }

    public function readDHT22(int $pin): ?array
    {
        // Read DHT22 temperature/humidity sensor
        // Requires custom bit-banging or a C extension
        // Simplified: assume external script writes to file
        $data = @file_get_contents("/tmp/dht22_{$pin}.json");
        return $data ? json_decode($data, true) : null;
    }

    private function ensureExported(int $pin): void
    {
        if (!isset($this->exportedPins[$pin])) {
            throw new \RuntimeException("Pin {$pin} not exported. Call exportPin() first.");
        }
    }

    public function __destruct()
    {
        foreach (array_keys($this->exportedPins) as $pin) {
            file_put_contents('/sys/class/gpio/unexport', (string)$pin);
        }
    }
}
```

---

## 14.3 IoT Data Pipeline

```
┌─────────────┐    ┌────────────┐    ┌──────────────┐    ┌────────────┐
│ Sensor Node  │───▶│ MQTT Broker│───▶│ PHP Consumer │───▶│ Database   │
│ (Raspberry Pi)│   │ (Mosquitto)│    │ (Worker)     │    │ (TimescaleDB)│
└─────────────┘    └────────────┘    └──────────────┘    └────────────┘
                                              │
                                              ▼
                                     ┌──────────────┐
                                     │ Web Dashboard │
                                     │ (Laravel)     │
                                     └──────────────┘
```

```php
<?php
// IoT data consumer (runs as a long-running PHP CLI process)
require __DIR__ . '/vendor/autoload.php';

class IoTDataPipeline
{
    private PDO $db;
    private IoTService $iot;

    public function __construct()
    {
        $this->db = new PDO(
            $_ENV['DB_DSN'] ?? 'pgsql:host=localhost;dbname=iot',
            $_ENV['DB_USER'] ?? 'iot',
            $_ENV['DB_PASS'] ?? 'secret'
        );
        $this->iot = new IoTService();
    }

    public function run(): void
    {
        // Subscribe to all sensor data
        $this->iot->subscribe('sensors/+/data', function (string $topic, string $message) {
            $data = json_decode($message, true);

            // Store in time-series database
            $stmt = $this->db->prepare(
                'INSERT INTO sensor_readings (device_id, timestamp, temperature, humidity, pressure)
                 VALUES (?, to_timestamp(?), ?, ?, ?)'
            );
            $stmt->execute([
                $data['device_id'],
                $data['timestamp'],
                $data['temperature'],
                $data['humidity'],
                $data['pressure'] ?? null,
            ]);

            // Check for alert conditions
            if ($data['temperature'] > 40.0) {
                $this->triggerAlert($data['device_id'], 'high_temperature', $data['temperature']);
            }
        });

        // Run event loop
        $this->iot->loop();
    }

    private function triggerAlert(string $deviceId, string $type, float $value): void
    {
        $alert = [
            'device_id' => $deviceId,
            'type' => $type,
            'value' => $value,
            'message' => "Device {$deviceId}: {$type} = {$value}",
            'created_at' => time(),
        ];

        file_put_contents('/var/log/iot/alerts.log', json_encode($alert) . PHP_EOL, FILE_APPEND);

        // Optionally: send push notification, email, or SMS
    }
}

$pipeline = new IoTDataPipeline();
$pipeline->run();
```

---

## 14.4 Real-Time Dashboard

```php
<?php
// routes/web.php (Laravel example)
Route::get('/sensors', [SensorController::class, 'index']);
Route::get('/sensors/{deviceId}/history', [SensorController::class, 'history']);
Route::post('/sensors/{deviceId}/command', [SensorController::class, 'sendCommand']);

// app/Http/Controllers/SensorController.php
class SensorController extends Controller
{
    public function index(): View
    {
        $sensors = DB::table('sensor_readings')
            ->select('device_id',
                DB::raw('MAX(temperature) as max_temp'),
                DB::raw('MIN(temperature) as min_temp'),
                DB::raw('AVG(temperature) as avg_temp'),
                DB::raw('MAX(humidity) as humidity'),
                DB::raw('MAX(created_at) as last_seen')
            )
            ->groupBy('device_id')
            ->get();

        return view('sensors.dashboard', ['sensors' => $sensors]);
    }

    public function history(string $deviceId): JsonResponse
    {
        $data = DB::table('sensor_readings')
            ->where('device_id', $deviceId)
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at')
            ->get();

        return response()->json($data);
    }

    public function sendCommand(string $deviceId, Request $request): JsonResponse
    {
        $command = $request->validate(['command' => 'required|string']);

        // Publish command to MQTT
        // $iot->publish("sensors/{$deviceId}/command", json_encode($command));

        return response()->json(['status' => 'sent']);
    }
}
```

---

## 14.5 Practical PHP IoT Projects

### Project 1: Smart Thermostat

- Read temperature and humidity from DHT22 every 30 seconds
- Publish to MQTT broker
- PHP backend stores data and triggers HVAC relay via GPIO
- Web dashboard shows historical data and allows setpoint adjustment

### Project 2: Security Camera Motion Detector

- PIR motion sensor triggers GPIO interrupt
- PHP captures image from USB camera
- Uploads to S3 and sends push notification
- Logs events to database with timestamps

### Project 3: Plant Watering System

- Soil moisture sensor read via ADC
- PHP controls water pump relay via GPIO
- Schedule-based and moisture-based watering
- Dashboard with moisture trends and water usage

---

## 14.6 Exercises

1. Read temperature data from a DS18B20 sensor on Raspberry Pi using PHP
2. Publish IoT sensor data to an MQTT broker and consume it with a PHP subscriber
3. Build a web dashboard that displays real-time sensor data using Server-Sent Events
4. Implement device commands (turn on/off LED, activate relay) via MQTT from a web UI
5. Create a time-series database schema and ingest sensor data from multiple devices
6. Set up alert thresholds and notification system for sensor readings
7. Build a battery status monitor for remote IoT devices

---

## Further Reading

- **Doc:** [PHP MQTT Client](https://github.com/php-mqtt/client)
- **Doc:** [Raspberry Pi GPIO](https://www.raspberrypi.com/documentation/computers/os.html#gpio-control)
- **Doc:** [Mosquitto MQTT Broker](https://mosquitto.org/)
- **Doc:** [TimescaleDB](https://docs.timescale.com/) — Time-series database for IoT
- **Book:** "IoT with PHP" by Timm Friebe
