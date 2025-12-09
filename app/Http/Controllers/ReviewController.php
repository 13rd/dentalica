<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Appointment $appointment)
    {
        if ($appointment->patient_id !== auth()->id() || $appointment->status !== 'completed') {
            abort(403);
        }

        if ($appointment->review) {
            return back()->withErrors('Review already exists');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
        ]);

        $review = Review::create([
            'appointment_id' => $appointment->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);


        $doctor = $appointment->doctor;
        $doctor->rating = $doctor->appointments()->has('review')->average('review.rating');
        $doctor->save();

        return back()->with('success', 'Review added');
    }
};
