<div class="row justify-content-center">
    <div class="col-12 px-5">

        <div class="instructions-card mt-3">
            <div class="row g-4">

                <!-- Invoice -->
                <div class="col-md-6">
                    <div class="b1-text">
                        <span class="s1-text text-muted">Invoice No</span> :
                        {{ $purchase->invoice_no ?? '-' }}
                    </div>
                </div>

                <!-- PO -->
                <div class="col-md-6">
                    <div class="b1-text">
                        <span class="s1-text text-muted">PO Number</span> :
                        {{ $purchase->po_number ?? '-' }}
                    </div>
                </div>

                <!-- Purchase Date -->
                <div class="col-md-6">
                    <div class="b1-text">
                        <span class="s1-text text-muted">Purchase Date</span> :
                        {{ CommonHelper::getDateAs($purchase->invoice_date, "d/m/Y", "Y-m-d") }}
                    </div>
                </div>

                <!-- Received Date -->
                <div class="col-md-6">
                    <div class="b1-text">
                        <span class="s1-text text-muted">Received Date</span> :
                        {{ CommonHelper::getDateAs($purchase->received_date, "d/m/Y", "Y-m-d") }}
                    </div>
                </div>

                <!-- Company -->
                <div class="col-md-6">
                    <div class="b1-text">
                        <span class="s1-text text-muted">Company</span> :
                        {{ $purchase->company->name ?? '-' }}
                    </div>
                </div>

                <!-- Supplier -->
                <div class="col-md-6">
                    <div class="b1-text">
                        <span class="s1-text text-muted">Supplier</span> :
                        {{ $purchase->supplier->name ?? '-' }}
                    </div>
                </div>

                <!-- Location -->
                <div class="col-md-6">
                    <div class="b1-text">
                        <span class="s1-text text-muted">Location</span> :
                        {{ $purchase->location->name ?? '-' }}
                    </div>
                </div>

                <!-- Status -->
                <div class="col-md-6">
                    <div class="b1-text">
                        <span class="s1-text text-muted">Status</span> :
                        @if($purchase->fully_received == 0)
                        Not Yet Received
                        @elseif($purchase->fully_received == 1)
                        Fully Received
                        @elseif($purchase->fully_received == 2)
                        Partially Received
                        @else
                        Provision
                        @endif
                    </div>
                </div>

                <!-- Quantity -->
                <div class="col-md-6">
                    <div class="b1-text">
                        <span class="s1-text text-muted">Quantity</span> :
                        {{ $purchase->qty ?? '-' }}
                    </div>
                </div>

                <!-- Bill Amount -->
                <div class="col-md-6">
                    <div class="b1-text">
                        <span class="s1-text text-muted">Bill Amount</span> :
                        {{ $purchase->bill_amount ?? '-' }}
                    </div>
                </div>

                <!-- Notes -->
                <div class="col-md-12">
                    <div class="b1-text">
                        <span class="s1-text text-muted">Notes</span> :
                        {{ $purchase->notes ?? '-' }}
                    </div>
                </div>

            </div>

            <!-- TAX SECTION -->
            {{-- @if(!empty($taxElementArray))
            <div class="row g-4 mt-3">
                @foreach($taxElementArray as $key => $value)
                <div class="col-md-6">
                    <label class="s1-text text-muted">{{ $key }}</label>
                    <div class="b1-text">{{ number_format($value, 2) }}</div>
                </div>
                @endforeach

                <div class="col-md-6">
                    <label class="s1-text text-muted">Total Amount</label>
                    <div class="b1-text fw-bold">{{ $totalAmount }}</div>
                </div>
            </div>
            @endif --}}

            @if(!empty($taxElementArray))
            <div class="row g-4 mt-3">

                @foreach($taxElementArray as $key => $value)
                <div class="col-md-6">
                    <div class="b1-text">
                        <span class="s1-text text-muted">{{ $key }}</span> :
                        {{ number_format($value, 2) }}
                    </div>
                </div>
                @endforeach

                <div class="col-md-6">
                    <div class="b1-text fw-bold">
                        <span class="s1-text text-muted">Total Amount</span> :
                        {{ $totalAmount }}
                    </div>
                </div>

            </div>
            @endif

        </div>
    </div>
</div>