# Chapter 19: Disaster Recovery

## Learning Objectives

- Design backup strategies
- Implement failover systems
- Create recovery playbooks
- Test disaster scenarios

---

## 19.1 Backup and Recovery

```php
<?php
class BackupManager
{
    public function __construct(
        private S3Client $s3,
        private string $bucket,
    ) {}

    public function backupDatabase(string $name, string $dsn): string
    {
        $filename = "{$name}_" . date('Y-m-d_H-i-s') . '.sql.gz';
        $tempPath = "/tmp/{$filename}";

        // Create backup
        $command = "mysqldump --single-transaction --quick " .
            "-h {$this->getHost($dsn)} -u {$this->getUser($dsn)} " .
            "-p{$this->getPass($dsn)} {$this->getDbName($dsn)} | " .
            "gzip > {$tempPath}";
        
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new BackupException('Database backup failed');
        }

        // Upload to S3
        $this->s3->putObject([
            'Bucket' => $this->bucket,
            'Key' => "database/{$filename}",
            'SourceFile' => $tempPath,
            'StorageClass' => 'GLACIER',
        ]);

        unlink($tempPath);

        return "s3://{$this->bucket}/database/{$filename}";
    }

    public function restoreDatabase(string $backupPath, string $dsn): void
    {
        $tempPath = '/tmp/restore.sql.gz';

        // Download from S3
        $this->s3->getObject([
            'Bucket' => $this->bucket,
            'Key' => str_replace("s3://{$this->bucket}/", '', $backupPath),
            'SaveAs' => $tempPath,
        ]);

        // Restore
        $command = "gunzip < {$tempPath} | mysql " .
            "-h {$this->getHost($dsn)} -u {$this->getUser($dsn)} " .
            "-p{$this->getPass($dsn)} {$this->getDbName($dsn)}";
        
        exec($command, $output, $returnCode);

        unlink($tempPath);

        if ($returnCode !== 0) {
            throw new BackupException('Database restore failed');
        }
    }

    public function listBackups(string $prefix = 'database/'): array
    {
        $objects = $this->s3->listObjects([
            'Bucket' => $this->bucket,
            'Prefix' => $prefix,
        ]);

        return array_map(fn($obj) => [
            'key' => $obj['Key'],
            'size' => $obj['Size'],
            'last_modified' => $obj['LastModified']->format('c'),
        ], $objects['Contents'] ?? []);
    }
}

// Disaster recovery playbook
// 1. Database failure → Promote replica to master
// 2. Region outage → Failover to DR region
// 3. Data corruption → Restore from backup
// 4. Application crash → Auto-scale new instances
```

---

## 19.2 Exercises

1. Automate daily database backups to S3/Glacier
2. Implement cross-region replication
3. Create a disaster recovery playbook document
4. Test failover scenarios (database, cache, application)

---

## Further Reading

- **Doc:** [AWS Disaster Recovery](https://docs.aws.amazon.com/wellarchitected/latest/reliability-pillar/disaster-recovery.html)
