@extends('admin.layout')

@section('title', 'Admin • Participantes')

@section('content')
<h2>Participantes</h2>

<p class="muted">
    Total arrecadado:
    <strong>R$ {{ number_format($total, 2, ',', '.') }}</strong>
</p>

{{-- Formulário: novo participante --}}
<h3>Novo participante</h3>

<form method="POST" action="{{ route('admin.participants.store') }}">
    @csrf
    <input type="text" name="name" placeholder="Nome" required>
    <input type="text" name="phone" placeholder="Telefone" required>
    <button type="submit">Adicionar</button>
</form>

<hr style="margin:2rem 0;">

{{-- Lista --}}
<table>
    <thead>
        <tr>
            <th>Nome</th>
            <th>Telefone</th>
            <th>Valor</th>
            <th>%</th>
            <th>Adicionar participação</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($participants as $p)
            @php
                $value = app(\App\Services\ParticipationService::class)
                    ->getParticipantCollectedValue($p->id);
                $percent = app(\App\Services\ParticipationService::class)
                    ->calculateParticipationPercent($p->id) * 100;
            @endphp
            <tr>
                <td>{{ $p->name }}</td>
                <td>{{ $p->phone }}</td>
                <td>R$ {{ number_format($value, 2, ',', '.') }}</td>
                <td>{{ number_format($percent, 2, ',', '.') }}%</td>
                <td>
                    <form method="POST" action="{{ route('admin.participations.store', $p->id) }}">
                        @csrf
                        <input type="number" name="quantity" min="1" style="width:70px;" required>
                    
                        <button type="submit">+</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">Nenhum participante.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
