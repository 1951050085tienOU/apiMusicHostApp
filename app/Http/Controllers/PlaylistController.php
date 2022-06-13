<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Playlist;
use App\Models\Song;
use App\Models\Singer;
use Illuminate\Database\Eloquent\Collection;

class PlaylistController extends Controller
{
    public function index() {
        $playlists = Playlist::where('userId', auth()->user()->id)->select('id')->distinct()->get('id');
        $result = [];
        foreach ($playlists as $p) {
            //array_push($result, Playlist::where('id', $p->id)->first());
            $playlistInfo = Playlist::where('id', $p->id)->first();
            array_push($result, [
                'id' => $playlistInfo->id,
                'name' => $playlistInfo->name,
                'songs' => [],
                'createdDate' => $playlistInfo->createdDate,
                'userId' => $playlistInfo->userId
            ]);
        }
        return $result;
    }

    public function find(Playlist $playlist) {
        $playlist = Playlist::where('id', $playlist->id)->get();
        $songs = [];
        $playlistInfo = $playlist[0];
        foreach ($playlist as $song) {
            //array_push($songs, Song::where('id', $song->songId)->first());
            $songInfo = Song::where('id', $song->songId)->first();
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
        
        return response()->json([
            'id' => $playlistInfo->id,
            'name' => $playlistInfo->name,
            'songs' => $songs,
            'createdDate' => $playlistInfo->createdDate,
            'userId' => $playlistInfo->userId
        ], 200);
    }

    public function store() {
        request()->validate([
            'name' => 'required',
            'songId' => 'required'
        ]);

        try {
            $playlist = new Playlist();
            $playlist->timestamps = false;
            $lastId = Playlist::select('id')->distinct()->orderBy('id', 'desc')->first();
            $playlist->id = $lastId ? $lastId->id + 1 : 1;
            $playlist->name = request('name');
            $playlist->songId = request('songId');
            $playlist->createdDate = date('Y-m-d');
            $playlist->userId = auth()->user()->id;
            $playlist->save();
        } catch (\Exception $e) {
            return response()->json([
                'success'=> 'no'
            ], 500);
        }

        return response()->json([
            'success'=> 'yes'
        ], 201);
    }

    public function update(Playlist $playlist) {
        request()->validate([
            'name' => 'required'
        ]);
        $selected = Playlist::find($playlist);
        try {
            foreach ($selected as $row) {
                $row->timestamps = false;
                $row->update([
                    "name" => request('name')
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success'=> 'no'
            ], 500);
        }

        return response()->json([
            'success'=> 'yes'
        ], 200);
    }

    public function storeASong(Playlist $playlist, Song $song) {
        try {
            $playlistAllSong = Playlist::where('id', $playlist->id)->get();
            foreach ($playlistAllSong as $songIn) {
                if ($songIn->songId == $song->id) {
                    throw new \Exception('This item has been existed in the playlist.');
                }
            }
            $selected = Playlist::where('id', $playlist->id)->first();
            $new = new Playlist();
            $new->timestamps = false;
            $new->id = $selected->id;
            $new->name = $selected->name;
            $new->userId = $selected->userId;
            $new->createdDate = date('Y-m-d');
            $new->songId = $song->id;
            $new->save();
        } catch (\Exception $e) {
            return response()->json([
                'success'=> 'no'
            ], 500);
        }

        return response()->json([
            'success'=> 'yes'
        ], 200);
    }

    public function deleteASong(Playlist $playlist, Song $song) {
        try {
            $selected = Playlist::where('songId', $song->id)->where('id', $playlist->id)->delete();
        } catch (\Exception $e) {
            return response()->json([
                'success'=> 'no'
            ], 500);
        }

        return response()->json([
            'success'=> 'yes'
        ], 200);
    }

    public function delete(Playlist $playlist) {
        try {
            $playlist->delete();
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
