namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\TravelPackage;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $query = TravelPackage::with(['category', 'schedules']);

        // Filter berdasarkan kata kunci nama atau lokasi
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan Kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter berdasarkan Batas Maksimal Harga
        if ($request->filled('max_price')) {
            $query->whereHas('schedules', function ($q) use ($request) {
                $q->where('price_per_person', '<=', $request->max_price);
            });
        }

        $packages = $query->latest()->paginate(9)->withQueryString();
        $categories = Category::all();

        return view('packages.index', compact('packages', 'categories'));
    }

    public function show($slug)
    {
        $package = TravelPackage::with(['category', 'schedules' => function($q) {
            $q->where('departure_date', '>=', now());
        }])->where('slug', $slug)->firstOrFail();

        return view('packages.show', compact('package'));
    }
}