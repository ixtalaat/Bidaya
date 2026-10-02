<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('certificates.title') }} — {{ $certificate->number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Marhey:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Cairo', sans-serif;
            background: #f1f5fd;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px;
        }
        .toolbar {
            margin-bottom: 16px;
            display: flex;
            gap: 12px;
        }
        .toolbar a,
        .toolbar button {
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            padding: 10px 22px;
            border-radius: 12px;
            border: 2px solid #0f172a;
            cursor: pointer;
            text-decoration: none;
        }
        .toolbar .print {
            background: #f59e0b;
            color: #0f172a;
            box-shadow: 3px 3px 0 #0f172a;
        }
        .toolbar .back {
            background: #ffffff;
            color: #0f172a;
            box-shadow: 3px 3px 0 #0f172a;
        }
        .sheet {
            background: #ffffff;
            border: 4px solid #0f172a;
            border-radius: 24px;
            width: 100%;
            max-width: 960px;
            padding: 10px;
        }
        .frame {
            border: 2px solid #2563eb;
            border-radius: 16px;
            padding: 40px 48px;
            text-align: center;
            position: relative;
        }
        .logo {
            font-family: 'Marhey', 'Cairo', sans-serif;
            font-size: 30px;
            font-weight: 700;
            color: #0f172a;
        }
        .tagline {
            font-size: 12px;
            letter-spacing: 2px;
            color: #64748b;
            margin-top: 2px;
        }
        h1 {
            font-family: 'Marhey', 'Cairo', sans-serif;
            font-size: 40px;
            color: #0f172a;
            margin: 18px 0 4px;
        }
        .lede {
            color: #475569;
            font-size: 15px;
        }
        .student {
            font-family: 'Marhey', 'Cairo', sans-serif;
            font-size: 44px;
            font-weight: 700;
            color: #2563eb;
            margin: 10px 0;
        }
        .course {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
        }
        .meta {
            display: flex;
            justify-content: center;
            gap: 48px;
            margin-top: 26px;
            font-size: 13px;
            color: #475569;
        }
        .meta strong {
            display: block;
            color: #0f172a;
            font-size: 15px;
            margin-top: 2px;
        }
        .seal {
            width: 92px;
            height: 92px;
            border-radius: 9999px;
            background: #f59e0b;
            border: 3px solid #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Marhey', 'Cairo', sans-serif;
            font-weight: 700;
            font-size: 15px;
            color: #0f172a;
            position: absolute;
            inset-inline-end: 40px;
            bottom: 36px;
            transform: rotate(-10deg);
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .toolbar {
                display: none;
            }
            .sheet {
                border-width: 3px;
                max-width: none;
            }
        }
    </style>
</head>
<body>
<div class="toolbar">
    <button type="button" class="print" onclick="window.print()">{{ __('certificates.print_button') }}</button>
    <a class="back" href="{{ route('enrollments.show', $certificate->enrollment) }}">{{ __('enrollments.show_title') }}</a>
</div>
<div class="sheet">
    <div class="frame">
        <p class="logo">بداية</p>
        <p class="tagline">BIDAYA • {{ __('layout.tagline') }}</p>
        <h1>{{ __('certificates.certificate_of_completion') }}</h1>
        <p class="lede">{{ __('certificates.presented_to') }}</p>
        <p class="student">{{ $certificate->enrollment->student->name }}</p>
        <p class="lede">{{ __('certificates.for_completing') }}</p>
        <p class="course">{{ $certificate->enrollment->group->course->name }} — {{ $certificate->enrollment->group->name }}</p>
        <div class="meta">
            <p>{{ __('certificates.certificate_no') }}<strong>{{ $certificate->number }}</strong></p>
            <p>{{ __('certificates.issued_on') }}<strong>{{ $certificate->issued_at->toDateString() }}</strong></p>
        </div>
        <div class="seal">★</div>
    </div>
</div>
</body>
</html>
