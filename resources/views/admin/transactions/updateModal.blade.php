<form id="editTransaction">
    <x-adminlte-modal id="editTransactionModal" title="Edit Pesanan">
        @csrf
        @method('PUT')
        <input id="transaction_id" type="hidden">
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
            <x-adminlte-button theme="primary" label="Simpan" type="submit" />
            <x-adminlte-button theme="default" label="Batalkan" data-dismiss="modal" />
        </x-slot>
    </x-adminlte-modal>
</form>

@push('js')
    <script>
        $(document).on('click', '#editTransactionButton', async function() {
            var id = $(this).data('id');
            var url = "{{ route('transactions.edit', ':id') }}";
            await $.ajax({
                url: url.replace(':id', id),
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    $('#transaction_id').val(data.id);
                    $('#amount').val(data.amount);
                    $('#total').val(data.total.toLocaleString('id-ID'));
                    $('#price').val(data.harvest.price);
                    $('#retail_price').val(data.harvest.retail_price);
                    $('#retail_minimum').val(data.harvest.retail_minimum);
                    $('#total').val(data.total);
                }
            })

        });

        $('#amount').on('input', function() {
            var amount = parseInt($(this).val());
            var price = parseInt($('#price').val());
            var retail_price = parseInt($('#retail_price').val());
            var retail_minimum = parseInt($('#retail_minimum').val());
            var total = 0;
            if (retail_price != 0 && amount >= retail_minimum) {
                total = amount * retail_price;
            } else {
                total = amount * price;
            }
            $('#total').val(total.toLocaleString('id-ID'));
        });

        $('#editTransaction').on('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                customClass: {
                    confirmButton: 'bg-primary',
                },
                title: 'Apakah anda yakin?',
                text: "Apakah anda yakin ingin mengubah pembelian panen ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya'
            }).then((result) => {
                if (result.isConfirmed) {
                    var id = $('#transaction_id').val();
                    var url = "{{ route('transactions.update', ':id') }}";
                    $.ajax({
                        url: url.replace(':id', id),
                        method: "POST",
                        dataType: "JSON",
                        processData: false,
                        contentType: false,
                        cache: false,
                        data: {
                            _token: $("meta[name='csrf-token']").attr("content"),
                        },
                        data: new FormData(this),
                        success: function(data) {
                            $('#editTransactionModal').modal('hide');
                            $('#editTransaction').trigger('reset');
                            $('#transactionsTable').DataTable().ajax.reload();
                            toastr.success(data.message, 'Sukses');
                        },
                        error: function(data) {
                            toastr.error(data.responseJSON.message, 'Error');
                        }
                    });
                }
            });
        });
    </script>
@endpush
