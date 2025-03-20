@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2>Tableau de Rendement des Formateurs</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Mle Formateur</th>
                                    <th>Nom & Prénom Formateur</th>
                                    <th>MHP Totale</th>
                                    <th>MHSYN Totale</th>
                                    <th>MH Totale (1)</th>
                                    <th>MHP Réalisée</th>
                                    <th>MHSYN Réalisée</th>
                                    <th>MH Totale Réalisée (2)</th>
                                    <th>Rendement en % (2/1)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($formateurs as $formateur)
                                <tr>
                                    <td>{{ $formateur->{'Mle Formateur'} }}</td>
                                    <td>{{ $formateur->{'Nom & Prénom Formateur'} }}</td>
                                    <td>{{ $formateur->{'MHP Totale'} }}</td>
                                    <td>{{ $formateur->{'MHSYN Totale'} }}</td>
                                    <td>{{ $formateur->{'MH Totale (1)'} }}</td>
                                    <td>{{ $formateur->{'MHP Réalisée'} }}</td>
                                    <td>{{ $formateur->{'MHSYN Réalisée'} }}</td>
                                    <td>{{ $formateur->{'MH Totale Réalisée (2)'} }}</td>
                                    <td>
                                        <span class="badge {{ $formateur->{'Rendement en % (2/1)'} >= 100 ? 'bg-success' : ($formateur->{'Rendement en % (2/1)'} >= 80 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $formateur->{'Rendement en % (2/1)'} }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection