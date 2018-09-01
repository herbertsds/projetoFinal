<?php

namespace App\Http\Controllers;

use App\DN;
use Illuminate\Http\Request;
use App\Exercicios;

class DNController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $exercicios = Exercicios::getExercicio($request);

        $dn = new DN();

        return Exercicios::converteSaida($dn->iniciarDN($exercicios));
    }

    public function step(Request $request){
        $dn = new DN();

        return $dn->step($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DN  $dN
     * @return \Illuminate\Http\Response
     */
    public function show(DN $dN)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DN  $dN
     * @return \Illuminate\Http\Response
     */
    public function edit(DN $dN)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DN  $dN
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DN $dN)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DN  $dN
     * @return \Illuminate\Http\Response
     */
    public function destroy(DN $dN)
    {
        //
    }
}
