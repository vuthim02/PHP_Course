# Chapter 13: Blockchain and PHP

## Learning Objectives

- Understand blockchain fundamentals and smart contracts
- Connect to Ethereum and read blockchain data from PHP
- Build Web3 applications with PHP
- Process crypto payments in your PHP application
- Verify blockchain transactions and signatures

---

## 13.1 Web3 Integration

```php
<?php
use Web3\Web3;
use Web3\Contract;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;

class Web3Service
{
    private Web3 $web3;

    public function __construct(
        private string $rpcUrl = 'https://mainnet.infura.io/v3/YOUR_PROJECT_ID'
    ) {
        $this->web3 = new Web3(new HttpProvider(new HttpRequestManager($this->rpcUrl)));
    }

    public function getBalance(string $address): string
    {
        $balanceWei = null;
        $this->web3->eth->getBalance($address, function ($err, $balance) use (&$balanceWei) {
            if ($err) {
                throw new \RuntimeException($err->getMessage());
            }
            $balanceWei = $balance;
        });

        return $this->fromWei($balanceWei);
    }

    public function callContract(string $contractAddress, string $abi, string $method, array $params = []): mixed
    {
        $contract = new Contract($this->web3->provider, $abi);
        
        $result = null;
        $contract->at($contractAddress)->call($method, ...$params, function ($err, $data) use (&$result) {
            if ($err) {
                throw new \RuntimeException($err->getMessage());
            }
            $result = $data;
        });

        return $result;
    }

    public function verifySignature(string $message, string $signature, string $address): bool
    {
        $recovered = $this->ecRecover($message, $signature);
        return strtolower($recovered) === strtolower($address);
    }

    private function fromWei(string $wei): string
    {
        return bcdiv($wei, '1000000000000000000', 18);
    }

    private function ecRecover(string $message, string $signature): string
    {
        // Recover address from signature using secp256k1 elliptic curve
        // In production, use kornrunner/ethereum-address library
        return '0x...';
    }
}
```

---

## 13.2 Crypto Payment Processing

```php
<?php
class CryptoPaymentProcessor
{
    public function __construct(
        private Web3Service $web3,
        private string $merchantWallet,
    ) {}

    public function createPayment(float $amountUSD): array
    {
        $ethPrice = $this->getEthPrice();
        $amountEth = $amountUSD / $ethPrice;

        return [
            'payment_id' => bin2hex(random_bytes(16)),
            'amount_eth' => $amountEth,
            'amount_usd' => $amountUSD,
            'merchant_wallet' => $this->merchantWallet,
            'expires_at' => time() + 1800, // 30 minutes
        ];
    }

    public function verifyPayment(string $paymentId, string $txHash): bool
    {
        $receipt = $this->web3->getTransactionReceipt($txHash);
        // Verify amount, recipient, confirmations
        return $receipt !== null;
    }

    public function monitorPayments(string $expectedAmount, int $confirmations = 6): void
    {
        // In production: use webhooks from BlockCypher, Etherscan, or Alchemy
        // Poll for transaction confirmations
        $txHash = $this->waitForTransaction($expectedAmount);
        
        if ($txHash) {
            // Update order status
            $this->confirmOrder($txHash);
        }
    }

    private function getEthPrice(): float
    {
        // Fetch from Chainlink oracle or CoinGecko API
        $response = file_get_contents(
            'https://api.coingecko.com/api/v3/simple/price?ids=ethereum&vs_currencies=usd'
        );
        $data = json_decode($response, true);
        return $data['ethereum']['usd'] ?? 2000.00;
    }

    private function waitForTransaction(string $amount): ?string
    {
        // Poll mempool for transaction to merchant wallet with matching amount
        // This is simplified; use a proper webhook service in production
        return null;
    }

    private function confirmOrder(string $txHash): void
    {
        // Update database, send confirmation email
    }
}

// Usage in checkout flow
$processor = new CryptoPaymentProcessor($web3, '0xYourMerchantWallet');

// Step 1: Present payment request
$payment = $processor->createPayment(99.99);
// Display: "Send {$payment['amount_eth']} ETH to {$payment['merchant_wallet']}"

// Step 2: Verify after user confirms
if ($processor->verifyPayment($payment['payment_id'], $userProvidedTxHash)) {
    // Complete order
}
```

---

## 13.3 Smart Contract Interaction

```solidity
// SimpleStorage.sol (Solidity smart contract)
// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

contract SimpleStorage {
    uint256 private storedData;

    event DataStored(uint256 data, address indexed sender);

    function set(uint256 data) public {
        storedData = data;
        emit DataStored(data, msg.sender);
    }

    function get() public view returns (uint256) {
        return storedData;
    }
}
```

```php
<?php
// Interact with the deployed contract from PHP
$abi = '[{"inputs":[],"name":"get","outputs":[{"internalType":"uint256","type":"uint256"}],"stateMutability":"view","type":"function"},{"inputs":[{"internalType":"uint256","type":"uint256"}],"name":"set","outputs":[],"stateMutability":"nonpayable","type":"function","payable":false},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint256","name":"data","type":"uint256"},{"indexed":true,"internalType":"address","name":"sender","type":"address"}],"name":"DataStored","type":"event"}]';

$contract = new Contract($web3->provider, $abi);

// Read data (free, no gas)
$contract->at($contractAddress)->call('get', function ($err, $result) {
    echo "Stored value: {$result[0]}\n";
});

// Write data (costs gas, requires private key)
// Note: Writing requires signing the transaction with a private key
// Use a library like web3.php or ethereum-tx for transaction signing
```

---

## 13.4 Practical Considerations

| Concern | Recommendation |
|---------|---------------|
| **Network fees** | Gas costs can exceed transaction value on Ethereum. Consider L2s (Polygon, Arbitrum) |
| **Confirmation time** | Wait 6+ block confirmations for finality (~1-2 min on Ethereum) |
| **Price volatility** | Lock exchange rate at payment creation (15-30 min window) |
| **User experience** | Provide QR codes, copy-to-clipboard, and payment status polling |
| **Regulatory** | Consult legal for KYC/AML requirements in your jurisdiction |
| **Production** | Use a blockchain API service (Alchemy, Infura, QuickNode) instead of running your own node |

### PHP Web3 Ecosystem

| Library | Purpose | Packagist |
|---------|---------|-----------|
| `web3-php/web3` | Ethereum JSON-RPC client | `web3p/web3.php` |
| `kornrunner/ethereum-address` | Address validation/checksum | `kornrunner/ethereum-address` |
| `kornrunner/ethereum-tx` | Transaction signing | `kornrunner/ethereum-tx` |
| `php-libp2p/php-libp2p` | P2P networking (IPFS/libp2p) | `php-libp2p/php-libp2p` |

---

## 13.5 Exercises

1. Connect to Ethereum mainnet via Infura and query your wallet balance
2. Deploy the SimpleStorage contract to a testnet (Sepolia) and call it from PHP
3. Build a crypto payment gateway that generates invoices and verifies payments
4. Verify an Ethereum signed message in PHP using ECDSA
5. Monitor the mempool for transactions to a specific address
6. Build a simple NFT metadata viewer that fetches tokenURI from a contract
7. Compare transaction costs on Ethereum vs Polygon vs Solana

---

## Further Reading

- **Doc:** [web3.php](https://github.com/web3-php/web3)
- **Doc:** [Ethereum PHP](https://ethereum.org/en/developers/docs/programming-languages/php/)
- **Doc:** [Solidity Documentation](https://docs.soliditylang.org/)
- **Tool:** [Infura](https://infura.io/) — Ethereum node provider
- **Tool:** [Etherscan API](https://etherscan.io/apis) — Blockchain explorer API
