<?php
namespace fcosantos\CloudflareDns;

class CloudflareDNSUpdater {
    // Cloudflare zone ID
    private string $zoneId;
    // Cloudflare account email
    private string $email;
    // API key or token
    private string $apiKey;
    // Base domain
    private string $domain;

    // Constructor with essential data
    public function __construct(string $zoneId, string $email, string $apiKey, string $domain) {
        $this->zoneId = $zoneId; 
        $this->email  = $email;  
        $this->apiKey = $apiKey; 
        $this->domain = $domain; 
    }

    // Update a specific DNS record
    public function updateDns(string $subdomain, string $recordId, int $ttl, bool $proxied, string $type = "A"): void {
        $dynamicIp = $this->getDynamicIp(); 
        $recordName = $subdomain === '' ? $this->domain : $subdomain . '.' . $this->domain; 
        $url = "https://api.cloudflare.com/client/v4/zones/{$this->zoneId}/dns_records/{$recordId}"; 
        $data = [ 
            "type"    => $type, 
            "name"    => $recordName,
            "content" => $dynamicIp,
            "ttl"     => $ttl,
            "proxied" => $proxied
        ];
        $response = $this->callCloudflareAPI("PUT", $url, $data); 
        echo "Response for {$recordName}: " . $response . "\n"; 
    }

    // Retrieve the current dynamic IP
    private function getDynamicIp(): string {
        $ip = trim(file_get_contents("https://ipv4.icanhazip.com")); 
        if (!$ip) { 
            throw new \Exception("Error retrieving dynamic IP.");
        }
        return $ip; 
    }

    // Perform the API call to Cloudflare
    private function callCloudflareAPI(string $method, string $url, ?array $data = null): string {
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); 
        $headers = [ 
            "X-Auth-Email: " . $this->email,
            "Authorization: Bearer " . $this->apiKey,
            "Content-Type: application/json"
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        if ($data !== null) { 
            $jsonData = json_encode($data); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $response = curl_exec($ch); 
        if (curl_errno($ch)) { 
            echo "Error: " . curl_error($ch) . "\n";
        }
        curl_close($ch); 
        return $response; 
    }
}