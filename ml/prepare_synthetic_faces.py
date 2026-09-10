from pathlib import Path

from PIL import Image


SOURCE_DIR = Path(__file__).with_name("synthetic_face_sheets")
OUTPUT_DIR = Path(__file__).with_name("synthetic_face_dataset")


def split_sheet(source: Path, employee_id: str) -> None:
    image = Image.open(source)
    tile_width, tile_height = image.width // 5, image.height // 2
    employee_dir = OUTPUT_DIR / employee_id
    employee_dir.mkdir(parents=True, exist_ok=True)

    for index in range(10):
        column, row = index % 5, index // 5
        tile = image.crop((
            column * tile_width,
            row * tile_height,
            (column + 1) * tile_width,
            (row + 1) * tile_height,
        ))
        tile.save(employee_dir / f"{index + 1:02}.jpg", quality=95)


if __name__ == "__main__":
    sheets = sorted(SOURCE_DIR.glob("*.png"))
    assert len(sheets) == 3, f"Expected 3 sheets, found {len(sheets)}"
    for number, sheet in enumerate(sheets, start=1):
        split_sheet(sheet, f"EMP{number:03}")
    assert len(list(OUTPUT_DIR.glob("EMP*/*.jpg"))) == 30
    print(f"Created 30 images in {OUTPUT_DIR}")
