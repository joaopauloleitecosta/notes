<?php

namespace App\Http\Controllers;

use App\Note;
use App\Services\Operations;
use App\User;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index() {
        //load user's notes
        $id = session('user.id');
        $notes = User::find($id)
                    ->notes()
                    ->whereNull('deleted_at')
                    ->get()
                    ->toArray();

        //show home view
        return view('home', ['notes' => $notes]);
    }

    public function newNote() {
        //show new note
        return view('new_note');
    }

    public function newNoteSubmit(Request $request) {
        //validate request
        $request->validate(
            //rules
            [
                'text_title' => 'required|min:3|max:200',
                'text_note' => 'required|min:3|max:3000'
            ],
            //error messages
            [
                'text_title.required' => 'Título é obrigatório',
                'text_title.min'      => 'Titulo deve ter pelo menos :min caracteres',
                'text_title.max'      => 'Título deve possuir no máximo :max caracteres',
                
                'text_note.required'  => 'Nota é obrigatória',
                'text_note.min'       => 'Nota deve ter pelo menos :min caracteres',
                'text_note.max'       => 'Nota deve possuir no máximo :max caracteres',

            ]
        );

        //get user id
        $id = session('user.id');

        //create new note
        $note = new Note();
        $note->user_id = $id;
        $note->title   = $request->text_title;
        $note->text    = $request->text_note;
        $note->save();

        //redirect to home
        return redirect()->route('home');
    }

    public function editNote($id) {
        $id = Operations::decryptId($id);

        if($id === null) {
            return redirect()->route('home');
        }

        //load note
        $note = Note::find($id);

        //show edit note view
        return view('edit_note', ['note' => $note]);
    }

    public function editNoteSubmit(Request $request)
    {
        //validate request
        $request->validate(
            //rules
            [
                'text_title' => 'required|min:3|max:200',
                'text_note' => 'required|min:3|max:3000'
            ],
            //error messages
            [
                'text_title.required' => 'Título é obrigatório',
                'text_title.min'      => 'Titulo deve ter pelo menos :min caracteres',
                'text_title.max'      => 'Título deve possuir no máximo :max caracteres',
                
                'text_note.required'  => 'Nota é obrigatória',
                'text_note.min'       => 'Nota deve ter pelo menos :min caracteres',
                'text_note.max'       => 'Nota deve possuir no máximo :max caracteres',

            ]
        );

        //check if id not exist
        if($request->note_id == null) {
            return redirect()->route('home');
        }

        //decrypt note id
        $id = Operations::decryptId($request->note_id);

        if($id === null) {
            return redirect()->route('home');
        }

        //load note
        $note = Note::find($id);

        //update note
        $note->title = $request->text_title;
        $note->text = $request->text_note;
        $note->save();

        return redirect()->route('home');
    }

    public function deleteNote($id) {
        
        $id = Operations::decryptId($id);

        if($id === null) {
            return redirect()->route('home');
        }
        
        //load note
        $note = Note::find($id);

        //show delete note confirmation
        return view('delete_note', ['note' => $note]);
    }

    public function deleteNoteConfirm($id)
    {
        // check if $id encrypt
        $id = Operations::decryptId($id);

        if($id === null) {
            return redirect()->route('home');
        }
        
        //load note
        $note = Note::find($id);

        //hard delete (property SoftDeletes in model)
        $note->forceDelete();

        //soft delete (property SoftDeletes in model)
        $note->delete();

        //redirect to home
        return redirect()->route('home');

    }


}
