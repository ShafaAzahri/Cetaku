<?php
namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;


class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        // Query pencarian ke database items
        $results = DB::table('items')
            ->where('nama_item', 'like', '%' . $query . '%')
            ->get();

        return view('user.results', [
            'results' => $results,
            'query' => $query
    ]);
    }
}