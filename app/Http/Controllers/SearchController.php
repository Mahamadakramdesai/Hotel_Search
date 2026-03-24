<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SearchService;
use App\Http\Requests\SearchRequest;

class SearchController extends Controller
{
    public function searchRoom(SearchRequest $request, SearchService $searchService)
    {

        $data = $request->validated();
        $result = $searchService->search($data);
        return response()->json($result);
        
    }
}
