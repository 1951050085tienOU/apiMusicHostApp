<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage; //new

use App\Models\Song;
use App\Models\Singer;

class SongController extends Controller
{
    const publicPath = '/storage/';
    const songFolder = 'songs/';
    const lyricFolder = "lyrics/";
    

    public function index() {
        $songList = Song::where('verified', 1)->get();
        $songs = [];
        foreach ($songList as $songInfo) {
            array_push($songs, [
                 'id' => $songInfo->id,
                 'name' => $songInfo->name,
                 'path' => $songInfo->path,
                 'lyricPath' => $songInfo->lyricPath,
                 'singer' => Singer::where('id', $songInfo->singerId)->first(),
                 'verified' => $songInfo->verified,
                 'createdBy' => $songInfo->createdBy
            ]);
        }
        return response()->json($songs, 200);
    }

    public function find(Song $song) {
        return Song::where('id', $song->id)->first();
    }

    public function store() {
        request()->validate([
            'name' => 'required',
            'path' => 'required',
            'singerId' => 'required',
        ]);
    
        try {
            $song = new Song();
            $song->timestamps = false;
            $song->name = request('name');

            //save music file and save music path to $song object
            if (request('path')) {
                $ext = pathinfo(request('path')->getClientOriginalName(), PATHINFO_EXTENSION);
                $fileRename = rand().time();
                if (request('path')->move(public_path(SongController::publicPath . SongController::songFolder), $fileRename . '.' . 'mp4'))
                    //Storage::disk('public')->put(SongController::publicPath . SongController::songFolder . $fileRename . '.' . $ext, request('path')))
                //request('path')->storeAs(SongController::songFolder, $fileRename . '.' . $ext)) 
                {
                    $song->path = SongController::publicPath . SongController::songFolder . $fileRename . '.' . 'mp4';
                }
            }
            else {
                throw new \Nette\FileNotFoundException();
            }

            //save lyric and save path to $song object
            if (request('lyricPath')) {
                $fileRename = rand().time();
                if (request('path')->move(public_path(SongController::publicPath . SongController::lyricFolder), $fileRename . '.' . $ext))
                    //Storage::disk('public')->put(SongController::publicPath . SongController::lyricFolder . $fileRename . '.txt', request('lyricPath')))
                    //request('lyricPath')->storeAs(SongController::lyricFolder, $fileRename . '.txt')) 
                    {
                    $song->path = SongController::publicPath . SongController::lyricFolder . $fileRename . '.txt';
                }
            }

            $song->singerId = request('singerId');
            $song->verified = 0;
            $song->createdBy = auth()->user()->id;
            $song->save();
        } catch (\Exception $e) {
            return response()->json([
                'success'=> 'no'
            ], 500);
        }

        return response()->json([
            'success'=> 'yes'
        ], 201);
    }

    public function update(Song $song) {
        request()->validate([
            'name' => 'required'
        ]);
    
        try {
            $song->timestamps = false;
            
            $song->name = request('name');

            //delete old and save other lyricPath file
            if (request('lyricPath')) {
                $oldFileName = $song->lyricPath;

                $fileRename = rand().time();
                if (request('path')->move(public_path(SongController::publicPath . SongController::lyricFolder), $fileRename . '.' . $ext))
                    //Storage::disk('public')->put(SongController::publicPath . SongController::lyricFolder . $fileRename . '.txt', request('lyricPath')))
                    //request('lyricPath')->storeAs(SongController::lyricFolder, $fileRename . '.txt')) 
                    {
                    $song->path = SongController::publicPath . SongController::lyricFolder . $fileRename . '.txt';
                }

                if (File::exists(storage_Path() . $oldFileName)) {
                    File::delete(storage_Path() . $oldFileName);
                }
            }
            if (auth()->user()->role == 'Administrators') {   //verify for this song
                $song->verified = 1;
            }
            $song->save();
        } catch (\Exception $e) {
            $out = new \Symfony\Component\Console\Output\ConsoleOutput();
            $out->writeln($e->getMessage());
            return response()->json([
                'success'=> 'no'
            ], 500);
        }

        return response()->json([
            'success'=> 'yes'
        ], 200);
    }

    public function delete(Song $song) {
        try {
            $songFileName = storage_path() . $song->path;
                if (File::exists($songFileName)) {    
                File::delete($songFileName);
            }
            $songLyricFileName = storage_path() . $song->lyricPath;
            if (File::exists($songLyricFileName)) {
                File::delete($songLyricFileName);
            }
            $song->delete();
        } catch (\Exception $e) {
            return response()->json([
                'success'=> 'no'
            ], 500);
        }

        return response()->json([
            'success'=> 'yes'
        ], 200);
    }
}
