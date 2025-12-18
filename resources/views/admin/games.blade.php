@extends('admin.layout')

@section('title', 'Admin • Jogos')

@section('content')
    <h2>Jogos Realizados</h2>

    @if ($games->isEmpty())
        <p>Nenhum jogo registrado.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Números</th>
                    <th>Tamanho</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Comprovante</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($games as $game)
                    <tr>
                        <td>{{ $game->id }}</td>

                        <td class="muted">
                            @php
                                $numbers = app(\App\Services\GameService::class)
                                    ->getGameNumbers($game->id);
                            @endphp
                            {{ implode(', ', $numbers) }}
                        </td>

                        <td>{{ $game->game_size }}</td>
                        <td>R$ {{ number_format($game->total_value, 2, ',', '.') }}</td>

                        <td>
                            @if ($game->status === 'confirmed')
                                ✅ Confirmado
                            @else
                                🕓 Simulado
                            @endif
                        </td>

                        <td>
                            @php
                                $receipt = app(\App\Services\ReceiptService::class)
                                    ->getReceiptByGame($game->id);
                            @endphp

                            @if ($receipt)
                                <a href="{{ asset('storage/' . $receipt->file_path) }}" target="_blank">
                                    Ver comprovante
                                </a>
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>

                        <td>
                            @if ($game->status !== 'confirmed')
                                <form method="POST"
                                      action="{{ route('admin.games.confirm', $game->id) }}"
                                      style="display:inline;">
                                    @csrf
                                    <button type="submit">Confirmar</button>
                                </form>
                            @endif

                            @if ($game->status === 'confirmed' && !$receipt)
                                <form method="POST"
                                      action="{{ route('admin.games.receipt', $game->id) }}"
                                      enctype="multipart/form-data"
                                      style="margin-top:.5rem;">
                                    @csrf
                                    <input type="file" name="file" required>
                                    <button type="submit">Anexar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
