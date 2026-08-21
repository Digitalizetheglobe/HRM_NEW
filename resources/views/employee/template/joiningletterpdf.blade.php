<div class="offer-letter-wrap">
    <div class="d-flex justify-content-end mb-2">
        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="bottom"
            title="{{ __('Download') }}" onclick="saveAsPDF(); return false;">
            <span class="fa fa-download"></span>
        </a>
    </div>

    <div class="offer-letter-viewport">
        <div class="card offer-letter-page" id="printTable">
            <div class="card-body" id="boxes">
                @php
                    $src = null;
                    $imagePath = public_path('letter head/guv_letter_head.png');
                    if (file_exists($imagePath)) {
                        $src = 'data:image/png;base64,' . base64_encode(file_get_contents($imagePath));
                    }
                    $fullName = trim($employees->name . ' ' . ($employees->middle_name ?? '') . ' ' . ($employees->last_name ?? ''));
                    $letterDate = !empty($employees->company_doj) ? \Auth::user()->dateFormat($employees->company_doj) : date('d/m/Y');
                @endphp

                @if($src)
                    <img src="{{ $src }}" class="offer-letter-letterhead" alt="Letterhead">
                @endif

                <div class="letter-content">
                    <div class="letter-date">
                        <strong>Date:</strong> {{ $letterDate }}
                    </div>

                    <h3 class="letter-title">OFFER LETTER</h3>

                    <p><strong>To:</strong><br>{{ $fullName }}</p>

                    <p>We are thrilled to extend this offer to you for the position of {{ $employees->designation->name ?? 'Web Developer Executive' }} at GUV CORPORATION LLP (Digitalize The Globe)!</p>

                    <p><strong>Below are the details of the offer:</strong></p>
                    <ul>
                        <li><strong>Designation:</strong> {{ $employees->designation->name ?? 'Social Media Marketing Executive' }}</li>
                        <li><strong>Joining Date:</strong> {{ $letterDate }}</li>
                        <li><strong>1.5 Paid Leave every month</strong></li>
                    </ul>

                    <p><strong>Salary: {{ !empty($employees->salary) ? \Auth::user()->priceFormat($employees->salary) : '25000' }}/- per month</strong></p>
                    <ul>
                        <li>₹14,000 per month for the first 6 months</li>
                        <li>From the 7th to the 12th month, your salary will be revised to ₹16,000 or ₹17,000, based on your overall performance and contribution.</li>
                    </ul>

                    <p>We believe your skills and experience make you an ideal fit for our team. Our company offers a dynamic work environment, opportunities for growth, and a chance to work on exciting projects.</p>

                    <p>Please note, upon joining, you will be required to serve a 30-day notice period should you decide to resign from your position.<br>
                    Kindly confirm your acceptance by replying to this email.</p>

                    <p><strong>Congratulations, and we look forward to having you on board!</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #commonModal .modal-dialog.modal-xl {
        max-width: 980px;
        width: calc(100% - 24px);
        margin: 12px auto;
    }

    .offer-letter-wrap {
        padding: 8px 16px 16px;
    }

    .offer-letter-viewport {
        max-height: calc(100vh - 170px);
        overflow: auto;
        background: #eef1f6;
        padding: 16px 12px;
        border-radius: 8px;
    }

    .offer-letter-page {
        width: 100%;
        max-width: 794px;
        margin: 0 auto;
        border: none;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.12);
    }

    #boxes {
        position: relative;
        box-sizing: border-box;
        width: 100%;
        min-height: 1123px;
        padding: 210px 56px 96px;
        overflow: visible;
        background: #fff;
    }

    .offer-letter-letterhead {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        object-fit: fill;
        pointer-events: none;
    }

    .letter-content {
        position: relative;
        z-index: 1;
        font-family: 'Times New Roman', Times, serif;
        font-size: 15px;
        line-height: 1.65;
        color: #111;
        word-wrap: break-word;
        overflow-wrap: anywhere;
        max-width: 100%;
    }

    .letter-content p,
    .letter-content li {
        max-width: 100%;
        white-space: normal;
    }

    .letter-date {
        text-align: right;
        margin-bottom: 28px;
    }

    .letter-title {
        font-weight: 700;
        text-align: center;
        margin: 0 0 28px;
        letter-spacing: 1px;
    }

    @media (max-width: 768px) {
        #boxes {
            padding: 160px 28px 72px;
            min-height: 900px;
        }
        .letter-content {
            font-size: 14px;
        }
    }
</style>

<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script>
    function saveAsPDF() {
        var element = document.getElementById('boxes');
        var opt = {
            filename: '{{ $employees->name }}_Offer_Letter',
            image: {type: 'jpeg', quality: 1},
            html2canvas: {scale: 2, dpi: 72, letterRendering: true, useCORS: true},
            jsPDF: {unit: 'in', format: 'A4', orientation: 'portrait'}
        };
        html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
            var isAndroid = navigator.userAgent.toLowerCase().indexOf("android") > -1;

            if (isAndroid && navigator.share) {
                try {
                    var blob = pdf.output('blob');
                    var file = new File([blob], opt.filename + ".pdf", { type: "application/pdf" });
                    navigator.share({
                        files: [file],
                        title: opt.filename,
                        text: 'Offer Letter Document'
                    }).catch(function(err) {
                        alert("Error sharing: " + err.message);
                    });
                } catch(e) {
                    alert("Error preparing document: " + e.message);
                }
            } else {
                if (isAndroid) {
                    alert("Your app does not support direct PDF downloads. Please open the app in Google Chrome to download.");
                } else {
                    pdf.save(opt.filename + ".pdf");
                }
            }
        });
    }
</script>
