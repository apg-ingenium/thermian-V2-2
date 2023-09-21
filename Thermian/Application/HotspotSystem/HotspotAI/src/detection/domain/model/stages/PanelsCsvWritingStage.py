import csv
import io
from typing import List

from src.detection.domain.files.Csv import Csv
from src.detection.domain.files.FileCollection import FileCollection
from src.detection.domain.model.Stage import Stage
from src.detection.domain.model.models.shared.HotspotDetectionResults import HotspotDetectionResults


class PanelsCsvWritingStage(Stage):

    def process(self, dataset: None, results: List[HotspotDetectionResults]) -> List[FileCollection]:
        return list(map(self.__write, results))

    def __write(self, results: HotspotDetectionResults) -> FileCollection:
        panels_csv = io.StringIO()
        writer = csv.writer(panels_csv)

        writer.writerow(["panel_index", "score", "y_min", "x_min", "y_max", "x_max"])

        for panel in results.panels:
            writer.writerow([
                panel.id,
                f"{panel.score:.2f}",
                panel.box[0],
                panel.box[1],
                panel.box[2],
                panel.box[3]
            ])

        panels_csv.seek(0)
        panels_csv = Csv.from_string(panels_csv.read(), name="panels.csv")

        return FileCollection.containing(("panels", panels_csv))
