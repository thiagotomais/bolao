<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Bolão Mega da Virada {{ env('APP_ANO') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        h1, h2 {
            margin-top: 0;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            background: #eee;
        }

        .success {
            background: #e6f4ea;
            color: #137333;
        }

        .warning {
            background: #fff4e5;
            color: #a15c00;
        }

        .numbers {
            margin-top: 8px;
        }

        .ball {
            display: inline-block;
            width: 36px;
            height: 36px;
            line-height: 36px;
            border-radius: 50%;
            background: #1976d2;
            color: #fff;
            text-align: center;
            margin: 4px;
            font-weight: bold;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .col {
            flex: 1;
            min-width: 220px;
        }

        a {
            color: #1976d2;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .muted {
            color: #777;
        }
    </style>
</head>
<body>

<div class="container">

   
 <!-- PAINEL PARTICIPANTE -->

 <!-- PAINEL GERAL -->
<div class="card">
    <h1>🎉 Bolão Mega da Virada {{ env('APP_ANO') }}</h1>
    <p class="muted">
        Participante: <strong>{{ $participant->name }}</strong>
    </p>

    <div class="row" style="margin-top:20px">

        <!-- Total do bolão -->
        <div class="col">
            <strong>💰 Total do bolão</strong><br>
            R$ {{ number_format($totalValue, 2, ',', '.') }}
        </div>

        <!-- Valor investido -->
        <div class="col">
            <strong>💵 Seu investimento</strong><br>
            R$ {{ number_format($participantValue, 2, ',', '.') }}
        </div>

        <!-- Percentual -->
        <div class="col">
            <strong>📊 Sua participação</strong><br>
            {{ number_format($percent * 100, 2, ',', '.') }}%
        </div>
    </div>

    <hr style="margin:20px 0; border:none; border-top:1px solid #eee">

    <div class="row">
        <!-- Prêmio total -->
        <div class="col">
            <strong>🏆 Prêmio estimado do concurso</strong><br>
            R$ {{ number_format($estimatedPrize, 2, ',', '.') }}
        </div>

        <!-- Prêmio proporcional -->
        <div class="col">
            <strong>🎯 Sua estimativa proporcional</strong><br>
            R$ {{ number_format($estimatedUserPrize*0.9, 2, ',', '.') }}
        </div>
    </div>

    <p class="muted" style="font-size:12px; margin-top:12px">
        * Valores estimados, sujeitos a variações e regras oficiais da Caixa.
    </p>
</div>

@if (!empty($drawNumbers))
    <div class="card">
        <h2>🎯 Números Sorteados</h2>

        <div class="numbers">
            @foreach ($drawNumbers as $n)
                <span class="ball" style="background:#2e7d32">
                    {{ $n }}
                </span>
            @endforeach
        </div>

        <p class="muted" style="margin-top:10px">
            Resultado oficial do sorteio.
        </p>
    </div>
@endif


    <!-- JOGOS -->
    <div class="card">
        <h2>🎟️ Jogos Definidos</h2>

        @if (count($games) === 0)
            <p class="muted">Nenhum jogo confirmado até o momento.</p>
        @else
            @foreach ($games as $game)
                <div class="card" style="background:#fafafa">
                    <strong>{{ $game->game_size }} números</strong>

                    @if (!empty($game->file_path))
                        <span class="badge success">Jogo registrado</span>
                    @else
                        <span class="badge warning">Aguardando registro na lotérica</span>
                    @endif


                    <div class="numbers">
                        @foreach ($game->numbers as $n)
                            @php
                                $isHit = in_array($n, $drawNumbers ?? []);
                            @endphp

                            <span class="ball"
                                style="
                                    background: {{ $isHit ? '#2e7d32' : '#1976d2' }};
                                    box-shadow: {{ $isHit ? '0 0 8px rgba(46,125,50,0.8)' : 'none' }};
                                ">
                                {{ $n }}
                            </span>

                        @endforeach
                    </div>

                      
                    @php
    $odd = $probabilities[$game->game_size] ?? null;
@endphp

@if ($odd)
    <div style="margin-top:12px;">
        <table style="
            border-collapse: collapse;
            font-size: 13px;
            background: #fafafa;
            width: auto;
        ">
            <thead>
                <tr>
                    <th style="border:1px solid #ddd; padding:6px 10px; text-align:left;">
                        Quantidade de números
                    </th>
                    <th style="border:1px solid #ddd; padding:6px 10px;">
                        Sena
                    </th>
                    <th style="border:1px solid #ddd; padding:6px 10px;">
                        Quina
                    </th>
                    <th style="border:1px solid #ddd; padding:6px 10px;">
                        Quadra
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border:1px solid #ddd; padding:6px 10px;">
                        {{ $game->game_size }}
                    </td>
                    <td style="border:1px solid #ddd; padding:6px 10px; text-align:center;">
                        {{ number_format($odd['sena'], 0, ',', '.') }}
                    </td>
                    <td style="border:1px solid #ddd; padding:6px 10px; text-align:center;">
                        {{ number_format($odd['quina'], 0, ',', '.') }}
                    </td>
                    <td style="border:1px solid #ddd; padding:6px 10px; text-align:center;">
                        {{ number_format($odd['quadra'], 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="muted" style="font-size:12px; margin-top:4px;">
            Probabilidade de acerto (1 em)
        </div>
    </div>
@endif



                    <div style="margin-top:10px">
                        @if (!empty($game->file_path))
                            <a href="{{ asset('storage/' . $game->file_path) }}" target="_blank">
                                📄 Ver comprovante
                            </a>
                        @else
                            <span class="badge warning">Comprovante pendente</span>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>

</div>

</body>
</html>
