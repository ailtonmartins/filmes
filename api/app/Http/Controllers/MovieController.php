<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
          
          $data = $request->all();
         
          $validator = Validator::make($data, [
               'name' => 'required',
               'file' => 'required | mimes:mpeg,mp4,mov,3gp,avi,wmv | max:5000'
          ],
          [
               'file.max' => 'The file may not be greater than 5MB'
          ]);

          if ($validator->fails()) {
                return response()->json([
                    'message'   => 'Validation Failed',
                    'errors'    => $validator->errors()->all()
                ], 422);
          }

          /** File action */
          $file = $request->file('file');
          $fileName = time().'.'.$file->getClientOriginalExtension();
          $destinationPath = public_path('/movie');
          $file->move($destinationPath, $fileName);
        
          $user = Auth::user();

          $movie = new Movie();
          $movie->fill($data);
          $movie->user = $user->id;
          $movie->file = $fileName;
          $movie->save();

        return response()->json( $movie, 200); 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function show(Movie $movie)
    {
        //
    }
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Movie $movie)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function destroy(Movie $movie)
    {
        //
    }
}
