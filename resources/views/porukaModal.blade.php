{{-- Autori: Mateja Milošević 2020/0487 --}}

<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="feedbackModalLabel">{{ $naslov }}</h1>
                <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" href=""></a>
            </div>
            <div class="modal-body">
                <p>{{ $sadrzaj }}</p>
            </div>
        </div>
    </div>
</div>

<script>
    new bootstrap.Modal("#feedbackModal").show();
</script>
