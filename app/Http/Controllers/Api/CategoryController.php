<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $id = $request->input('id');
            // $limit = $request->input('limit', 6);
            $name = $request->input('name');

            $categories = Category::find($id);

            $categories = Category::query();

            if ($name) {
                $categories->where('name', 'like', '%' . $name . '%');
            }

            if ($categories == null || $categories->isEmpty()) {
                return ResponseFormatter::error([
                    null,
                    'message' => 'Category does not exist'
                ], 404);
            }

            return ResponseFormatter::success(
                $categories,
                'Category retrieved successfully'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Something went wrong', 500);
        }
    }

    public function create(): JsonResponse
    {
        try {
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Something went wrong', 500);
        }
    }
}
