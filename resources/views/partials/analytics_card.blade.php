<div class="card shadow-sm h-100">
    <div class="card-header fw-bold bg-white d-flex justify-content-between align-items-center">
        <span>{{ $title }}</span>
        @if(isset($icon))
            <i class="{{ $icon }} text-muted"></i>
        @endif
    </div>
    <div class="card-body">
        <div class="chart-container" style="position: relative; height: {{ $height ?? '300' }}px; width: 100%;">
            <canvas id="{{ $id }}"></canvas>
        </div>
        @if(isset($description))
            <p class="mt-2 text-muted small mb-0 border-top pt-2 d-print-none">
                <i class="fas fa-info-circle me-1"></i> {{ $description }}
            </p>
        @endif
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        new Chart(document.getElementById('{{ $id }}').getContext('2d'), {
            type: '{{ $type ?? "bar" }}',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: '{{ $datasetLabel ?? "العدد" }}',
                    data: {!! json_encode($data) !!},
                    backgroundColor: {!! json_encode($colors ?? '#003366') !!},
                    borderColor: {!! json_encode($borderColors ?? 'transparent') !!},
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: {{ isset($showLegend) && $showLegend ? 'true' : 'false' }}, position: 'bottom' }
                },
                scales: {
                    y: { beginAtZero: true, display: '{{ $type ?? "bar" }}' !== 'pie' && '{{ $type ?? "bar" }}' !== 'doughnut' }
                }
            }
        });
    });
</script>
