<?

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    // Menampilkan semua items
    public function index()
    {
        $items = Item::all();
        return response()->json($items);
    }

    // Menambahkan item baru
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'image_url' => 'nullable|string|url', // Validasi untuk URL gambar
        ]);

        // Membuat item baru
        $item = Item::create($validated);
        return response()->json($item, 201); // Mengembalikan item yang dibuat dengan status 201
    }

    // Menampilkan item berdasarkan ID
    public function show($id)
    {
        $item = Item::findOrFail($id); // Jika tidak ditemukan, akan otomatis memunculkan error 404
        return response()->json($item);
    }

    // Memperbarui item berdasarkan ID
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'image_url' => 'nullable|string|url', // Validasi untuk URL gambar
        ]);

        // Update item
        $item->update($validated);
        return response()->json($item);
    }

    // Menghapus item berdasarkan ID
    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'Item deleted successfully']);
    }
}
