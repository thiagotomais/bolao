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

        .success { background: #e6f4ea; color: #137333; }
        .warning { background: #fff4e5; color: #a15c00; }

        .numbers { margin-top: 8px; }

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

        .row { display: flex; flex-wrap: wrap; gap: 20px; }
        .col { flex: 1; min-width: 220px; }

        .muted { color: #777; }
    </style>
</head>
<body>

<div class="container">

    <!-- PAINEL GERAL -->
    <div class="card">
        <h1>🎉 Bolão Mega da Virada {{ env('APP_ANO') }}</h1>
        <p class="muted">Participante: <strong>{{ $participant->name }}</strong></p>

        <div class="row" style="margin-top:20px">
            <div class="col">
                <strong>💰 Total do bolão</strong><br>
                R$ {{ number_format($totalValue, 2, ',', '.') }}
            </div>

            <div class="col">
                <strong>💵 Seu investimento</strong><br>
                R$ {{ number_format($participantValue, 2, ',', '.') }}
            </div>

            <div class="col">
                <strong>📊 Sua participação</strong><br>
                {{ number_format($percent * 100, 2, ',', '.') }}%
            </div>
        </div>

        <hr style="margin:20px 0; border:none; border-top:1px solid #eee">

        <div class="row">
            <div class="col">
                <strong>🏆 Prêmio estimado</strong><br>
                R$ {{ number_format($estimatedPrize, 2, ',', '.') }}
            </div>

            <div class="col">
                <strong>🎯 Sua estimativa proporcional</strong><br>
                R$ {{ number_format($estimatedUserPrize * 0.9, 2, ',', '.') }}
            </div>
        </div>

        <p class="muted" style="font-size:12px; margin-top:12px">
            * Valores estimados.
        </p>
    </div>

    <!-- RESULTADO -->
    @if (!empty($drawNumbers))
        <div class="card">
            <h2>🎯 Números Sorteados</h2>
            <div class="numbers">
                @foreach ($drawNumbers as $n)
                    <span class="ball" style="background:#2e7d32">{{ $n }}</span>
                @endforeach
            </div>
        </div>
    @endif

    <!-- JOGOS OU PROGRESSO -->
    <div class="card">
        <h2>🎟️ Jogos do Bolão</h2>

        @if ($games->count() === 0)

            <div class="card" style="background:#fafafa">
                <h3>📈 Progresso do Bolão</h3>

                @if ($simulation['status'] === 'progress')
                    <p>
                        Já é possível realizar um jogo com
                        <strong>{{ $simulation['current']['game_size'] }} números</strong>.
                    </p>

                    <p>
                        @php
    $currentSize = $simulation['current']['game_size'];
    $nextSize    = $simulation['next']['game_size'];

    $currentOdds = $probabilities[$currentSize]['sena'] ?? null;
    $nextOdds    = $probabilities[$nextSize]['sena'] ?? null;
@endphp

<p>
    Para chegar a
    <strong>{{ $nextSize }} números</strong>,
    faltam
    <strong>R$ {{ number_format($simulation['missing'], 2, ',', '.') }}</strong>.
</p>

@if ($currentOdds && $nextOdds)
    <p class="muted" style="font-size:13px; margin-top:4px">
        Assim, aumentamos nossa chance de
        <strong>1 em {{ number_format($currentOdds, 0, ',', '.') }}</strong>
        para
        <strong>1 em {{ number_format($nextOdds, 0, ',', '.') }}</strong>.
    </p>
@endif
                    </p>

                    <div style="margin-top:12px">
						<div style="
							background:#cfcfcf;
							border-radius: 20px;
							height: 20px;
							overflow: hidden;
							position: relative;
						">
							<div style="
								width: {{ $simulation['progress'] }}%;
								height: 100%;
								background:#1976d2;
								border-radius: 20px;
							"></div>

							<span style="
								position: absolute;
								right: 10px;
								top: 50%;
								transform: translateY(-50%);
								font-size: 12px;
								font-weight: bold;
								color: #000;
							">
								{{ number_format($simulation['progress'], 1) }}%
							</span>
						</div>
					</div>


                @elseif ($simulation['status'] === 'max_reached')
                    <p>🎯 O bolão já atingiu o maior tipo de jogo disponível.</p>
                @else
                    <p>Ainda não há valor suficiente para gerar jogos.</p>
                @endif
            </div>

        @else

            @foreach ($games as $game)
                <div class="card" style="background:#fafafa">
                    <strong>{{ $game->game_size }} números</strong>

                    <div class="numbers">
                        @foreach ($game->numbers as $n)
                            @php $hit = in_array($n, $drawNumbers ?? []); @endphp
                            <span class="ball" style="background: {{ $hit ? '#2e7d32' : '#1976d2' }}">
                                {{ $n }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endforeach

        @endif
    </div>

</div>

</body>
</html>
