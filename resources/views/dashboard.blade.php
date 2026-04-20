@extends('layouts.app')

@section('content')
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Tahoma, Arial, sans-serif;
            background:
                linear-gradient(rgba(9, 15, 35, 0.64), rgba(9, 15, 35, 0.56)),
                url('{{ asset('images/crm.jpg') }}') no-repeat center center fixed;
            background-size: cover;
            color: #0f172a;
        }

        .dashboard-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 120px 24px 32px;
        }

        .dashboard-card {
            width: min(1180px, 100%);
            border-radius: 34px;
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(255, 255, 255, 0.86);
            box-shadow: 0 32px 90px rgba(8, 15, 39, 0.32);
            overflow: hidden;
        }

        .dashboard-top {
            display: grid;
            grid-template-columns: minmax(0, 0.95fr) minmax(280px, 0.7fr);
            gap: 22px;
            padding: 34px 34px 22px;
            background:
                radial-gradient(circle at top right, rgba(56, 189, 248, 0.12), transparent 28%),
                linear-gradient(180deg, rgba(248, 250, 252, 0.96), rgba(241, 245, 249, 0.92));
        }

        .side-stats {
            padding: 0 34px 34px;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .stat-box {
            padding: 18px 16px;
            border-radius: 22px;
            background: rgba(15, 23, 42, 0.92);
            border: 1px solid rgba(148, 163, 184, 0.16);
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.16);
        }

        .stat-box span {
            display: block;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.72);
            margin-bottom: 8px;
        }

        .stat-box strong {
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
        }

        .chart-panel {
            border-radius: 30px;
            padding: 24px 22px 18px;
            background: linear-gradient(180deg, #0b1023, #111733);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
        }

        .chart-panel-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }

        .chart-panel-top h2 {
            margin: 0;
            color: #f8fafc;
            font-size: clamp(20px, 2.4vw, 30px);
        }

        .chart-legend {
            display: flex;
            align-items: center;
            gap: 18px;
            color: rgba(255, 255, 255, 0.88);
            font-size: 14px;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .legend-swatch {
            width: 16px;
            height: 16px;
            border-radius: 6px;
        }

        .legend-swatch.is-prospect {
            background: linear-gradient(135deg, #fb72dd, #a431e7);
        }

        .legend-swatch.is-client {
            background: linear-gradient(135deg, #5de3ff, #258dff);
        }

        .chart-stage {
            display: grid;
            grid-template-columns: 66px minmax(0, 1fr);
            gap: 14px;
            align-items: stretch;
        }

        .chart-y-axis {
            display: grid;
            grid-template-rows: repeat(6, 1fr);
            align-items: end;
            padding: 8px 0 34px;
            color: rgba(255, 255, 255, 0.72);
            font-size: 13px;
        }

        .chart-surface {
            position: relative;
            height: 320px;
            border-left: 1px solid rgba(255, 255, 255, 0.12);
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            background-image:
                linear-gradient(to top, rgba(255, 255, 255, 0.1) 1px, transparent 1px),
                linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 100% calc(100% / 5), calc(100% / 12) 100%;
            overflow: hidden;
        }

        .chart-svg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        .chart-empty,
        .chart-loading {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            text-align: center;
            padding: 24px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 15px;
        }

        .chart-x-axis {
            position: relative;
            height: 34px;
            padding-top: 12px;
            margin-left: 80px;
            color: rgba(255, 255, 255, 0.76);
            font-size: 14px;
        }

        .chart-x-axis span {
            position: absolute;
            top: 12px;
            transform: translateX(-50%);
            white-space: nowrap;
        }

        .welcome-panel {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100%;
            padding: 28px;
            border-radius: 30px;
            background:
                linear-gradient(160deg, rgba(15, 23, 42, 0.96), rgba(30, 41, 59, 0.92)),
                url('{{ asset('images/crm.jpg') }}') no-repeat center center;
            background-size: cover;
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }

        .welcome-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg, rgba(14, 165, 233, 0.14), rgba(15, 23, 42, 0.5), rgba(15, 23, 42, 0.76));
        }

        .welcome-content,
        .welcome-meta {
            position: relative;
            z-index: 1;
        }

        .welcome-kicker {
            margin: 0 0 14px;
            font-size: 13px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.68);
        }

        .welcome-title {
            margin: 0;
            font-size: clamp(34px, 5vw, 56px);
            line-height: 0.98;
            font-weight: 800;
        }

        .welcome-name {
            display: block;
            margin-top: 10px;
            color: #7dd3fc;
        }

        .welcome-copy {
            margin: 18px 0 0;
            max-width: 320px;
            color: rgba(255, 255, 255, 0.76);
            font-size: 15px;
            line-height: 1.8;
        }

        .welcome-meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 28px;
        }

        .welcome-pill {
            padding: 16px 14px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .welcome-pill span {
            display: block;
            color: rgba(255, 255, 255, 0.68);
            font-size: 12px;
            margin-bottom: 8px;
        }

        .welcome-pill strong {
            font-size: 20px;
            font-weight: 700;
        }

        .logout-button {
            margin-top: 18px;
            padding: 12px 18px;
            border-radius: 18px;
            border: none;
            background: linear-gradient(160deg, rgba(14, 165, 233, 0.14), rgba(15, 23, 42, 0.5), rgba(15, 23, 42, 0.76));
            color: #ffffff;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .logout-button:hover {
            background: rgba(241, 242, 245, 0.95);
            color: #0f172a;

        }

        @media (max-width: 720px) {
            .dashboard-shell {
                padding: 104px 14px 20px;
            }

            .dashboard-top {
                grid-template-columns: 1fr;
                padding: 24px 18px 18px;
            }

            .side-stats {
                padding: 0 18px 24px;
                grid-template-columns: 1fr 1fr;
            }

            .chart-panel-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .chart-stage {
                grid-template-columns: 48px minmax(0, 1fr);
                gap: 10px;
            }

            .chart-surface {
                height: 260px;
            }

            .chart-x-axis {
                margin-left: 58px;
                height: 46px;
                padding-top: 18px;
                font-size: 10px;
            }

            .chart-x-axis span {
                transform: translateX(-50%) rotate(-35deg);
                transform-origin: top center;
            }

            .welcome-panel {
                padding: 22px;
            }
        }
    </style>

    <div class="dashboard-shell">
        <div class="dashboard-card">
            <div class="dashboard-top">
                <section class="dashboard-main">
                    <div class="chart-panel">
                        <div class="chart-panel-top">
                            <h2>Prospects vs client</h2>
                            <div class="chart-legend">
                                <div class="legend-item">
                                    <span class="legend-swatch is-prospect"></span>
                                    <span>Prospects</span>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-swatch is-client"></span>
                                    <span>client</span>
                                </div>
                            </div>
                        </div>

                        <div class="chart-stage">
                            <div class="chart-y-axis" id="chart-y-axis">
                                <span>0</span>
                                <span>0</span>
                                <span>0</span>
                                <span>0</span>
                                <span>0</span>
                                <span>0</span>
                            </div>

                            <div>
                                <div class="chart-surface" id="chart-surface">
                                    <div class="chart-loading" id="chart-loading">Chargement du graphique...</div>
                                    <svg class="chart-svg" id="chart-svg" viewBox="0 0 1000 420" preserveAspectRatio="none"></svg>
                                </div>

                                <div class="chart-x-axis" id="chart-x-axis"></div>
                            </div>
                        </div>
                    </div>
                </section>

                <aside class="welcome-panel">
                    <div class="welcome-content">
                        <p class="welcome-kicker">Dashboard</p>
                        <h1 class="welcome-title">
                            Welcome
                            <span class="welcome-name">{{ auth()->user()->name }}</span>
                        </h1>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="logout-button">Logout</button>
                        </form>
                    </div>

                    <div class="welcome-meta">
                        <div class="welcome-pill">
                            <span>Annee</span>
                            <strong>{{ now()->year }}</strong>
                        </div>
                        <div class="welcome-pill">
                            <span>Mois actuel</span>
                            <strong>{{ now()->format('M') }}</strong>
                        </div>
                    </div>
                </aside>
            </div>

            <div class="side-stats">
                <div class="stat-box">
                    <span>Prospects</span>
                    <strong id="prospects-count">{{ $prospectsCount }}</strong>
                </div>
                <div class="stat-box">
                    <span>Clients</span>
                    <strong id="clients-count">{{ $clientsCount }}</strong>
                </div>
                <div class="stat-box">
                    <span>Resume aujourd'hui</span>
                    <strong id="resumes-count">{{ $resumesTodayCount }}</strong>
                </div>
                <div class="stat-box">
                    <span>Conversions ce mois</span>
                    <strong id="converted-count">{{ $convertedThisMonthCount }}</strong>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartSvg = document.getElementById('chart-svg');
            const chartSurface = document.getElementById('chart-surface');
            const chartLoading = document.getElementById('chart-loading');
            const chartXAxis = document.getElementById('chart-x-axis');
            const chartYAxis = document.getElementById('chart-y-axis');
            const prospectsCount = document.getElementById('prospects-count');
            const clientsCount = document.getElementById('clients-count');
            const resumesCount = document.getElementById('resumes-count');
            const convertedCount = document.getElementById('converted-count');

            const width = 1000;
            const height = 420;
            const baseline = height;

            function setAxisTicks(maxValue) {
                const ticks = Array.from({ length: 6 }, function (_, index) {
                    return Math.ceil((maxValue * (5 - index)) / 5);
                });

                chartYAxis.innerHTML = ticks.map(function (tick) {
                    return '<span>' + tick + '</span>';
                }).join('');
            }

            function setXAxis(labels) {
                const lastIndex = Math.max(labels.length - 1, 1);

                chartXAxis.innerHTML = labels.map(function (label, index) {
                    const left = (index / lastIndex) * 100;
                    return '<span style="left:' + left + '%">' + label + '</span>';
                }).join('');
            }

            function buildPoints(series, maxValue) {
                const stepX = series.length > 1 ? width / (series.length - 1) : width;

                return series.map(function (value, index) {
                    const x = index * stepX;
                    const y = baseline - ((Number(value) || 0) / maxValue) * (height - 18);
                    return { x: x, y: y };
                });
            }

            function smoothPath(points) {
                if (!points.length) {
                    return '';
                }

                if (points.length === 1) {
                    return 'M ' + points[0].x + ' ' + points[0].y;
                }

                let path = 'M ' + points[0].x + ' ' + points[0].y;

                for (let index = 0; index < points.length - 1; index++) {
                    const current = points[index];
                    const next = points[index + 1];
                    const controlX = (current.x + next.x) / 2;
                    path += ' C ' + controlX + ' ' + current.y + ', ' + controlX + ' ' + next.y + ', ' + next.x + ' ' + next.y;
                }

                return path;
            }

            function areaPath(points) {
                if (!points.length) {
                    return '';
                }

                return smoothPath(points) + ' L ' + points[points.length - 1].x + ' ' + baseline + ' L ' + points[0].x + ' ' + baseline + ' Z';
            }

            function buildDots(points, stroke) {
                return points.map(function (point) {
                    return '<circle cx="' + point.x + '" cy="' + point.y + '" r="7" fill="' + stroke + '" fill-opacity="0.18" stroke="none"></circle>' +
                        '<circle cx="' + point.x + '" cy="' + point.y + '" r="4.4" fill="#f8fafc" stroke="' + stroke + '" stroke-width="2"></circle>';
                }).join('');
            }

            function renderChart(months) {
                const labels = months.map(function (month) {
                    return month.label;
                });
                const prospects = months.map(function (month) {
                    return Number(month.prospects) || 0;
                });
                const clients = months.map(function (month) {
                    return Number(month.clients) || 0;
                });
                const maxValue = Math.max(1, ...prospects, ...clients);

                setXAxis(labels);
                setAxisTicks(maxValue);

                const prospectPoints = buildPoints(prospects, maxValue);
                const clientPoints = buildPoints(clients, maxValue);

                chartSvg.innerHTML = `
                    <defs>
                        <linearGradient id="prospect-fill" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#fb72dd" stop-opacity="0.78"></stop>
                            <stop offset="65%" stop-color="#d14dff" stop-opacity="0.34"></stop>
                            <stop offset="100%" stop-color="#7f22fe" stop-opacity="0.08"></stop>
                        </linearGradient>
                        <linearGradient id="client-fill" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#6de7ff" stop-opacity="0.88"></stop>
                            <stop offset="65%" stop-color="#339dff" stop-opacity="0.34"></stop>
                            <stop offset="100%" stop-color="#1d4ed8" stop-opacity="0.08"></stop>
                        </linearGradient>
                        <filter id="chart-glow" x="-20%" y="-20%" width="140%" height="140%">
                            <feGaussianBlur stdDeviation="7" result="blur"></feGaussianBlur>
                            <feMerge>
                                <feMergeNode in="blur"></feMergeNode>
                                <feMergeNode in="SourceGraphic"></feMergeNode>
                            </feMerge>
                        </filter>
                    </defs>
                    <path d="${areaPath(prospectPoints)}" fill="url(#prospect-fill)"></path>
                    <path d="${areaPath(clientPoints)}" fill="url(#client-fill)"></path>
                    <path d="${smoothPath(prospectPoints)}" fill="none" stroke="#fb72dd" stroke-width="4" filter="url(#chart-glow)"></path>
                    <path d="${smoothPath(clientPoints)}" fill="none" stroke="#5de3ff" stroke-width="4" filter="url(#chart-glow)"></path>
                    ${buildDots(prospectPoints, '#fb72dd')}
                    ${buildDots(clientPoints, '#5de3ff')}
                `;
            }

            function showEmptyState(message) {
                chartSvg.innerHTML = '';
                chartLoading.className = 'chart-empty';
                chartLoading.textContent = message;
            }

            fetch('{{ route('dashboard.data') }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Failed to load dashboard data.');
                    }

                    return response.json();
                })
                .then(function (data) {
                    chartLoading.remove();

                    prospectsCount.textContent = data.prospectsCount ?? 0;
                    clientsCount.textContent = data.clientsCount ?? 0;
                    resumesCount.textContent = data.resumesTodayCount ?? 0;
                    convertedCount.textContent = data.convertedThisMonthCount ?? 0;

                    if (!Array.isArray(data.months) || !data.months.length) {
                        showEmptyState('Aucune donnee disponible pour afficher le graphique.');
                        return;
                    }

                    renderChart(data.months);
                })
                .catch(function () {
                    showEmptyState('Impossible de charger les donnees du graphique pour le moment.');
                });
        });
    </script>
@endsection
