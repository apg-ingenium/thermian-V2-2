import csv
import io
from typing import List

from src.detection.domain.files.Csv import Csv
from src.detection.domain.files.FileCollection import FileCollection
from src.detection.domain.model.Stage import Stage
from src.detection.domain.model.models.shared.HotspotDetectionResults import HotspotDetectionResults


class HotspotsCsvWritingStage(Stage):

    def process(self, dataset: None, results: List[HotspotDetectionResults]) -> List[FileCollection]:
        return list(map(self.__write, results))

    def __write(self, results: HotspotDetectionResults) -> FileCollection:
        hotspot_csv = io.StringIO()
        writer = csv.writer(hotspot_csv)

        writer.writerow(["hotspot_index", "panel_index", "score", "y_min", "x_min", "y_max", "x_max"])

        for hotspot in results.hotspots:
            writer.writerow([
                hotspot.id,
                hotspot.panel_id,
                f"{hotspot.score:.2f}",
                hotspot.box[0],
                hotspot.box[1],
                hotspot.box[2],
                hotspot.box[3]
            ])

        hotspot_csv.seek(0)
        hotspot_csv = Csv.from_string(hotspot_csv.read(), name="hotspots.csv")

        return FileCollection.containing(("hotspots", hotspot_csv))
