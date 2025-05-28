<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="receiptModalLabel">Acknowledgement Receipt</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Receipt content will be loaded here via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="printReceipt()">
                    <i class="fas fa-print mr-1"></i>Print
                </button>
                <button type="button" class="btn btn-warning" onclick="downloadPDF()">
                    <i class="fas fa-download mr-1"></i>Download PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function printReceipt() {
    var printContents = document.querySelector('#receiptModal .modal-body').innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    
    // Rebind event handlers after restoring content
    location.reload();
}

function downloadPDF() {
    var paymentTransactionId = $('#receiptModal').data('payment-transaction-id');
    window.location.href = 'indexes/download-receipt.php?payment_transaction_id=' + paymentTransactionId;
}
</script> 