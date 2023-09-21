<?php
declare(strict_types=1);

namespace Hotspot\HotspotDetection\Domain;

use Cake\Http\Client;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Shared\Domain\Uuid;

class HotspotAnalyzer
{
    private Client $http;
    private string $modelName;

    /** @var array<string, mixed> */
    private array $modelConfig;

    /**
     * @param string $modelName
     * @param array<string, mixed> $modelConfig
     */
    public function __construct(string $modelName, array $modelConfig = [])
    {
        $this->modelName = $modelName;
        $this->modelConfig = $modelConfig;
        $this->http = new Client([
            'scheme' => 'http',
            'host' => env('HOTSPOT_AI_HOST'),
            'port' => 81,
            'timeout' => null,
        ]);
    }

    public function analyze(string $analysisId, string $imageId): void
    {
        $response = $this->http->post(
            '/hotspots/analysis',
            json_encode([
                'analysis_id' => $analysisId,
                'image_id' => $imageId,
                'model_name' => $this->modelName,
                'model_config' => $this->modelConfig,
            ]),
            ['type' => 'json']
        );
    }

    public function analyzeDataset(AnalysisId $analysisId, Uuid $datasetId): void
    {
        $response = $this->http->post(
            '/hotspots/analysis',
            json_encode([
                'analysis_id' => $analysisId->value(),
                'dataset_id' => $datasetId->value(),
                'model_name' => $this->modelName,
                'model_config' => $this->modelConfig,
            ]),
            ['type' => 'json']
        );
    }
}
