@extends('admin.layout')

@section('title', 'Admin • Simulação de Jogos')

@section('content')
    <h2>Simulação de Jogos</h2>

    <p class="muted">
       Valor total arrecadado:
        <strong>
            R$ {{ number_format($totalValue, 2, ',', '.') }}
        </strong>
    </p>

    @if (empty($simulation))
    <p>Nenhum jogo pode ser gerado com o valor atual.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Números</th>
                    <th>Quantidade</th>
                    <th>Valor unitário</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($simulation as $item)
                    <tr>
                        <td>{{ $item['game_size'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>R$ {{ number_format($item['unit_price'], 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($item['total_price'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <hr>

<h4>Resumo da Simulação</h4>

<ul>
   <li>
    Valor utilizado nos jogos:
    <strong>R$ {{ number_format($summary['used'], 2, ',', '.') }}</strong>
    </li>

    <li>
        Valor restante:
        <strong>R$ {{ number_format($summary['remaining'], 2, ',', '.') }}</strong>
    </li>

    @if ($summary['remaining'] == 0)
        <li>✅ Todo o valor arrecadado foi utilizado nos jogos.</li>

    @elseif ($summary['missing_for_next_game'])
        <li>
            Para gerar mais 1 jogo de 6 números,
            o organizador precisaria adicionar:
            <strong>
                R$ {{ number_format($summary['missing_for_next_game'], 2, ',', '.') }}
            </strong>
        </li>

    @else
        <li>💡 O valor restante já permite gerar mais um jogo.</li>
    @endif

</ul>

 <hr>

    <div style="text-align: right;">

    {{-- Caso precise complementar --}}
    @if ($summary['remaining'] > 0 && $summary['missing_for_next_game'])
        <form method="POST" action="{{ route('admin.games.generate') }}">
            @csrf
            <input type="hidden" name="allow_complement" value="1">

            <button type="submit" class="btn btn-warning">
                Complementar
                R$ {{ number_format($summary['missing_for_next_game'], 2, ',', '.') }}
                e gerar novo jogo
            </button>
        </form>

    {{-- Caso já esteja otimizado --}}
    @else
        <form method="POST" action="{{ route('admin.games.generate') }}">
            @csrf
            <button type="submit" class="btn btn-primary">
                Gerar jogos oficiais
            </button>
        </form>
    @endif

</div>

    @endif
@endsection