<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Singer;
use Illuminate\Database\DBAL\TimestampType;

class SingerController extends Controller
{
    public function index() {
        return Singer::all();
    }

    public function find(Singer $singer) {
        return Singer::find($singer);
    }

    public function store() {
        request()->validate([
            'name' => 'required'
        ]);
    
        try {
            $singer = new Singer();
            $singer->timestamps = false;
            $singer->name = request('name');
            $singer->description = request('description');
            $singer->save();
        } catch (\Exception $e) {
            $out = new \Symfony\Component\Console\Output\ConsoleOutput();
            $out->writeln($e->getMessage());
            return response()->json([
                'success'=> 'no'
            ], 500);
        }
    
        return response()->json([
            'success'=> 'yes'
        ], 201);
    }

    public function update(Singer $singer) {
        request()->validate([
            'name' => 'required'
        ]);
    
        try {
            $singer->timestamps = false;
            $singer->update([
                "name" => request('name'),
                "description" => request('description')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'=> 'no'
            ], 500);
        }

        return response()->json([
            'success'=> 'yes'
        ], 200);
    }

    public function delete(Singer $singer) {
        try {
            $singer->delete();
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
