<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\{BookResource, BookDetailResource};

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $books = Book::with('ratings')->offset($request->last_id ?? 0)->orderBy('created_at', 'DESC')->take(20)->get();

        return response()->json(BookResource::collection($books));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'pages' => 'required|numeric',
            'authors' => 'required|array',
            'isbn' => 'required|digits:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'invalid field',
                'errors' => $validator->errors()
            ], 422);
        }

        $authors = '';
        foreach ($request->authors as $author) {
            $authors .= $author.", ";
        }

        Auth::user()->books()->create([
            'title' => $request->title,
            'pages' => $request->pages,
            'authors' => $authors,
            'isbn' => $request->isbn
        ]);

        return response()->json(['message' => 'book created successfuly']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::with([
            'ratings',
            'reviews' => function($query) {
                $query->orderBy('updated_at', 'DESC');
            }
        ])->find($id);

        if (!$book) {
            return response()->json(['message' => 'book not found'], 404);
        }

        return response()->json(new BookDetailResource($book));
    }

    public function rating(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|numeric|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'ivalid field',
                'errors' => $validator->errors()
            ], 422);
        }

        Auth::user()->ratings()->create([
            'book_id' => $id,
            'rating' => $request->rating
        ]);

        return response()->json(['message' => 'Success'], 204);
    }

    public function review(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'review' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'ivalid field',
                'errors' => $validator->errors()
            ], 422);
        }

        Auth::user()->reviews()->create([
            'book_id' => $id,
            'review' => $request->review
        ]);

        return response()->json(['message' => 'Success'], 204);
    }
}
