from pathlib import Path

src = Path(r"C:\xampp\htdocs\erp-acshop\app\Http\Controllers\api\SupplierController.php").read_text(encoding="utf-8")
dst_path = Path(r"C:\xampp\htdocs\westoria-erp\app\Http\Controllers\api\SupplierController.php")
dst = dst_path.read_text(encoding="utf-8")

marker_start = "    public function fetch(Request $request)"
marker_end = "    public function importVendors(Request $request)"

start = src.find(marker_start)
end = src.find(marker_end)
if start < 0 or end < 0:
    raise SystemExit(f"extract markers missing start={start} end={end}")

gst_block = src[start:end]

dstart = dst.find(marker_start)
dend = dst.find(marker_end)
if dstart < 0 or dend < 0:
    raise SystemExit(f"dest markers missing dstart={dstart} dend={dend}")

new_dst = dst[:dstart] + gst_block + dst[dend:]
dst_path.write_text(new_dst, encoding="utf-8")
print("SupplierController GST methods replaced OK")
print("bytes", len(new_dst.encode("utf-8")))
