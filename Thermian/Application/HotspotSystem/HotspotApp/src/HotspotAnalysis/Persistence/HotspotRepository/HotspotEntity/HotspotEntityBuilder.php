<?php
declare(strict_types=1);

namespace Hotspot\HotspotAnalysis\Persistence\HotspotRepository\HotspotEntity;

use Hotspot\HotspotAnalysis\Domain\Hotspot\HotspotId;
use RuntimeException;
use Shared\Domain\Uuid;

class HotspotEntityBuilder
{
    private int $index;
    private float $score;
    private int $xMin;
    private int $xMax;
    private int $yMin;
    private int $yMax;
    private ?Uuid $panelId;
    private ?HotspotId $id;

    public static function hotspot(): HotspotEntityBuilder
    {
        return new HotspotEntityBuilder();
    }

    public function __construct()
    {
        $this->id = null;
        $this->panelId = null;
    }

    public function withId(HotspotId $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function withIndex(int $index): self
    {
        $this->index = $index;

        return $this;
    }

    public function withScore(float $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function withPanelId(Uuid $panelId): self
    {
        $this->panelId = $panelId;

        return $this;
    }

    public function withXMin(int $xMin): self
    {
        $this->xMin = $xMin;

        return $this;
    }

    public function withXMax(int $xMax): self
    {
        $this->xMax = $xMax;

        return $this;
    }

    public function withYMin(int $yMin): self
    {
        $this->yMin = $yMin;

        return $this;
    }

    public function withYMax(int $yMax): self
    {
        $this->yMax = $yMax;

        return $this;
    }

    public function atPosition(int $x, int $y): self
    {
        $this->xMin = $x;
        $this->yMin = $y;

        return $this;
    }

    public function withSize(int $width, int $height): self
    {
        $this->xMax = $this->xMin + $width;
        $this->yMax = $this->yMin + $height;

        return $this;
    }

    public function build(): HotspotEntity
    {
        if (is_null($this->panelId)) {
            throw new RuntimeException('Panel id is missing');
        }

        return HotspotEntity::create(
            $this->panelId,
            $this->index,
            $this->score,
            $this->xMin,
            $this->xMax,
            $this->yMin,
            $this->yMax,
            $this->id
        );
    }
}
