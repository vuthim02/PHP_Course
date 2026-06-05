# Chapter 17: Cloud Deployment

## Learning Objectives

- Deploy PHP applications to AWS
- Configure cloud infrastructure
- Implement auto-scaling
- Manage cloud costs

---

## 17.1 AWS Deployment

```php
<?php
// AWS Elastic Beanstalk configuration
// .ebextensions/php-settings.config
// option_settings:
//   - namespace: aws:elasticbeanstalk:container:php:phpini
//     option_name: document_root
//     value: /public
//   - namespace: aws:elasticbeanstalk:container:php:phpini
//     option_name: memory_limit
//     value: 256M
//   - namespace: aws:elasticbeanstalk:container:php:phpini
//     option_name: composer_options
//     value: --no-dev --optimize-autoloader

// AWS SDK integration
use Aws\S3\S3Client;
use Aws\Sqs\SqsClient;
use Aws\SecretsManager\SecretsManagerClient;

class CloudService
{
    private S3Client $s3;
    private SqsClient $sqs;
    private SecretsManagerClient $secrets;

    public function __construct()
    {
        $this->s3 = new S3Client([
            'version' => 'latest',
            'region' => $_ENV['AWS_REGION'],
        ]);

        $this->sqs = new SqsClient([
            'version' => 'latest',
            'region' => $_ENV['AWS_REGION'],
        ]);

        $this->secrets = new SecretsManagerClient([
            'version' => 'latest',
            'region' => $_ENV['AWS_REGION'],
        ]);
    }

    public function uploadToS3(string $bucket, string $key, string $body): void
    {
        $this->s3->putObject([
            'Bucket' => $bucket,
            'Key' => $key,
            'Body' => $body,
            'ServerSideEncryption' => 'AES256',
        ]);
    }

    public function sendToQueue(string $queueUrl, array $message): void
    {
        $this->sqs->sendMessage([
            'QueueUrl' => $queueUrl,
            'MessageBody' => json_encode($message),
            'MessageGroupId' => 'default',
        ]);
    }

    public function getSecret(string $secretName): string
    {
        $result = $this->secrets->getSecretValue([
            'SecretId' => $secretName,
        ]);
        return $result['SecretString'];
    }
}

// Serverless PHP with Bref
// serverless.yml
// service: my-php-app
// provider:
//   name: aws
//   region: us-east-1
//   runtime: provided.al2
//
// plugins:
//   - ./vendor/bref/bref
//
// functions:
//   api:
//     handler: public/index.php
//     layers:
//       - ${bref:layer.php-82-fpm}
//     events:
//       - httpApi: '*'
```

---

## 17.2 Exercises

1. Deploy a PHP application to AWS Elastic Beanstalk
2. Configure auto-scaling based on CPU/memory
3. Integrate S3 for file storage and SQS for queues
4. Deploy serverless PHP application with Bref

---

## Further Reading

- **Doc:** [AWS SDK PHP](https://docs.aws.amazon.com/sdk-for-php/)
- **Doc:** [Bref](https://bref.sh/)
