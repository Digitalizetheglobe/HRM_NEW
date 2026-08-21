@extends('layouts.contractheader')
@section('page-title')
    {{ __('Experience Certificate') }}
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card mt-5" id="printTable">
            <div class="card-body p-5 watermark-container" id="boxes" style="position: relative; overflow: hidden; padding: 180px 60px 100px 60px; min-height: 1056px;">
                <!-- Letterhead Image -->
                @php
                    $imagePath = public_path('letter head/guv_letter_head.png');
                    $imageData = base64_encode(file_get_contents($imagePath));
                    $src = 'data:image/png;base64,' . $imageData;
                @endphp
                <img src="{{ $src }}" 
                     style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; object-fit: fill;" 
                     alt="Letterhead Background">

                {{-- Letter Content --}}
                <div class="letter-content mt-10" style="position: relative; z-index: 1;">
                    <div class="letter-body">
                        <br><br><br>
                        <div class="formatted-content" style="padding: 10px; margin-top: 100px;">
                            <div style="text-align: right; margin-bottom: 30px;">
                                <strong>Date:</strong> {{ date('d/m/Y') }}
                            </div>
                            <div class="text-center mb-5">
                                <h3 style="font-weight: bold; text-align: center;">EXPERIENCE CERTIFICATE</h3>
                            </div>
                            
                            <p><strong>To:</strong><br>
                            {{ trim($employees->name . ' ' . $employees->middle_name . ' ' . $employees->last_name) }}</p>
                            
                            <p>This is to certify that Mr./Ms. {{ trim($employees->name . ' ' . $employees->last_name) }} was employed with Digitalize The Globe from <strong>{{ !empty($employees->company_doj) ? \Auth::user()->dateFormat($employees->company_doj) : '24th December 2024' }}</strong> to <strong>{{ !empty($employees->termination_date) ? \Auth::user()->dateFormat($employees->termination_date) : '31st january 2026' }}</strong> as <strong>{{ $employees->designation->name ?? 'Graphic Designer' }}</strong>.</p>
                            
                            <p>In their role within our digital marketing department, their primary responsibilities included:</p>
                            
                            <p style="margin-bottom: 20px;">
                                Creating visual assets for social media and digital advertising.<br>
                                Assisting the creative team with layout designs and image editing.<br>
                                Ensuring design tasks were completed according to client briefs.
                            </p>
                            
                            <p>During their tenure, <strong>{{ trim($employees->name . ' ' . $employees->middle_name . ' ' . $employees->last_name) }}</strong> fulfilled their assigned duties and followed company protocols. This letter is issued at their request upon the conclusion of their employment.</p>
                            
                            <p>We wish them the best in their future professional pursuits.</p>
                            
                            <table style="width: 100%; margin-top: 60px;">
                                <tr>
                                    <td style="text-align: left; width: 50%;">
                                        <strong>Ankush Bagmar (Jain)</strong><br>
                                        Founder & CEO
                                    </td>
                                    <td style="text-align: right; width: 50%;">
                                        <strong>Dimple Prithwani</strong><br>
                                        Co-Founder & CMO
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="closing mt-4"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Watermark container */
    .watermark-container {
        position: relative;
    }

    /* Foreground content above watermark */
    .watermark-container > * {
        position: relative;
        z-index: 1;
    }

    /* Logo top-left */
    .company-logo {
        height: 75px;
        display: block;
    }

    /* Half-width black line */
    .header-line {
        border-top: 2px solid #000;
        width: 50%;
        margin-top: 15px;
    }

    .letter-content {
        font-family: 'Times New Roman', serif;
        line-height: 1.8;
        margin-top: 25px;
        color: #333;
    }

    .formatted-content {
        padding: 20px 0;
        font-size: 14px;
    }

    .formatted-content p {
        margin-bottom: 15px;
        text-align: justify;
    }

    .formatted-content strong {
        font-weight: 600;
    }

    #printTable {
        border: none;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .card-body {
        padding: 180px 60px 100px 60px; /* Adjust padding to fit within the letterhead header/footer */
        min-height: 1056px; /* A4 height approx */
    }

    @media print {
        .card-body {
            padding: 180px 60px 100px 60px;
        }
    }
</style>
@endpush
    @push('script-page')
        <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
        <script>
            function closeScript() {
                setTimeout(function() {
                    window.open(window.location, '_self').close();
                }, 1000);
            }

            $(window).on('load', function() {
                var element = document.getElementById('boxes');
                var opt = {
                    filename: '{{ $employees->name }}',
                    image: {
                        type: 'jpeg',
                        quality: 1
                    },
                    html2canvas: {
                        scale: 4,
                        dpi: 72,
                        letterRendering: true
                    },
                    jsPDF: {
                        unit: 'in',
                        format: 'A4'
                    }
                };

                html2pdf().set(opt).from(element).save().then(closeScript);
            });
        </script>
    @endpush
