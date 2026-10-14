@php
    $kopSetting = $setting ?? \App\Models\Setting::getSettings();
    $kopLogoPath = $kopSetting->getLogoPath();
    $rawName = $kopSetting->company_name ?: 'IBU NARDI';
    if (trim(strtoupper($rawName)) === 'N7 DECORATION & CATERING BU NARDI' || trim(strtoupper($rawName)) === 'IBU NARDI') {
        $kopCompanyName = 'IBU NARDI';
        $kopCompanySub = 'Catering & N7 Decoration';
    } else {
        $kopCompanyName = $rawName;
        $kopCompanySub = $subTitle ?? 'Catering & N7 Decoration';
    }
    $kopAddress = $kopSetting->company_address ?: 'Jl. Patrakomala No.7, Jayaraga, Tarogong Kidul, Garut';
    $kopPhone = $kopSetting->company_phone ?: '0852-8844-2884';
    $kopBorderColor = $dividerColor ?? '#1e293b';
@endphp

<table class="kop-header-table" style="width: 100%; border-collapse: collapse; margin-bottom: 0;">
    <tr>
        <td class="kop-logo-td" style="width: 65px; vertical-align: middle; padding: 0 14px 0 0; border: none;">
            @if($kopLogoPath && file_exists($kopLogoPath))
                <img src="{{ $kopLogoPath }}" alt="Logo" class="kop-logo-image" style="height: 60px; max-width: 65px; object-fit: contain; display: block;">
            @endif
        </td>
        <td class="kop-text-td" style="vertical-align: middle; padding: 0; border: none; text-align: left;">
            <div class="kop-brand-title" style="font-size: 16px; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.15; margin-bottom: 2px;">
                {{ $kopCompanyName }}
            </div>
            <div class="kop-brand-sub" style="font-size: 12px; font-weight: 700; color: #334155; line-height: 1.2; margin-bottom: 3px;">
                {{ $kopCompanySub }}
            </div>
            <div class="kop-address-line" style="font-size: 8.5px; color: #475569; line-height: 1.35;">
                Alamat : {{ $kopAddress }}
            </div>
            <div class="kop-contact-line" style="font-size: 8.5px; color: #475569; line-height: 1.35;">
                WhatsApp : {{ $kopPhone }} &ndash; Instagram &amp; TikTok : @n7cateringbunardi
            </div>
        </td>
    </tr>
</table>
<div class="kop-divider-bar" style="border-bottom: 2px solid {{ $kopBorderColor }}; margin-top: 8px; margin-bottom: 12px; clear: both;"></div>
