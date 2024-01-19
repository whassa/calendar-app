<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventsUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Models\Events;

class EventsController extends Controller
{
    const COLORS = ['blue', 'yellow','green','red'];

    public function get($id): JsonResponse
    {
        try {
            $user_id = Auth::id();
            $events = Events::where('user_id', $user_id)->where('id',$id)->first();
            // I returned a custom json because date where not returned in a good format
            return response()->json([
                'id' => $events->id,
                'title' => $events->title,
                'description' => $events->description,
                'startingDate' => $events->startingDate->format('Y-m-d H:i'),
                'endingDate' => $events->endingDate->format('Y-m-d H:i'),
            ]);
        }
        catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['errors' => $e->errors()], 400);
        }
    }

    public function fetch(Request $request): JsonResponse
    {
        $id = Auth::id();
        $events = Events::where('user_id', $id)->get();
        $eventsArray = $events->toArray();


        $formattedEvents = collect($eventsArray)->map(function ($event, $key) {
            $startDate = \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $event['startingDate']);
            $endingDate = \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $event['endingDate']);
            return (object)[
                'id' => $event['id'],
                'title' => $event['title'],
                'description' => $event['description'],
                'time' => (object)[
                    'start' =>  $startDate->format('Y-m-d H:i'), 
                    'end' => $endingDate->format('Y-m-d H:i'),
                ],
                'color' => self::COLORS[$key % count(self::COLORS)],
                'isEditable' => true,
            ];
        });

        return response()->json($formattedEvents);
    }

    public function create(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'string',
                'startingDate' => 'required|date',
                'endingDate' => 'date|after_or_equal:startingDate',
            ]);

            $id = Auth::id();

            $endingDate = $request->has('endingDate') ? $request->input('endingDate') : $validatedData['startingDate'];

            $events = Events::create([
                'user_id' => $id,
                'title' => $request->title,
                'description' => $request->description,
                'startingDate' => $request->startingDate,
                'endingDate' => $endingDate,
            ]);
            

            return response()->json();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['errors' => $e->errors()], 400);
        }
    }

    //
    public function update(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'id' => 'numeric',
                'title' => 'required|string|max:255',
                'description' => 'string',
                'startingDate' => 'required|date',
                'endingDate' => 'date|after_or_equal:startingDate',
            ]);
            $events = Events::findOrFail($validatedData['id']);
            $events->update([
                'id' => $validatedData['id'],
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'startingDate' => $validatedData['startingDate'],
                'endingDate' => $validatedData['endingDate'],
            ]);
            return response()->json();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['errors' => $e->errors()], 400);
        }
    }


    public function delete(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'id' => ['required', 'numeric'],
            ]);
            $user_id = Auth::id();
            Events::where('id', $request->id)
                    ->where('user_id', $user_id)
                    ->delete();

            return response()->json();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json(['errors' => $e->errors()], 400);
        }
        
    }
}
