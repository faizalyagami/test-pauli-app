{{-- resources/views/tester/sessions/partials/realtime-data.blade.php --}}
<div class="card mt-3">
    <div class="card-header bg-light">
        <h6 class="mb-0"><i class="fas fa-list me-2"></i> Recent Answers (Real-time)</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Time</th>
                        <th>Column</th>
                        <th>Row</th>
                        <th>Answer</th>
                        <th>Correct Answer</th>
                        <th>Status</th>
                        <th>Time Taken</th>
                    </tr>
                </thead>
                <tbody id="realtimeAnswers">
                    <tr>
                        <td colspan="7" class="text-center">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let realtimeInterval;

    function startRealtimeUpdates() {
        realtimeInterval = setInterval(fetchRealtimeAnswers, 5000);
    }

    function fetchRealtimeAnswers() {
        fetch(`/api/sessions/{{ $session->id }}/realtime-answers`)
            .then(response => response.json())
            .then(data => {
                updateRealtimeTable(data.answers);
            })
            .catch(error => console.error('Error:', error));
    }

    function updateRealtimeTable(answers) {
        if (!answers || answers.length === 0) {
            document.getElementById('realtimeAnswers').innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        <i class="fas fa-inbox me-2"></i>No answers yet
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        answers.slice(0, 20).forEach(answer => {
            const statusClass = answer.is_correct ? 'text-success' : 'text-danger';
            const statusIcon = answer.is_correct ?
                '<i class="fas fa-check-circle"></i> Correct' :
                '<i class="fas fa-times-circle"></i> Wrong';

            html += `
                <tr>
                    <td><small>${answer.time || '-'}</small></td>
                    <td class="text-center"><strong>${answer.column}</strong></td>
                    <td class="text-center"><strong>${answer.row}</strong></td>
                    <td class="text-center"><span class="badge bg-dark fs-6">${answer.answer}</span></td>
                    <td class="text-center"><span class="badge bg-secondary">${answer.correct_value || '?'}</span></td>
                    <td class="${statusClass}">${statusIcon}</td>
                    <td class="text-center">${answer.time_taken || '-'}s</td>
                </tr>
            `;
        });

        document.getElementById('realtimeAnswers').innerHTML = html;
    }

    // Start realtime updates
    startRealtimeUpdates();

    // Cleanup
    window.onbeforeunload = function() {
        if (realtimeInterval) {
            clearInterval(realtimeInterval);
        }
    };
</script>
@endpush