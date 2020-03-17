<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Tag;
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
    public function index( Request $data )
    {
        $size = 5;
        $dir = isset($data->dir) ? $data->dir : 'asc';
        $prop = isset($data->prop) ?  $data->prop : 'id';
        $search = $data->search ?  $data->search : '';

        $validator = Validator::make(['dir'=>$dir,'prop'=>$prop], [
               'dir' => 'in:asc,desc',
               'prop' =>  'in:id,name'
             ],
             [
               'dir.in' => 'The dir field only accepts values asc and desc',
               'prop.in' => 'The prop field only accepts values id and name'
             ]);

        if ($validator->fails()) {
                return response()->json([
                    'message'   => 'Validation Failed',
                    'errors'    => $validator->errors()->all()
                ], 422);
        }

        $movies = Movie::with("tags");

        if( $search != '' ) {
          $movies->orWhere('movies.id', 'like', '%' . $search . '%');           
          $movies->orWhere('movies.name', 'like', '%' . $search . '%');
          $movies->orWhereHas('tags', function ($query) use( $search ) {
                                                           $query->where( 'tags.name', 'like', '%' . $search . '%');
                                                        });
        }
       
        $movies->orderBy($prop, $dir);

        return $movies->paginate($size);
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
          $movie->file_size = $file->getSize();
          $movie->save();


          if ( !empty($data['tags'])  ) {
            $tags = explode(';' , $data['tags']);
            foreach( $tags as $v ) {
                $valueTag = trim($v);
                if ( !empty($v) ) {
                    $tag = new Tag();
                    $tag->name = $v;
                    $tag->movie = $movie->id;
                    $tag->save();
                }
               
            }
          }
          
        return $this->show( $movie->id ); 
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function show( $id )
    {
        $movie = Movie::with(['tags'])->find(  $id );
        if ( $movie ) {
            return response()->json( $movie , 200);
        } else {
            return response()->json([
                'message'   => 'Movie not found',
            ], 404);
        }
    }
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Movie  $movie
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       $data = $request->all(); 

       $validator = Validator::make($data, [
                    'name' => 'required',
                    'file' => 'mimes:mpeg,mp4,mov,3gp,avi,wmv | max:5000'
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

       $movie = Movie::find(  $id );

       if ( $movie ) {
            Tag::where('movie', $id)->delete();          
            if ( !empty($data['tags'])  ) {
                    $tags = explode(';' , $data['tags']);                   
                    foreach( $tags as $v ) {
                        $valueTag = trim($v);
                        if ( !empty($v) ) {
                            $tag = new Tag();
                            $tag->name = $v;
                            $tag->movie = $movie->id;
                            $tag->save();
                        }
                    
                    }
            }
            
            /** File action */
            if ( $request->file('file') ) {
                $file = $request->file('file');
                $fileName = time().'.'.$file->getClientOriginalExtension();
                $destinationPath = public_path('/movie');
                $file->move($destinationPath, $fileName);
                $movie->file = $fileName;
                $movie->file_size = $file->getSize();
            }
                       
            $movie->fill($data);           
            $movie->save();

          return $this->show( $id );
       } else {
          return response()->json([
             'message'   => 'Movie not found',
          ], 404);
       }
       
     return response()->json( $movie , 200);
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


 