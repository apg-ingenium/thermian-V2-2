from typing import List


class Hotspot:

    def __init__(self, id: int, panel_id: int, score: float, box: List[int]):
        self.__id = id
        self.__panel_id = panel_id
        self.__score = score
        self.__box = box

    @property
    def id(self):
        return self.__id

    @property
    def panel_id(self):
        return self.__panel_id

    @property
    def score(self):
        return self.__score

    @property
    def box(self):
        return self.__box

    def __str__(self) -> str:
        return f"Hotspot(id: {self.id}, panel_id: {self.panel_id}, score: {self.score}, box: {self.box})"
