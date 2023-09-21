<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository;

use DateTime;
use Hotspot\HotspotAnalysis\Domain\Hotspot\HotspotBuilder;
use Hotspot\HotspotAnalysis\Domain\Hotspot\HotspotId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\AnalysisId;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysis;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysisBuilder;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysisRecord;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysis\HotspotAnalysisRecordBuilder;
use Hotspot\HotspotAnalysis\Domain\HotspotAnalysisRepository\HotspotAnalysisRepository;
use Hotspot\HotspotAnalysis\Domain\HotspotRepository\HotspotRepository;
use Hotspot\HotspotAnalysis\Domain\Panel\PanelBuilder;
use Hotspot\HotspotAnalysis\Domain\PanelRepository\PanelRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisRecordSummary\AnalysisRecordSummary;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisRecordSummary\AnalysisRecordSummaryRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisSummary\AnalysisSummary;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\AnalysisSummary\AnalysisSummaryRepository;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\HotspotAnalysisEntity\HotspotAnalysisRecordEntity;
use Hotspot\HotspotAnalysis\Persistence\HotspotAnalysisRepository\HotspotAnalysisEntity\HotspotAnalysisRecordEntityBuilder;
use Hotspot\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity\HotspotEntityBuilder;
use Hotspot\HotspotAnalysis\Persistence\PanelRepository\PanelEntity\PanelEntity;
use Hotspot\HotspotAnalysis\Persistence\PanelRepository\PanelEntity\PanelEntityBuilder;
use Hotspot\HotspotDataset\Domain\Image\ImageId;
use Shared\Domain\Uuid;

class MySQLHotspotAnalysisRepository implements HotspotAnalysisRepository
{
    private AnalysisSummaryRepository $analysisSummaryRepository;
    private AnalysisRecordSummaryRepository $analysisRecordRepository;
    private PanelRepository $panelRepository;
    private HotspotRepository $hotspotRepository;

    public function __construct(
        AnalysisSummaryRepository $analysisSummaryRepository,
        AnalysisRecordSummaryRepository $imageAnalysisRepository,
        PanelRepository $panelRepository,
        HotspotRepository $hotspotRepository
    ) {
        $this->analysisSummaryRepository = $analysisSummaryRepository;
        $this->analysisRecordRepository = $imageAnalysisRepository;
        $this->panelRepository = $panelRepository;
        $this->hotspotRepository = $hotspotRepository;
    }

    public function saveAnalysis(HotspotAnalysis $analysis): void
    {
        $analysisSummary = AnalysisSummary::create(
            $analysis->getId(),
            $analysis->getTarget(),
            $analysis->getCreationDate(),
            $analysis->getNumRecords(),
            $analysis->getNumPanels(),
            $analysis->getNumHotspots(),
        );

        $panels = [];
        $hotspots = [];
        $recordStats = [];

        foreach ($analysis->getRecords() as $record) {
            $recordEntity = $this->mapToRecordEntity($record);
            $recordStats[] = AnalysisRecordSummary::create(
                $record->getAnalysisId(),
                $record->getImageId(),
                $record->getImageName(),
                $record->getNumPanels(),
                $record->getNumHotspots(),
                $record->getGpsCoordinates()
            );
            array_push($panels, ...array_values($recordEntity->getPanels()));
            array_push($hotspots, ...array_values($recordEntity->getHotspots()));
        }

        $this->analysisSummaryRepository->save($analysisSummary);
        $this->analysisRecordRepository->saveAll($recordStats);
        $this->panelRepository->saveAll($panels);
        $this->hotspotRepository->saveAll($hotspots);
    }

    public function findAnalysisById(AnalysisId $analysisId): ?HotspotAnalysis
    {
        $analysisDetails = $this->analysisSummaryRepository->findById($analysisId);

        if (is_null($analysisDetails)) {
            return null;
        }

        $analysisRecordSummaries = $this->analysisRecordRepository->findByAnalysisId($analysisId);
        $panelEntities = $this->panelRepository->findByAnalysisId($analysisId);
        $panelIds = array_map(fn(PanelEntity $p) => $p->getId(), $panelEntities);
        $hotspotEntities = $this->hotspotRepository->findByMultiplePanelIds($panelIds);

        $analysis = HotspotAnalysisBuilder
            ::hotspotAnalysis()
            ->withAnalysisId($analysisId)
            ->withTarget($analysisDetails->getTarget());

        $records = [];
        foreach ($analysisRecordSummaries as $recordSummary) {
            $records[$recordSummary->getImageId()->value()] = HotspotAnalysisRecordBuilder
                ::hotspotAnalysisRecord()
                ->withAnalysisId($recordSummary->getAnalysisId())
                ->withImageId($recordSummary->getImageId())
                ->withImageName($recordSummary->getImageName())
                ->withGpsCoordinates($recordSummary->getCoordinates());
        }

        $panelToRecordMap = [];
        foreach ($panelEntities as $panelEntity) {
            $recordId = $panelEntity->getImageId()->value();
            $records[$recordId]->withPanel(
                PanelBuilder::panel()
                    ->withIndex($panelEntity->getIndex())
                    ->withScore($panelEntity->getScore())
                    ->withYMin($panelEntity->getYMin())
                    ->withXMin($panelEntity->getXMin())
                    ->withYMax($panelEntity->getYMax())
                    ->withXMax($panelEntity->getXMax())
                    ->build()
            );
            $panelToRecordMap[$panelEntity->getId()->value()] = [
                'image_id' => $recordId,
                'panel_index' => $panelEntity->getIndex(),
            ];
        }

        foreach ($hotspotEntities as $hotspotEntity) {
            $panelId = $hotspotEntity->getPanelId()->value();
            $recordId = $panelToRecordMap[$panelId]['image_id'];
            $panelIndex = $panelToRecordMap[$panelId]['panel_index'];
            $records[$recordId]->withHotspot(
                HotspotBuilder::hotspot()
                    ->withIndex($hotspotEntity->getIndex())
                    ->withPanelIndex($panelIndex)
                    ->withScore($hotspotEntity->getScore())
                    ->withYMin($hotspotEntity->getYMin())
                    ->withXMin($hotspotEntity->getXMin())
                    ->withYMax($hotspotEntity->getYMax())
                    ->withXMax($hotspotEntity->getXMax())
                    ->build()
            );
        }

        foreach ($records as $record) {
            $analysis->withRecord($record->build());
        }

        return $analysis->build();
    }

    public function removeAnalysisById(AnalysisId $analysisId): void
    {
        $this->analysisSummaryRepository->removeById($analysisId);
    }

    public function saveAnalysisRecord(HotspotAnalysisRecord $analysisRecord): void
    {
        $recordEntity = $this->mapToRecordEntity($analysisRecord);

        $this->analysisSummaryRepository->save(
            AnalysisSummary::create(
                $recordEntity->getAnalysisId(),
                $recordEntity->getImageName(),
                new DateTime(),
                1,
                $recordEntity->getNumPanels(),
                $recordEntity->getNumHotspots()
            )
        );

        $this->analysisRecordRepository->save(
            AnalysisRecordSummary::create(
                $recordEntity->getAnalysisId(),
                $recordEntity->getImageId(),
                $recordEntity->getImageName(),
                $recordEntity->getNumPanels(),
                $recordEntity->getNumHotspots(),
                $recordEntity->getGpsCoordinates()
            )
        );

        $this->panelRepository->saveAll(
            $recordEntity->getPanels()
        );

        $this->hotspotRepository->saveAll(
            $recordEntity->getHotspots()
        );
    }

    private function mapToRecordEntity(HotspotAnalysisRecord $analysisRecord): HotspotAnalysisRecordEntity
    {
        $analysisId = $analysisRecord->getAnalysisId();
        $imageId = $analysisRecord->getImageId();
        $imageName = $analysisRecord->getImageName();
        $gpsCoordinates = $analysisRecord->getGpsCoordinates();

        $panels = [];
        foreach ($analysisRecord->getPanels() as $panel) {
            $panels[$panel->getIndex()] =
                PanelEntityBuilder::panel()
                    ->withId(Uuid::random())
                    ->withAnalysisId($analysisId)
                    ->withImageId($imageId)
                    ->withIndex($panel->getIndex())
                    ->withScore($panel->getScore())
                    ->withYMin($panel->getYMin())
                    ->withXMin($panel->getXMin())
                    ->withYMax($panel->getYMax())
                    ->withXMax($panel->getXMax())
                    ->build();
        }

        $hotspots = [];
        foreach ($analysisRecord->getHotspots() as $hotspot) {
            $hotspots[] =
                HotspotEntityBuilder::hotspot()
                    ->withId(HotspotId::random())
                    ->withPanelId($panels[$hotspot->getPanelIndex()]->getId())
                    ->withIndex($hotspot->getIndex())
                    ->withScore($hotspot->getScore())
                    ->withYMin($hotspot->getYMin())
                    ->withXMin($hotspot->getXMin())
                    ->withYMax($hotspot->getYMax())
                    ->withXMax($hotspot->getXMax())
                    ->build();
        }

        return HotspotAnalysisRecordEntityBuilder::hotspotAnalysisRecord()
            ->withAnalysisId($analysisId)
            ->withImageId($imageId)
            ->withImageName($imageName)
            ->withPanels($panels)
            ->withHotspots($hotspots)
            ->withGpsCoordinates($gpsCoordinates)
            ->build();
    }

    public function containsAnalysisRecordId(AnalysisId $analysisId, ImageId $imageId): bool
    {
        return $this->analysisRecordRepository
            ->containsAnalysisRecordId($analysisId, $imageId);
    }

    public function findAnalysisRecordById(AnalysisId $analysisId, ImageId $imageId): ?HotspotAnalysisRecord
    {
        $recordEntity = $this->findRecordEntityById($analysisId, $imageId);

        return is_null($recordEntity) ? null : $this->mapToRecord($recordEntity);
    }

    private function findRecordEntityById(AnalysisId $analysisId, ImageId $imageId): ?HotspotAnalysisRecordEntity
    {
        $recordSummary = $this->analysisRecordRepository->findByRecordId($analysisId, $imageId);
        if (is_null($recordSummary)) {
            return null;
        }

        $panels = $this->panelRepository->findByRecordId($analysisId, $imageId);
        $panelIds = array_map(fn(PanelEntity $panel) => $panel->getId(), $panels);
        $hotspots = $this->hotspotRepository->findByMultiplePanelIds($panelIds);

        return HotspotAnalysisRecordEntityBuilder
            ::hotspotAnalysisRecord()
            ->withAnalysisId($analysisId)
            ->withImageId($imageId)
            ->withImageName($recordSummary->getImageName())
            ->withGpsCoordinates($recordSummary->getCoordinates())
            ->withPanels($panels)
            ->withHotspots($hotspots)
            ->build();
    }

    private function mapToRecord(HotspotAnalysisRecordEntity $analysisRecordEntity): HotspotAnalysisRecord
    {
        $panels = [];
        foreach ($analysisRecordEntity->getPanels() as $panel) {
            $panelId = $panel->getId()->value();
            $panels[$panelId] =
                PanelBuilder::panel()
                    ->withIndex($panel->getIndex())
                    ->withScore($panel->getScore())
                    ->withYMin($panel->getYMin())
                    ->withXMin($panel->getXMin())
                    ->withYMax($panel->getYMax())
                    ->withXMax($panel->getXMax())
                    ->build();
        }

        $hotspots = [];
        foreach ($analysisRecordEntity->getHotspots() as $hotspot) {
            $panelId = $hotspot->getPanelId()->value();
            $panel = $panels[$panelId];
            $hotspots[] =
                HotspotBuilder::hotspot()
                    ->withPanelIndex($panel->getIndex())
                    ->withIndex($hotspot->getIndex())
                    ->withScore($hotspot->getScore())
                    ->withYMin($hotspot->getYMin())
                    ->withXMin($hotspot->getXMin())
                    ->withYMax($hotspot->getYMax())
                    ->withXMax($hotspot->getXMax())
                    ->build();
        }

        return HotspotAnalysisRecordBuilder
            ::hotspotAnalysisRecord()
            ->withAnalysisId($analysisRecordEntity->getAnalysisId())
            ->withImageId($analysisRecordEntity->getImageId())
            ->withImageName($analysisRecordEntity->getImageName())
            ->withGpsCoordinates($analysisRecordEntity->getGpsCoordinates())
            ->withPanels($panels)
            ->withHotspots($hotspots)
            ->build();
    }

    public function removeAnalysisRecordById(AnalysisId $analysisId, ImageId $imageId): void
    {
        $panelIds = $this->panelRepository->findPanelIdsByRecordId($analysisId, $imageId);
        $this->hotspotRepository->removeByMultiplePanelIds($panelIds);
        $this->panelRepository->removeByRecordId($analysisId, $imageId);
        $this->analysisRecordRepository->removeByAnalysisRecordId($analysisId, $imageId);
    }

    public function removeAll(): void
    {
        $this->hotspotRepository->removeAll();
        $this->panelRepository->removeAll();
        $this->analysisRecordRepository->removeAll();
        $this->analysisSummaryRepository->removeAll();
    }
}
