from dataclasses import dataclass
from typing import List, Optional

import io

import csv

from src.detection.domain.files.Csv import Csv
from src.detection.domain.files.FileCollection import FileCollection
from src.detection.domain.files.Image import Image
from src.detection.domain.model.Stage import Stage


@dataclass(frozen=True)
class Longitude:
    degrees: int
    minutes: int
    seconds: float
    direction: str

    def __init__(self, degrees: int, minutes: int, seconds: float, direction: str):
        if direction.lower() not in ("e", "east", "w", "west"):
            raise ValueError(f"invalid longitude direction: {direction}")
        object.__setattr__(self, 'degrees', int(degrees))
        object.__setattr__(self, 'minutes', int(minutes))
        object.__setattr__(self, 'seconds', seconds)
        object.__setattr__(self, 'direction', direction)


@dataclass(frozen=True)
class Latitude:
    degrees: int
    minutes: int
    seconds: float
    direction: str

    def __init__(self, degrees: int, minutes: int, seconds: float, direction: str):
        if direction.lower() not in ("n", "north", "s", "south"):
            raise ValueError(f"invalid latitude direction: {direction}")
        object.__setattr__(self, 'degrees', int(degrees))
        object.__setattr__(self, 'minutes', int(minutes))
        object.__setattr__(self, 'seconds', seconds)
        object.__setattr__(self, 'direction', direction)


@dataclass(frozen=True)
class SphericalCoordinates:
    latitude: Latitude
    longitude: Longitude


class GpsCoordinateExtractionStage(Stage):

    def process(self, dataset: List[Image], results: None) -> List[FileCollection]:
        return [self.__write(self.__extract_gps_coordinates(image)) for image in dataset]

    @staticmethod
    def __extract_gps_coordinates(image: Image) -> Optional[SphericalCoordinates]:
        gps_info = image.to_PIL_image()._getexif()
        gps_info = gps_info and gps_info.get(0x8825, None)

        return SphericalCoordinates(
            Latitude(
                degrees=gps_info[2][0],
                minutes=gps_info[2][1],
                seconds=gps_info[2][2],
                direction=gps_info[1]
            ),
            Longitude(
                degrees=gps_info[4][0],
                minutes=gps_info[4][1],
                seconds=gps_info[4][2],
                direction=gps_info[3]
            ),
        ) if gps_info else None

    @staticmethod
    def __write(coordinates: Optional[SphericalCoordinates]) -> FileCollection:
        gps_csv = io.StringIO()
        writer = csv.writer(gps_csv)

        writer.writerow([
            "lat_degrees", "lat_minutes", "lat_seconds", "lat_direction",
            "long_degrees", "long_minutes", "long_seconds", "long_direction"
        ])

        if coordinates is not None:
            latitude = coordinates.latitude
            longitude = coordinates.longitude
            writer.writerow([
                latitude.degrees, latitude.minutes, latitude.seconds, latitude.direction,
                longitude.degrees, longitude.minutes, longitude.seconds, longitude.direction,
            ])

        gps_csv.seek(0)
        return FileCollection.containing(("gps_csv", Csv.from_string(gps_csv.read(), name="gps.csv")))
