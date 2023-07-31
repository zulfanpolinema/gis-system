<form id="addTransaction">
    <x-adminlte-modal id="addTransactionModal" title="Tambah Pesanan">
        @csrf
        <input id="harvest_id" name="harvest_id" type="hidden" />
        <input id="price" type="hidden" />
        <input id="retail_price" type="hidden" />
        <input id="retail_minimum" type="hidden" />
        <x-adminlte-input name="amount" id="amount" label="Jumlah pesanan" placeholder="Jumlah pesanan">
            <x-slot name="appendSlot">
                <div class="input-group-text">
                    Kg
                </div>
            </x-slot>
        </x-adminlte-input>
        <x-adminlte-input name="total" label="Estimasi Harga" placeholder="0" readonly>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    Rp
                </div>
            </x-slot>
        </x-adminlte-input>
        <x-slot name="footerSlot">
            <x-adminlte-button theme="primary" label="Simpan" type="submit"/>
            <x-adminlte-button theme="default" label="Batalkan" data-dismiss="modal" />
        </x-slot>
    </x-adminlte-modal>
</form>

@push('js')
    <script>
        $(document).on('click', '#addTransactionButton', function() {
            $('#harvest_id').val($(this).data('id'));
            $('#price').val($(this).data('price'));
            $('#retail_price').val($(this).data('retail_price'));
            $('#retail_minimum').val($(this).data('retail_minimum'));
            $('#amount').val();
            $('#total').val(0);
        });

        $('#amount').on('input', function() {
            let amount = $(this).val();
            let price = $('#price').val();
            let retail_price = $('#retail_price').val();
            let retail_minimum = $('#retail_minimum').val();
            let total = 0;
            if (retail_price != 0 && amount >= retail_minimum) {
                total = amount * retail_price;
            } else {
                total = amount * price;
            }
            $('#total').val(total.toLocaleString('id-ID'));
        });

        $('#addTransaction').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('transactions.store') }}",
                method: "POST",
                dataType: "JSON",
                processData: false,
                contentType: false,
                cache: false,
                data: new FormData(this),
                success: function(data) {
                    $('#addTransactionModal').modal('hide');
                    $('#transactionForm').trigger('reset');
                    toastr.success(data.message, 'Sukses');
                },
                error: function(data) {
                    toastr.error(data.responseJSON.message, 'Error');
                }
            });
        });
    </script>
@endpush
