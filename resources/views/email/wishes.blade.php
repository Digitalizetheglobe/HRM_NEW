<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $eventType === 'birthday' ? 'Happy Birthday' : 'Work Anniversary' }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f4f8; }

  /* ── Birthday Theme ── */
  .birthday-header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
  .birthday-accent { color: #f5576c; }
  .birthday-btn    { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
  .birthday-ribbon { background: #f5576c; }

  /* ── Anniversary Theme ── */
  .anniversary-header { background: linear-gradient(135deg, #018ac8 0%, #0056b3 100%); }
  .anniversary-accent { color: #018ac8; }
  .anniversary-btn    { background: linear-gradient(135deg, #018ac8 0%, #0056b3 100%); }
  .anniversary-ribbon { background: #018ac8; }
</style>
</head>
<body>
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8; padding: 40px 20px;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background:#ffffff; border-radius:24px; overflow:hidden; box-shadow:0 10px 40px rgba(0,0,0,0.12);">

        {{-- ══ HERO HEADER ══ --}}
        <tr>
          <td class="{{ $eventType }}-header" style="padding: 50px 40px 40px; text-align:center;">

            @if($eventType === 'birthday')
              {{-- Birthday decorations --}}
              <div style="font-size:70px; line-height:1; margin-bottom:16px;">🎂</div>
              <div style="display:inline-block; padding:4px 16px; border-radius:30px; font-size:12px; font-weight:700; letter-spacing:1px; margin-bottom:12px; background:rgba(255,255,255,0.3); color:#fff; border:1px solid rgba(255,255,255,0.4);">🎉 Birthday</div>
              <h1 style="color:#fff; font-size:36px; font-weight:900; letter-spacing:-1px; margin:16px 0 8px;">
                Happy Birthday!
              </h1>
              <p style="color:rgba(255,255,255,0.9); font-size:18px; font-weight:500;">
                Wishing you a wonderful day, <strong>{{ $recipientName }}</strong>!
              </p>

            @else
              {{-- Anniversary decorations --}}
              <div style="font-size:70px; line-height:1; margin-bottom:16px;">🏆</div>
              <div style="display:inline-block; padding:4px 16px; border-radius:30px; font-size:12px; font-weight:700; letter-spacing:1px; margin-bottom:12px; background:rgba(255,255,255,0.3); color:#fff; border:1px solid rgba(255,255,255,0.4);">🎊 Work Anniversary</div>
              <h1 style="color:#fff; font-size:36px; font-weight:900; letter-spacing:-1px; margin:16px 0 8px;">
                Work Anniversary!
              </h1>
              <p style="color:rgba(255,255,255,0.9); font-size:18px; font-weight:500;">
                Celebrating your journey, <strong>{{ $recipientName }}</strong>!
              </p>
            @endif

          </td>
        </tr>

        {{-- ══ YEARS BADGE (anniversary only) ══ --}}
        @if($eventType === 'anniversary' && $years)
        <tr class="mt-5">
          <td style="background:#fff; padding: 0 40px; text-align:center;">
            <div style="display:inline-block; margin-top:20px; background:linear-gradient(135deg,#018ac8,#0056b3); color:#fff; border-radius:50px; padding:10px 30px; font-size:15px; font-weight:700; letter-spacing:1px; box-shadow:0 4px 15px rgba(1,138,200,0.4);">
              🎖️ &nbsp; {{ $years }} Year{{ $years > 1 ? 's' : '' }} of Excellence
            </div>
          </td>
        </tr>
        @endif

        {{-- ══ GREETING BODY ══ --}}
        <tr>
          <td style="padding: 40px 40px 20px;">

            <p style="font-size:17px; color:#333; line-height:1.8; margin-bottom:20px;">
              Dear <strong style="color:{{ $eventType === 'birthday' ? '#f5576c' : '#018ac8' }};">{{ $recipientName }}</strong>,
            </p>

            @if($customMessage)
              {{-- Custom message from sender --}}
              <div style="background:{{ $eventType === 'birthday' ? '#fff5f7' : '#f0f8ff' }}; border-left: 4px solid {{ $eventType === 'birthday' ? '#f5576c' : '#018ac8' }}; border-radius:0 12px 12px 0; padding:20px 24px; margin-bottom:24px;">
                <p style="font-size:16px; color:#444; line-height:1.8; font-style:italic;">
                  "{{ $customMessage }}"
                </p>
              </div>
            @else
              @if($eventType === 'birthday')
                <p style="font-size:16px; color:#555; line-height:1.9; margin-bottom:16px;">
                  On this special day, we want you to know how much you are appreciated and valued.
                  Your hard work, dedication, and positive energy make our team a better place every single day.
                </p>
                <p style="font-size:16px; color:#555; line-height:1.9; margin-bottom:16px;">
                  May this birthday bring you immense joy, good health, and success in everything you do.
                  Here's to celebrating you today and every day! 🎉
                </p>
              @else
                <p style="font-size:16px; color:#555; line-height:1.9; margin-bottom:16px;">
                  Today marks a remarkable milestone in your professional journey — <strong>{{ $years }} year{{ $years > 1 ? 's' : '' }}</strong> with us!
                  Your commitment, expertise, and contributions have been invaluable to our team and organization.
                </p>
                <p style="font-size:16px; color:#555; line-height:1.9; margin-bottom:16px;">
                  We are truly grateful to have you as part of our team and look forward to many more years of
                  success, growth, and achievement together. Thank you for everything you bring to the table every day. 🌟
                </p>
              @endif
            @endif

            <p style="font-size:16px; color:#555; line-height:1.9; margin-bottom:8px;">
              With warm regards and best wishes,
            </p>

          </td>
        </tr>

        {{-- ══ SENDER CARD ══ --}}
        <tr>
          <td style="padding: 0 40px 40px;">
            <div style="background:{{ $eventType === 'birthday' ? 'linear-gradient(135deg,#fff5f7,#ffe0e8)' : 'linear-gradient(135deg,#f0f8ff,#dceeff)' }}; border-radius:16px; padding:20px 24px; display:flex; align-items:center;">
              <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td style="width:52px; vertical-align:middle;">
                    <div style="width:52px; height:52px; border-radius:50%; background:{{ $eventType === 'birthday' ? 'linear-gradient(135deg,#f093fb,#f5576c)' : 'linear-gradient(135deg,#018ac8,#0056b3)' }}; display:flex; align-items:center; justify-content:center; font-size:22px; text-align:center; line-height:52px;">
                      👤
                    </div>
                  </td>
                  <td style="padding-left:16px; vertical-align:middle;">
                    <div style="font-size:16px; font-weight:800; color:#222;">{{ $senderName }}</div>
                    @if($senderDesignation)
                      <div style="font-size:13px; color:{{ $eventType === 'birthday' ? '#f5576c' : '#018ac8' }}; font-weight:600;">{{ $senderDesignation }}</div>
                    @endif
                    <div style="font-size:12px; color:#888; margin-top:2px;">{{ $companyName }}</div>
                  </td>
                </tr>
              </table>
            </div>
          </td>
        </tr>

        {{-- ══ DECORATIVE DIVIDER ══ --}}
        <tr>
          <td style="padding: 0 40px;">
            <div style="height:2px; background:{{ $eventType === 'birthday' ? 'linear-gradient(90deg,#f093fb,#f5576c,transparent)' : 'linear-gradient(90deg,#018ac8,#0056b3,transparent)' }}; border-radius:2px;"></div>
          </td>
        </tr>

        {{-- ══ FOOTER ══ --}}
        <tr>
          <td style="padding: 24px 40px 32px; text-align:center;">
            <p style="font-size:13px; color:#aaa; line-height:1.6;">
              This email was sent via the <strong style="color:{{ $eventType === 'birthday' ? '#f5576c' : '#018ac8' }};">{{ $companyName }} HR Portal</strong>.<br>
              @if($eventDate)
                {{ \Carbon\Carbon::parse($eventDate)->format('l, F j, Y') }}
              @endif
            </p>
            <div style="margin-top:16px; font-size:22px;">
              @if($eventType === 'birthday') 🎂 🎈 🎉 @else 🏆 ⭐ 🌟 @endif
            </div>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
