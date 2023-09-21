<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\UseCase\RemoveAllHotspotAnalyses;

use Cake\Database\Connection;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\MySQLHotspotAnalysisRepository;
use Hotspot\HotspotResults\Persistence\HotspotCsvRepository\MySQLHotspotCsvRepository;
use Hotspot\HotspotResults\Persistence\HotspotImageRepository\MySQLHotspotImageRepository;

class MySQLRemoveAllHotspotAnalysesTransaction implements RemoveAllHotspotAnalysesTransaction
{
    private Connection $connection;
    private MySQLHotspotAnalysisRepository $hotspotAnalysisRepository;
    private MySQLHotspotCsvRepository $hotspotCsvRepository;
    private MySQLHotspotImageRepository $hotspotImageRepository;

    public function __construct(
        Connection $connection,
        MySQLHotspotAnalysisRepository $hotspotAnalysisRepository,
        MySQLHotspotCsvRepository $hotspotCsvRepository,
        MySQLHotspotImageRepository $hotspotImageRepository
    ) {
        $this->hotspotAnalysisRepository = $hotspotAnalysisRepository;
        $this->hotspotCsvRepository = $hotspotCsvRepository;
        $this->hotspotImageRepository = $hotspotImageRepository;
        $this->connection = $connection;
    }

    public function execute(): void
    {
        $this->connection->transactional(function (Connection $connection) {
            $this->hotspotAnalysisRepository->removeAll();
            $this->hotspotImageRepository->removeAll();
            $this->hotspotCsvRepository->removeAll();
        });
    }
}
