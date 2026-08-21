
@extends('layouts.contractheader')
@section('page-title')
    {{ __('NOC') }}
@endsection

@section('content')
<div class="row" >

    <div class="col-lg-10">
        <div class="container">
            <div>
                <div class="card mt-5" id="printTable" style="margin-left: 180px;margin-right: -57px;">
                    <div class="card-body" id="boxes" style="position: relative; overflow: hidden; padding: 180px 60px 100px 60px; min-height: 1056px;">
                        <!-- Letterhead Image -->
                        @php
                            $imagePath = public_path('letter head/guv_letter_head.png');
                            $imageData = base64_encode(file_get_contents($imagePath));
                            $src = 'data:image/png;base64,' . $imageData;
                        @endphp
                        <img src="{{ $src }}" 
                             style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; object-fit: fill;" 
                             alt="Letterhead Background">

                            <div class="row invoice-title mt-2" style="position: relative; z-index: 1;">
                                
                                
                                <p data-v-f2a183a6="">
                                    <div>{!!$noc_certificate->content!!}</div>
                                   
                                </p>
                        

                        </div>
                 </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('styles')
<style>
    /* Background watermark (Letterhead) */
    #boxes {
        position: relative;
        padding: 180px 60px 100px 60px; /* Adjust padding to fit within the letterhead header/footer */
        min-height: 1056px; /* A4 height approx */
    }

    #boxes > * {
        position: relative;
        z-index: 1;
    }

    @media print {
        #boxes {
            padding: 180px 60px 100px 60px;
        }
    }
</style>
@endpush
@push('script-page')
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        function closeScript() {
            setTimeout(function () {
                window.open(window.location, '_self').close();
            }, 1000);
        }

        $(window).on('load', function () {
            var element = document.getElementById('boxes');
            var opt = {
                filename: '{{$employees->name}}',
                image: {type: 'jpeg', quality: 1},
                html2canvas: {scale: 4, dpi: 72, letterRendering: true},
                jsPDF: {unit: 'in', format: 'A4'}
            };

            html2pdf().set(opt).from(element).save().then(closeScript);
        });

        
    </script>
    
@endpush