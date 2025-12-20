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
            <th>Link do participante</th>
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
                <td>
                    <a href="{{ url('/p/' . $p->hash1 . '/' . $p->hash2) }}"
                       target="_blank">
                        Abrir link
                    </a> | 
					<a target="_blank"
					   href="https://api.whatsapp.com/send/?phone={{ $p->phone }}&text=%F0%9F%8D%80%20Bol%C3%A3o%20Mega%20da%20Virada%202025%0A%0AAqui%20est%C3%A1%20seu%20link%20individual%20para%20acompanhar%20sua%20participa%C3%A7%C3%A3o%2C%20os%20jogos%20realizados%20e%20os%20comprovantes%3A%0A{{ url('/p/' . $p->hash1 . '/' . $p->hash2) }}&type=phone_number&app_absent=0">
						Enviar no WhatsApp
					</a>

                </td>
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
                <td colspan="6">Nenhum participante.</td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
